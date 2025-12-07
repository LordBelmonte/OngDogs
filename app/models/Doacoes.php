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
        $sql = "INSERT INTO $tabela (id_usuario, valor, descricao, data_doacao) VALUES (:id_usuario, :valor, :descricao, :data_doacao)";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_usuario", $dados["id_usuario"]);
        $stm->bindValue(":valor", $dados["valor"] ?? null);
        $stm->bindValue(":descricao", $dados["descricao"] ?? null);
        $stm->bindValue(":data_doacao", $dados["data_doacao"] ?? null);
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            return [ 'erro' => false, 'mensagem' => 'Registro inserido com sucesso!', 'dados' => [] ];
        }
        return [ 'erro' => true, 'mensagem' => 'Erro ao inserir registro!', 'dados' => [] ];
    }

    public static function buscarDoacoesPeloId( $id_doacao ) {
        $tabela = "doacoes";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "SELECT * FROM $tabela WHERE id_doacao = :id_doacao";
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
        $sql = "SELECT * FROM $tabela";
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
        $sql = "UPDATE $tabela SET id_usuario = :id_usuario, valor = :valor, descricao = :descricao, data_doacao = :data_doacao WHERE id_doacao = :id_doacao";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_usuario", $dados["id_usuario"]);
        $stm->bindValue(":valor", $dados["valor"] ?? null);
        $stm->bindValue(":descricao", $dados["descricao"] ?? null);
        $stm->bindValue(":data_doacao", $dados["data_doacao"] ?? null);
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
        $sql = "DELETE FROM $tabela WHERE id_doacao = :id_doacao";
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
    
    // Adicionar dados obrigatórios que não vem do formulário
    $input['id_usuario'] = $_SESSION['user_id'] ?? 0;
    $input['data_doacao'] = date('Y-m-d H:i:s');
    
    $resultado = Doacoes::inserir($input);
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
    $sql = "SELECT * FROM doacoes WHERE id_usuario = :id_usuario ORDER BY data_doacao DESC";
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




