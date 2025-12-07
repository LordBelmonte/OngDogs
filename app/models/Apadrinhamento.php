<?php

// Linha 3: Define o caminho absoluto para a pasta raiz do projeto.
$rootPath = dirname(dirname(dirname(__FILE__)));

// Usa $rootPath para incluir o arquivo de configuração,
// garantindo que o separador de diretório seja o correto para o SO.
require_once $rootPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

class Apadrinhamento {
    public static function inserir( $dados ) {
        $tabela = "apadrinhamentos";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        // A tabela atual possui colunas: id_apadrinhamento, id_usuario, id_animal, data_adocao, status
        // Vai-se armazenar a contribuição em 'status' e preencher data_adocao com a data atual
        $sql = "INSERT INTO $tabela (id_usuario, id_animal, data_adocao, status) VALUES (:id_usuario, :id_animal, :data_adocao, :status)";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_usuario", $dados["id_usuario"]);
        $stm->bindValue(":id_animal", $dados["id_animal"]);
        $stm->bindValue(":data_adocao", $dados["data_adocao"] ?? date('Y-m-d'));
        $stm->bindValue(":status", $dados["valor_mensal"] ?? $dados["valor_contribuicao"] ?? null);
        $stm->execute();

        if ( $stm->rowCount() > 0 ) {
            return [ 'erro' => false, 'mensagem' => 'Registro inserido com sucesso!', 'dados' => [] ];
        }
        return [ 'erro' => true, 'mensagem' => 'Erro ao inserir registro!', 'dados' => [] ];
    }

    public static function buscarApadrinhamentoPeloId( $id ) {
        $tabela = "apadrinhamentos";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "SELECT * FROM $tabela WHERE id_apadrinhamento = :id_apadrinhamento";
        $stm = $conexao->prepare( $sql );
        $stm->bindValue(":id_apadrinhamento", $id );
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            $valores = $stm->fetch(PDO::FETCH_ASSOC);
            return [ 'erro' => false, 'mensagem' => "Registro encontrado!", 'dados' => $valores ];
        }
        return [ 'erro' => true, 'mensagem' => "Codigo não cadastrado!", 'dados' => [] ];
    }

    public static function buscarTodosApadrinhamento() {
        $tabela = "apadrinhamentos";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "SELECT id_apadrinhamento, id_usuario, id_animal, data_adocao, status FROM $tabela";
        $stm = $conexao->prepare($sql);
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            $valores = $stm->fetchAll(PDO::FETCH_ASSOC);
            // compatibilidade: expor 'valor_mensal' para frontend que espera esse campo
            foreach ($valores as &$v) {
                $v['valor_mensal'] = $v['status'] ?? null;
            }
            return [ 'erro' => false, 'mensagem' => 'Registros encontrados!', 'dados' => $valores ];
        }
        return [ 'erro' => true, 'mensagem' => 'Tabela está vazia!', 'dados' => [] ];
    }

    public static function alterar( $id, $dados ) {
        $tabela = "apadrinhamentos";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "UPDATE $tabela SET id_usuario = :id_usuario, id_animal = :id_animal, data_adocao = :data_adocao, status = :status WHERE id_apadrinhamento = :id_apadrinhamento";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_usuario", $dados["id_usuario"]);
        $stm->bindValue(":id_animal", $dados["id_animal"]);
        $stm->bindValue(":data_adocao", $dados["data_adocao"] ?? null);
        $stm->bindValue(":status", $dados["valor_mensal"] ?? ($dados["valor_contribuicao"] ?? null));
        $stm->bindValue(":id_apadrinhamento", $id);
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            return [ 'erro' => false, 'mensagem' => 'Dados alterados com sucesso!', 'dados' => [] ];
        }
        return [ 'erro' => true, 'mensagem' => 'Erro!', 'dados' => [] ];
    }

    public static function deletar( $id ) {
        $tabela = "apadrinhamentos";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "DELETE FROM $tabela WHERE id_apadrinhamento = :id_apadrinhamento";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_apadrinhamento", $id);
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            return [ 'erro' => false, 'mensagem' => 'Dados deletados com sucesso!', 'dados' => [] ];
        }
        return [ 'erro' => true, 'mensagem' => 'Erro!', 'dados' => [] ];
    }
}

