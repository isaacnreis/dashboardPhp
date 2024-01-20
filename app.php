<?php

class Dashboard {

  public $data_inicio;
  public $data_fim;
  public $numeroVendas;
  public $totalVendas;
  public $clientesAtivos;
  public $clientesInativos;
  public $totalReclamacoes;
  public $totalElogios;
  public $totalSugestoes;
  public $totalDespesas;

  public function __get($atributo) {
    return $this->$atributo;
  }

  public function __set($atributo, $valor) {
    $this->$atributo = $valor;
    return $this;
  }
}

class Conexao {

  private $host = 'localhost';
  private $dbname = 'dashboard';
  private $user = 'root';
  private $pass = '';

  public function conectar() {
    try {
      $conexao = new PDO(
        "mysql:host=$this->host;dbname=$this->dbname",
        "$this->user",
        "$this->pass"
      );

      $conexao->exec('set charset set utf8');

      return $conexao;

    } catch (PDOException $e) {
      echo '<p>'.$e->getMessage().'</p>';
    }
  }
}

class Db {
  private $conexao;
  private $dashboard;

  public function __construct(Conexao $conexao, Dashboard $dashboard)
  {
    $this->conexao = $conexao->conectar();
    $this->dashboard = $dashboard;
  }

  public function getNumeroVendas() {
    $query = '
      select
        count(*) as numero_vendas
      from
        tb_vendas
      where
        data_venda between :data_inicio and :data_fim;
    ';

    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
    $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
  }

  public function getTotalVendas() {
    $query = '
      select
        SUM(total) as total_vendas
      from
        tb_vendas
      where
        data_venda between :data_inicio and :data_fim;
    ';

    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
    $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
  }

  public function getClientesAtivos() {
    $query = '
      select
        SUM(cliente_ativo) as clientes_ativos
      from
        tb_clientes
    ';

    $stmt = $this->conexao->prepare($query);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->clientes_ativos;
  }

  public function getClientesInativos() {
    $query = '
      select
        count(cliente_ativo) as clientes_inativos
      from
        tb_clientes
      where
        cliente_ativo like :false
    ';

    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':false', '0');
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->clientes_inativos;
  }

  public function getTotalReclamacoes() {
    $query = '
      select
        count(tipo_contato) as total_reclamacoes
      from
        tb_contatos
      where
        tipo_contato like :IdReclamacoes
    ';

    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':IdReclamacoes', '1');
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->total_reclamacoes;
  }

  public function getTotalElogios() {
    $query = '
      select
        count(tipo_contato) as total_elogios
      from
        tb_contatos
      where
        tipo_contato like :IdElogios
    ';

    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':IdElogios', '2');
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->total_elogios;
  }

  public function getTotalSugestoes() {
    $query = '
      select
        count(tipo_contato) as total_sugestoes
      from
        tb_contatos
      where
        tipo_contato like :IdSugestoes
    ';

    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':IdSugestoes', '3');
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->total_sugestoes;
  }

  public function getTotalDespesas() {
    $query = '
      select
        SUM(total) as total_despesas
      from
        tb_despesas
      where
        data_despesa between :data_inicio and :data_fim;
    ';

    $stmt = $this->conexao->prepare($query);
    $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
    $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ)->total_despesas;
  }
}

$dashboard = new Dashboard();

$conexao = new Conexao();

$competencia = explode('-', $_GET['competencia']);
$ano = $competencia[0];
$mes = $competencia[1];
$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

$dashboard->__set('data_inicio', $ano.'-'.$mes.'-01');
$dashboard->__set('data_fim', $ano.'-'.$mes.'-'.$dias_do_mes);

$db = new Db($conexao, $dashboard);

$dashboard->__set('numeroVendas', $db->getNumeroVendas());
$dashboard->__set('totalVendas', $db->getTotalVendas());
$dashboard->__set('clientesAtivos', $db->getClientesAtivos());
$dashboard->__set('clientesInativos', $db->getClientesInativos());
$dashboard->__set('totalReclamacoes', $db->getTotalReclamacoes());
$dashboard->__set('totalElogios', $db->getTotalElogios());
$dashboard->__set('totalSugestoes', $db->getTotalSugestoes());
$dashboard->__set('totalDespesas', $db->getTotalDespesas());
echo json_encode($dashboard);

?>