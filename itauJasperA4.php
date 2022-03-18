<?php
// para rodar este exemplo é necessario adicionar no seu composer
// "quilhasoft/jasperphp":"dev-master"
// "openboleto/openboleto":"dev-master"

date_default_timezone_set("America/Sao_Paulo");

//require '../autoloader.php';
//require '../../../rctnet/JasperPHP/autoloader.php';
require '../../autoload.php'; // necessario rodar o autoad principal do seu composer para pegar o openboleto, e JasperPHP

use OpenBoleto\Agente;
use JasperPHP\Report;
use OpenBoleto\AgenteFaturamento;
use OpenBoleto\AgentePrePagamento;
use OpenBoleto\Banco\Unicred;

//use JasperPHP\ado\TTransaction;
//use JasperPHP\ado\TLoggerHTML;

class Boleto
{
    /* Variavel que armazenara os dados do boleto 
    / @var array();
    */
    private $data = array();
    /*
    * método __set()
    * executado sempre que uma propriedade for atribuída.
    */
    public function __set($prop, $value)
    {
        // verifica se existe método set_<propriedade>
        if (method_exists($this, 'set_' . $prop)) {
            // executa o método set_<propriedade>
            call_user_func(array($this, 'set_' . $prop), $value);
        } else {
            if ($value === NULL) {
                unset($this->data[$prop]);
            } else {
                // atribui o valor da propriedade
                $this->data[$prop] = $value;
            }
        }
    }
    /*
    * método __get()
    * executado sempre que uma propriedade for requerida
    */
    public function __get($prop)
    {
        // verifica se existe método get_<propriedade>
        if (method_exists($this, 'get_' . $prop)) {
            // executa o método get_<propriedade>
            return call_user_func(array($this, 'get_' . $prop));
        } else {
            // retorna o valor da propriedade
            if (isset($this->data[$prop])) {
                return ($this->data[$prop]);
            }
        }
    }

    public function __construct($sequencial = null)
    {
        //
        // aqui voce pode acessar sua base de dados e coletar os dados do boleto e preencher os campos abaixo
        //

        $sacado = new Agente('RONALDO GONCALVES', '471.503.006-34', '58', 'AV 21 DE NOVEMBRO, 829 VILA ISABEL', '37.505-185', 'ITAJUBA', 'MG');
        $cedente = new Agente('Unimed Itajubá Cooperativa de Trabalho Médico', '23.802.218/0001-65', 'Avenida Cesário Alvim, 382 Centro', '37.501-059', 'Itajubá', 'Minas Gerais');

        $boleto = new Unicred(array(
            // Parâmetros obrigatórios
            'dataVencimento' => new DateTime('2022-02-06'),
            'valor' => 1313.13,
            'sequencial' => $sequencial, // 8 dígitos
            'sacado' => $sacado,
            'cedente' => $cedente,
            'prePagamento1' => new AgentePrePagamento('ada', '58', '82394', '48329', '58239', '44829', '4234', 'teste'),
            'prePagamento2' => new AgentePrePagamento(),
            'faturamento1' => new AgenteFaturamento('Remocao Aerea (C)', '1,0000', 'R$' . '3,54', '3,54'),
            'faturamento2' => new AgenteFaturamento('Mensalidade', '1,0000', 'R$' . '1309,59', '1309,59'),
            'faturamento3' => new AgenteFaturamento(),
            'agencia' => 5691, // 4 dígitos
            'carteira' => 21, // 3 dígitos
            'conta' => 2779, // 5 dígitos

            // // Parâmetro obrigatório somente se a carteira for
            // // 107, 122, 142, 143, 196 ou 198
            'codigoCliente' => 2779080, // 5 dígitos
            'numeroDocumento' => 76784399, // 7 dígitos

            // Parâmetros recomendáveis
            // 'logoPath' => '/home/webdev/development/docker/docker-protocolo-dev-itajuba/www/vendor/openboleto/openboleto/resources/images/logo-empresa.jpg',
            'contaDv' => 0,
            'agenciaDv' => 0,
            'descricaoDemonstrativo' => array( // Até 5
                'Compra de materiais cosméticos',
                'Compra de alicate',
            ),
            'instrucoes' => array( // Até 8
                'APÓS O 3º DIA DE VENCIMENTO COBRAR MULTA DE 2%',
                'APÓS O VENCIMENTO COBRAR JUROS DE 0,17% AO DIA',
                'NÃO PODE SER PAGO COM CHEQUE',
            ),
            'instrucoes2' => array( // Até 8
                'APÓS O 3º DIA DE VENCIMENTO COBRAR MULTA DE 2%',
                'APÓS O VENCIMENTO COBRAR JUROS DE 0,17% AO DIA',
                'Contrato: 137010600535300 Competência: 2022/02',
                '',
                'NÃO PODE SER PAGO COM CHEQUE'
            ),

            // Parâmetros opcionais
            //'resourcePath' => '../resources',
            'moeda' => Unicred::MOEDA_REAL,
            'dataDocumento' => new DateTime('2022-01-14'),
            'dataProcessamento' => new DateTime('2022-01-14'),
            //'contraApresentacao' => true,
            //'pagamentoMinimo' => 23.00,
            'aceite' => 'N',
            'especieDoc' => 'DM',
            //'usoBanco' => 'Uso banco',
            //'layout' => 'layout.phtml',
            //'logoPath' => 'http://boletophp.com.br/img/opensource-55x48-t.png',
            //'sacadorAvalista' => new Agente('Antônio da Silva', '02.123.123/0001-11'),
            'descontosAbatimentos' => 0.00,
            'moraMulta' => '0.00',
            'outrasDeducoes' => 0.00,
            'outrosAcrescimos' => 0.00,
            'valorCobrado' => 1313.13,
            //'valorUnitario' => 123.12,
            'quantidade' => 1,
        ));
        $boleto->getOutput();
        $this->data = array_merge($this->data, $boleto->getData());
    }