// Processar POST requests se houver action=criar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'criar') {
    session_start();
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode(['erro' => true, 'mensagem' => 'Dados inválidos', 'dados' => []]);
        exit;
    }
    
    // Mapear valor_contribuicao para valor_mensal
    $input['id_usuario'] = $_SESSION['user_id'] ?? 0;
    $input['valor_mensal'] = $input['valor_contribuicao'] ?? 0;
    
    $resultado = Apadrinhamento::inserir($input);
    echo json_encode($resultado);
    exit;
}

// Processar GET requests para listar apadrinhamentos do usuário
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'listar') {
    session_start();
    header('Content-Type: application/json');
    
    $id_usuario = $_SESSION['user_id'] ?? 0;
    
    if (!$id_usuario) {
        echo json_encode(['erro' => true, 'mensagem' => 'Usuário não autenticado', 'dados' => []]);
        exit;
    }
    
    $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
    $sql = "SELECT id_apadrinhamento, id_usuario, id_animal, data_adocao, status FROM apadrinhamentos WHERE id_usuario = :id_usuario";
    $stm = $conexao->prepare($sql);
    $stm->bindValue(":id_usuario", $id_usuario);
    $stm->execute();

    if ($stm->rowCount() > 0) {
        $dados = $stm->fetchAll(PDO::FETCH_ASSOC);
        // expor valor_mensal para compatibilidade
        foreach ($dados as &$d) {
            $d['valor_mensal'] = $d['status'] ?? null;
        }
        echo json_encode(['erro' => false, 'mensagem' => 'Apadrinhamentos encontrados', 'dados' => $dados]);
    } else {
        echo json_encode(['erro' => true, 'mensagem' => 'Nenhum apadrinhamento encontrado', 'dados' => []]);
    }
    exit;
}

// Listar todos apadrinhamentos (admin)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'listar_todos') {
    header('Content-Type: application/json');
    $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
    $sql = "SELECT id_apadrinhamento, id_usuario, id_animal, data_adocao, status FROM apadrinhamentos ORDER BY id_apadrinhamento DESC";
    $stm = $conexao->prepare($sql);
    $stm->execute();
    if ($stm->rowCount() > 0) {
        $dados = $stm->fetchAll(PDO::FETCH_ASSOC);
        foreach ($dados as &$d) { $d['valor_mensal'] = $d['status'] ?? null; }
        echo json_encode(['erro' => false, 'mensagem' => 'Apadrinhamentos encontrados', 'dados' => $dados]);
    } else {
        echo json_encode(['erro' => true, 'mensagem' => 'Nenhum apadrinhamento encontrado', 'dados' => []]);
    }
    exit;
}

// Alterar apadrinhamento (admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'alterar') {
    header('Content-Type: application/json');
    $id = $_GET['id'] ?? 0;
    if (!$id) { echo json_encode(['erro'=>true,'mensagem'=>'ID inválido','dados'=>[]]); exit; }
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) { echo json_encode(['erro'=>true,'mensagem'=>'Dados inválidos','dados'=>[]]); exit; }
    $dados = [];
    $dados['id_usuario'] = $input['id_usuario'] ?? 0;
    $dados['id_animal'] = $input['id_animal'] ?? null;
    $dados['valor_mensal'] = $input['valor_mensal'] ?? ($input['valor_contribuicao'] ?? null);
    $resultado = Apadrinhamento::alterar($id, $dados);
    echo json_encode($resultado);
    exit;
}

// Processar DELETE requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'deletar') {
    session_start();
    header('Content-Type: application/json');
    
    $id = $_GET['id'] ?? 0;
    $id_usuario = $_SESSION['user_id'] ?? 0;
    
    if (!$id || !$id_usuario) {
        echo json_encode(['erro' => true, 'mensagem' => 'Dados inválidos', 'dados' => []]);
        exit;
    }
    
    $resultado = Apadrinhamento::deletar($id);
    echo json_encode($resultado);
    exit;
}

?>


