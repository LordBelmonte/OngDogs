<?php

// Linha 3: Define o caminho absoluto para a pasta raiz do projeto.
$rootPath = dirname(dirname(dirname(__FILE__)));

// Usa $rootPath para incluir o arquivo de configuração,
// garantindo que o separador de diretório seja o correto para o SO.
require_once $rootPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

class Doacoes {
    public static function inserir( $dados ) {
        $tabela = "doacoes";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        // Ajuste: tabela real tem colunas (id_doacoes, id_usuario, valor, data_doacao, forma_paga, status)
        $sql = "INSERT INTO $tabela (id_usuario, valor, data_doacao, forma_paga, status) VALUES (:id_usuario, :valor, :data_doacao, :forma_paga, :status)";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_usuario", $dados["id_usuario"]);
        $stm->bindValue(":valor", $dados["valor"] ?? null);
        $stm->bindValue(":data_doacao", $dados["data_doacao"] ?? null);
        $stm->bindValue(":forma_paga", $dados["forma_paga"] ?? null);
        $stm->bindValue(":status", $dados["status"] ?? null);
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            // retornar id inserido usando lastInsertId
            $id = $conexao->lastInsertId();
            return [ 'erro' => false, 'mensagem' => 'Registro inserido com sucesso!', 'dados' => ['id_doacoes' => $id] ];
        }
        return [ 'erro' => true, 'mensagem' => 'Erro ao inserir registro!', 'dados' => [] ];
    }

    public static function buscarDoacoesPeloId( $id_doacao ) {
        $tabela = "doacoes";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        // retornar colunas reais (id_doacoes é a PK)
        $sql = "SELECT id_doacoes, id_usuario, valor, data_doacao, forma_paga, status FROM $tabela WHERE id_doacoes = :id_doacao";
        $stm = $conexao->prepare( $sql );
        $stm->bindValue(":id_doacao", $id_doacao );
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            $valores = $stm->fetch(PDO::FETCH_ASSOC);
            return [ 'erro' => false, 'mensagem' => "Registro encontrado!", 'dados' => $valores ];
        }
        return [ 'erro' => true, 'mensagem' => "Codigo não cadastrado!", 'dados' => [] ];
    }

    public static function buscarTodosDoacoes() {
        $tabela = "doacoes";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "SELECT id_doacoes, id_usuario, valor, data_doacao, forma_paga, status FROM $tabela";
        $stm = $conexao->prepare($sql);
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            $valores = $stm->fetchAll(PDO::FETCH_ASSOC);
            return [ 'erro' => false, 'mensagem' => 'Registros encontrados!', 'dados' => $valores ];
        }
        return [ 'erro' => true, 'mensagem' => 'Tabela está vazia!', 'dados' => [] ];
    }

    public static function alterar( $id_doacao, $dados ) {
        $tabela = "doacoes";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "UPDATE $tabela SET id_usuario = :id_usuario, valor = :valor, data_doacao = :data_doacao, forma_paga = :forma_paga, status = :status WHERE id_doacoes = :id_doacao";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_usuario", $dados["id_usuario"] ?? null);
        $stm->bindValue(":valor", $dados["valor"] ?? null);
        $stm->bindValue(":data_doacao", $dados["data_doacao"] ?? null);
        $stm->bindValue(":forma_paga", $dados["forma_paga"] ?? null);
        $stm->bindValue(":status", $dados["status"] ?? null);
        $stm->bindValue(":id_doacao", $id_doacao);
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            return [ 'erro' => false, 'mensagem' => 'Dados alterados com sucesso!', 'dados' => [] ];
        }
        return [ 'erro' => true, 'mensagem' => 'Erro!', 'dados' => [] ];
    }

    public static function deletar( $id_doacao ) {
        $tabela = "doacoes";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "DELETE FROM $tabela WHERE id_doacoes = :id_doacao";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_doacao", $id_doacao);
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

    // Mapear campos para a estrutura real da tabela
    $payload = [];
    $payload['id_usuario'] = $_SESSION['user_id'] ?? 0;
    $payload['valor'] = $input['valor'] ?? null;
    $payload['forma_paga'] = $input['forma_paga'] ?? null;
    $payload['status'] = $input['status'] ?? null;
    $payload['data_doacao'] = date('Y-m-d H:i:s');

    $resultado = Doacoes::inserir($payload);
    echo json_encode($resultado);
    exit;
}

// Processar GET requests para listar doações do usuário
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'listar') {
    session_start();
    header('Content-Type: application/json');
    
    $id_usuario = $_SESSION['user_id'] ?? 0;
    
    if (!$id_usuario) {
        echo json_encode(['erro' => true, 'mensagem' => 'Usuário não autenticado', 'dados' => []]);
        exit;
    }
    
    $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
    $sql = "SELECT id_doacoes, id_usuario, valor, data_doacao, forma_paga, status FROM doacoes WHERE id_usuario = :id_usuario ORDER BY data_doacao DESC";
    $stm = $conexao->prepare($sql);
    $stm->bindValue(":id_usuario", $id_usuario);
    $stm->execute();
    
    if ($stm->rowCount() > 0) {
        $dados = $stm->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['erro' => false, 'mensagem' => 'Doações encontradas', 'dados' => $dados]);
    } else {
        echo json_encode(['erro' => true, 'mensagem' => 'Nenhuma doação encontrada', 'dados' => []]);
    }
    exit;
}

// Processar GET requests para listar todas as doações (admin)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'listar_todos') {
    header('Content-Type: application/json');
    $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
    $sql = "SELECT id_doacoes, id_usuario, valor, data_doacao, forma_paga, status FROM doacoes ORDER BY data_doacao DESC";
    $stm = $conexao->prepare($sql);
    $stm->execute();
    if ($stm->rowCount() > 0) {
        $dados = $stm->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['erro' => false, 'mensagem' => 'Doações encontradas', 'dados' => $dados]);
    } else {
        echo json_encode(['erro' => true, 'mensagem' => 'Nenhuma doação encontrada', 'dados' => []]);
    }
    exit;
}

// Processar POST requests para alterar doação (admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'alterar') {
    header('Content-Type: application/json');
    $id = $_GET['id'] ?? 0;
    if (!$id) { echo json_encode(['erro'=>true,'mensagem'=>'ID inválido','dados'=>[]]); exit; }
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) { echo json_encode(['erro'=>true,'mensagem'=>'Dados inválidos','dados'=>[]]); exit; }
    $dados = [];
    $dados['id_usuario'] = $input['id_usuario'] ?? ($input['user_id'] ?? 0);
    $dados['valor'] = $input['valor'] ?? null;
    $dados['forma_paga'] = $input['forma_paga'] ?? null;
    $dados['status'] = $input['status'] ?? null;
    $dados['data_doacao'] = $input['data_doacao'] ?? null;
    $resultado = Doacoes::alterar($id, $dados);
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
    
    $resultado = Doacoes::deletar($id);
    echo json_encode($resultado);
    exit;
}

?>




