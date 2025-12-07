<?php

// Linha 3: Define o caminho absoluto para a pasta raiz do projeto.
$rootPath = dirname(dirname(dirname(__FILE__)));

// Usa $rootPath para incluir o arquivo de configuração,
// garantindo que o separador de diretório seja o correto para o SO.
require_once $rootPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

class Adocoes {
    public static function inserir( $dados ) {
        $tabela = "adocoes";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );

        $sql = "INSERT INTO $tabela (id_usuario, id_animal, data_adocao) VALUES (:id_usuario , :id_animal, :data_adocao)";

        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_usuario", $dados["id_usuario"]);
        $stm->bindValue(":id_animal", $dados["id_animal"]);
        $stm->bindValue(":data_adocao", $dados["data_adocao"] ?? null);

        $stm->execute();

        if ( $stm->rowCount() > 0 ) {
            return [ 'erro' => false, 'mensagem' => 'Registro inserido com sucesso!', 'dados' => [] ];
        } else {
            return [ 'erro' => true, 'mensagem' => 'Erro ao inserir registro!', 'dados' => [] ];
        }
    }

    public static function buscarAdocoesPeloId( $id_adocao ) {
        $tabela = "adocoes";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );

        $sql = "SELECT * FROM $tabela WHERE id_adocao = :id_adocao";
        $stm = $conexao->prepare( $sql );
        $stm->bindValue(":id_adocao", $id_adocao );
        $stm->execute();

        if ( $stm->rowCount() > 0 ) {
            $valores = $stm->fetch(PDO::FETCH_ASSOC);
            return [ 'erro' => false, 'mensagem' => "Registro encontrado!", 'dados' => $valores ];
        } else {
            return [ 'erro' => true, 'mensagem' => "Codigo não cadastrado!", 'dados' => [] ];
        }
    }

    public static function buscarTodosAdocoes() {
        $tabela = "adocoes";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );

        $sql = "SELECT * FROM $tabela";
        $stm = $conexao->prepare($sql);
        $stm->execute();

        if ( $stm->rowCount() > 0 ) {
            $valores = $stm->fetchAll(PDO::FETCH_ASSOC);
            return [ 'erro' => false, 'mensagem' => 'Registros encontrados!', 'dados' => $valores ];
        } else {
            return [ 'erro' => true, 'mensagem' => 'Tabela está vazia!', 'dados' => [] ];
        }
    }

    public static function alterar( $id_adocao, $dados ) {
        $tabela = "adocoes";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );

        $sql = "UPDATE $tabela SET id_usuario = :id_usuario, id_animal = :id_animal, data_adocao = :data_adocao WHERE id_adocao = :id_adocao";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_usuario", $dados["id_usuario"]);
        $stm->bindValue(":id_animal", $dados["id_animal"]);
        $stm->bindValue(":data_adocao", $dados["data_adocao"] ?? null);
        $stm->bindValue(":id_adocao", $id_adocao);
        $stm->execute();

        if ( $stm->rowCount() > 0 ) {
            return [ 'erro' => false, 'mensagem' => 'Dados alterados com sucesso!', 'dados' => [] ];
        } else {
            return [ 'erro' => true, 'mensagem' => 'Erro!', 'dados' => [] ];
        }
    }

    public static function deletar( $id_adocao ) {
        $tabela = "adocoes";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );

        $sql = "DELETE FROM $tabela WHERE id_adocao = :id_adocao";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_adocao", $id_adocao);
        $stm->execute();

        if ( $stm->rowCount() > 0 ) {
            return [ 'erro' => false, 'mensagem' => 'Dados deletados com sucesso!', 'dados' => [] ];
        } else {
            return [ 'erro' => true, 'mensagem' => 'Erro!', 'dados' => [] ];
        }
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
    $input['data_adocao'] = date('Y-m-d H:i:s');
    
    $resultado = Adocoes::inserir($input);
    echo json_encode($resultado);
    exit;
}

?>

