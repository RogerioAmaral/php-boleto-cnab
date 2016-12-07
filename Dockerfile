################################################################################
# Base image
################################################################################

FROM nginx

################################################################################
# Build instructions
################################################################################

# Remove default nginx configs.
RUN rm -f /etc/nginx/conf.d/*

# Install packages
RUN apt-get update && apt-get install -my \
  supervisor \
  curl \
  wget \
  php5-curl \
  php5-fpm \
  php5-gd \
  php5-memcached \
  php5-mysql \
  php5-mcrypt \
  php5-sqlite \
  php5-xdebug \
  php-apc

# Ensure that PHP5 FPM is run as root.
RUN sed -i "s/user = www-data/user = root/" /etc/php5/fpm/pool.d/www.conf
RUN sed -i "s/group = www-data/group = root/" /etc/php5/fpm/pool.d/www.conf

# Pass all docker environment
RUN sed -i '/^;clear_env = no/s/^;//' /etc/php5/fpm/pool.d/www.conf

# Get access to FPM-ping page /ping
RUN sed -i '/^;ping\.path/s/^;//' /etc/php5/fpm/pool.d/www.conf
# Get access to FPM_Status page /status
RUN sed -i '/^;pm\.status_path/s/^;//' /etc/php5/fpm/pool.d/www.conf

# Prevent PHP Warning: 'xdebug' already loaded.
# XDebug loaded with the core
RUN sed -i '/.*xdebug.so$/s/^/;/' /etc/php5/mods-available/xdebug.ini

# Install HHVM
RUN apt-key adv --recv-keys --keyserver hkp://keyserver.ubuntu.com:80 0x5a16e7281be7a449
RUN echo deb http://dl.hhvm.com/debian jessie main | tee /etc/apt/sources.list.d/hhvm.list
RUN apt-get update -qq
RUN apt-get upgrade -y -qq
RUN apt-get install -y -qq nano curl sudo hhvm-dev git-core automake autoconf libtool gcc

# install libbson
RUN git clone git://github.com/mongodb/libbson.git /tmp/libbson
WORKDIR /tmp/libbson
RUN ./autogen.sh
RUN make && sudo make install

# install mongofill-hhvm
RUN git clone https://github.com/mongofill/mongofill-hhvm /tmp/mongofill-hhvm
WORKDIR /tmp/mongofill-hhvm
RUN ./build.sh

RUN mkdir -p /session

# install extension
RUN mkdir -p /etc/hhvm/extensions
RUN cp /tmp/mongofill-hhvm/mongo.so /etc/hhvm/extensions/mongo.so
RUN echo 'hhvm.dynamic_extension_path = /etc/hhvm/extensions' >> /etc/hhvm/php.ini
RUN echo 'hhvm.dynamic_extensions[mongo] = mongo.so' >> /etc/hhvm/php.ini
RUN echo 'hhvm.dynamic_extension_path = /etc/hhvm/extensions' >> /etc/hhvm/server.ini
RUN echo 'hhvm.dynamic_extensions[mongo] = mongo.so' >> /etc/hhvm/server.ini

# clean up
RUN rm -Rf /tmp/libbson
RUN rm -Rf /tmp/mongofill-hhvm
RUN rm -f /tmp/version.h

# Add configuration files
COPY conf/nginx.conf /etc/nginx/
COPY conf/supervisord.conf /etc/supervisor/conf.d/
COPY conf/php.ini /etc/php5/fpm/conf.d/40-custom.ini

################################################################################
# Volumes
################################################################################

VOLUME ["/var/www", "/etc/nginx/conf.d", "/session"]

################################################################################
# Ports
################################################################################

EXPOSE 80 443 9000

################################################################################
# Entrypoint
################################################################################

ENTRYPOINT ["/usr/bin/supervisord"]