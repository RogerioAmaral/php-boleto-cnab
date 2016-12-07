<?php

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;
use \BoletoPHP\Boletos\Boleto;
use \BoletoPHP\Consts\EspecieDoc,
    \BoletoPHP\Consts\Carteira,
    \BoletoPHP\Consts\Aceite;
use \BoletoPHP\Types\Pagador;
use \BoletoPHP\Types\Beneficiario;

final class BoletoController extends BaseController
{

    function geraItau($dados)
    {
        echo "TODO";
    }

    function geraCaixa($dados)
    {

        $dias_de_prazo_para_pagamento = 5;
        $taxa_boleto                  = 2.95;
        $valor_cobrado                = "2950,00"; // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
        $valor_cobrado                = str_replace(",", ".", $valor_cobrado);
        $valor_boleto                 = number_format($valor_cobrado + $taxa_boleto,
            2, ',', '');
        $beneficiario                 = new Beneficiario();
        $beneficiario->hydrate([
            'cedente' => 'Coloque a Razão Social da sua empresa aqui',
            'cidade_uf' => 'Cidade / Estado',
            'cpf_cnpj' => '0212164-545/0000',
            'endereco' => 'Coloque o endereço da sua empresa aqui',
            'agencia' => 1234,
            'conta' => 123,
            'conta_dv' => 0,
            'conta_cedente' => 123456
        ]);
        $pagador                      = new Pagador();
        $pagador->hydrate([
            'endereco1' => 'Endereço do seu Cliente',
            'endereco2' => 'Cidade - Estado -  CEP: 00000-000',
            'pagador_nome' => 'Luiz Fernando Popota',
            'pagador_cpf_cpnpj' => '265.857.562-90'
        ]);
        $params                       = array(
            'data_vencimento' => date("d/m/Y",
                time() + ($dias_de_prazo_para_pagamento * 86400)),
            'valor_boleto' => number_format($valor_cobrado + $taxa_boleto, 2,
                ',', ''),
            'agencia' => 1565,
            'conta' => 13877,
            'conta_dv' => 4,
            'carteira' => "SR",
            'conta_cedente' => 87000000414,
            'conta_cedente_dv' => 3,
            'inicio_nosso_numero' => 80,
            'nosso_numero' => 19525086,
            'identificacao' => 'BoletoPhp - Código Aberto de Sistema de Boletos',
            'cpf_cnpj' => '',
            'endereco' => 'Coloque o endereço da sua empresa aqui',
            'cidade_uf' => 'Cidade / Estado',
            'cedente' => 'Coloque a Razão Social da sua empresa aqui',
            'especie' => 'R$',
            'quantidade' => '',
            'numero_documento' => '27.030195.10',
            'sacado' => 'Nome do seu Cliente',
            'demonstrativo1' => 'Pagamento de Compra na Loja Nonononono',
            'demonstrativo2' => 'Mensalidade referente a nonon nonooon nononon<br>Taxa bancária - R$ '.number_format($taxa_boleto,
                2, ',', ''),
            'demonstrativo3' => "BoletoPhp - http://www.boletophp.com.br",
            'data_documento' => date("d/m/Y"),
            'especie_doc' => '',
            'aceite' => '',
            'data_processamento' => date("d/m/Y"),
            'carteira_descricao' => 'SR',
            'valor_unitario' => '',
            'instrucoes1' => '- Sr. Caixa, cobrar multa de 2% após o vencimento',
            'instrucoes2' => '- Receber até 10 dias após o vencimento',
            'instrucoes3' => '- Em caso de dúvidas entre em contato conosco: xxxx@xxxx.com.br',
            'instrucoes4' => '&nbsp; Emitido pelo sistema Projeto BoletoPhp - www.boletophp.com.br',
            'endereco1' => 'Endereço do seu Cliente',
            'endereco2' => 'Cidade - Estado -  CEP: 00000-000',
            'pagador_nome' => 'Luiz Fernando Popota',
            'pagador_cpf_cpnpj' => '265.857.562-90'
        );
        $boleto                       = new \BoletoPHP\Boletos\CaixaEconomicaFederal($params,
            $pagador, $beneficiario);
        echo $boleto->gerarBoleto();
    }

    function geraBB($dados)
    {
        echo "TODO";
    }

    /**
     * Todo - Santander
     * @param type $dados
     */
    function geraSantander($dados)
    {
        echo "TODO";
    }

    /**
     * Todo - Bradesco
     * @param type $dados
     */
    function geraBradesco($dados)
    {
        echo "TODO";
    }

    function geraSicoob($dados)
    {
        echo "TODO";
    }
}