    /* método para interceptar  a requisição e adicionar o codigo html necessario para correta exibição do demostrativo    */
    public function get_demonstrativo()
    {
        return '<table>
        <tr>

        <td>' . implode('<br>', $this->data['demonstrativo']) .
            '</td>
        </tr>
        <table>';
    }

    /* método para interceptar  a requisição e adicionar o codigo html necessario para correta exibição das instrucoes    */
    public function get_instrucoes()
    {
        return implode('
', $this->data['instrucoes']);
        // return '<table>
        // <tr>

        // <td>' . implode('<br>', $this->data['instrucoes']) . '
        // </td>
        // </tr>
        // <table>';
    }
    /* método para interceptar  a requisição e adicionar o codigo html necessario para correta exibição das instrucoes    */
    public function get_instrucoes2()
    {
        return implode('
', $this->data['instrucoes2']);
    }

    /* este metodo esta aqui para manter compatibilidade do jxml criado para o meu sistema*/
    public function get_carteiras_nome()
    {
        return $this->data['carteira'];
    }
}
// altere aqui para o nome do arquivo de configuração no diretorio config desativado mas pode ser usado por usuarios avançados
//JasperPHP\ado\TTransaction::open('dev'); 

// instancição do objeto :1 parametro: caminho do layout do boleto , 2 parametro :  array com os parametros para consulta no banco para localizar o boleto
// pode ser passado como paramtro um array com os numeros dos boletos que serão impressos desde que criado sql dentro do arquivo jrxml(desativado nesse exemplo)

//$report =new JasperPHP\Report("bol01Files/boletoCarne.jrxml",array());
$report = new Report("bol01Files/boletoA4.jrxml", array());

JasperPHP\Instructions::prepare($report);    // prepara o relatorio lendo o arquivo
$report->dbData = array(new Boleto(8240260225)); // aqui voce pode construir seu array de boletos em qualquer estrutura incluindo 
$report->generate(array());                // gera o relatorio

$report->out();                     // gera o pdf
$pdf  = JasperPHP\PdfProcessor::get();       // extrai o objeto pdf de dentro do report
$pdf->Output('boleto.pdf');  // metodo do TCPF para gerar saida para o browser
