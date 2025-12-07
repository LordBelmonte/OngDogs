<?php

// Linha 3: Define o caminho absoluto para a pasta raiz do projeto.
$rootPath = dirname(dirname(dirname(__FILE__))); 

// Usa $rootPath para incluir o arquivo de configuração,
// garantindo que o separador de diretório seja o correto para o SO.
require_once $rootPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

    class Participacoes {
        public static function inserir( $dados ) {
            $tabela = "participacoes";
            $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );

            $sql = "INSERT INTO $tabela (id_usuario, id_evento) VALUES (:id_usuario , :id_evento)";

            //Trocar o apelido pela informação
            $stm = $conexao->prepare($sql);
            $stm->bindValue(":id_usuario", $dados["id_usuario"]);
            $stm->bindValue(":id_evento", $dados["id_evento"]);
            
            $stm->execute();

            if ( $stm->rowCount() > 0 ) {
                //return "Registro inserido com sucesso!";
                return [
                    'erro' => false,
                    'mensagem' => 'Registro inserido com sucesso!',
                    'dados' => []
                ];
            } else {
                //return "Erro ao inserir registro!";
                return [
                    'erro' => true,
                    'mensagem' => 'Erro ao inserir registro!',
                    'dados' => []
                ];
            }
        }

    public static function buscarParticipacoesPeloId( $id_participacao ) {

        $tabela = "participacoes";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );

        $sql = "SELECT * FROM $tabela WHERE id_participacao = :id_participacao";

        $stm = $conexao->prepare( $sql );
        $stm->bindValue(":id_participacao", $id_participacao);

        $stm->execute();

        if ( $stm->rowCount() > 0 ) {
            $valores = $stm->fetch(PDO::FETCH_ASSOC);
            //var_dump( $valores );
            return [
                'erro' => false,
                'mensagem' => "Registro encontrado!",
                'dados' => $valores
            ];
        } else {
            //return "Codigo não cadastrado!";
            return [
                'erro' => true,
                'mensagem' => "Codigo não cadastrado!",
                'dados' => []
            ];
        }
    }

    public static function buscarTodosParticipacoes() {

        $tabela = "participacoes";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );

        $sql = "SELECT * FROM $tabela";

        $stm = $conexao->prepare($sql);

        $stm->execute();

        if ( $stm->rowCount() > 0 ) {
            $valores = $stm->fetchAll(PDO::FETCH_ASSOC);
            //var_dump( $valores );
            return [
                'erro' => false,
                'mensagem' => 'Registros encontrados!',
                'dados' => $valores
            ];
        } else {
            return [
                'erro' => true,
                'mensagem' => 'Tabela está vazia!',
                'dados' => []
            ];
        }
    }

    public static function alterar( $id_participacao, $dados ) {
        $tabela = "participacoes";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );

        $sql = "UPDATE $tabela SET id_usuario = :id_usuario, id_evento = :id_evento WHERE id_participacao = :id_participacao";

        //Trocar o apelido pela informação
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_usuario", $dados["id_usuario"]);
        $stm->bindValue(":id_evento", $dados["id_evento"]);
        $stm->bindValue(":id_participacao", $id_participacao);
        
        $stm->execute();

        if ( $stm->rowCount() > 0 ) {
            //return "Dados alterados com sucesso!";
            return [
                'erro' => false,
                'mensagem' => 'Dados alterados com sucesso!',
                'dados' => []
            ];
        } else {
            //return "Erro!";
            return [
                'erro' => true,
                'mensagem' => 'Erro!',
                'dados' => []
            ];
        }
    }

    public static function deletar( $id_participacao ) {
        $tabela = "participacoes";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );

        $sql = "DELETE FROM $tabela WHERE id_participacao = :id_participacao";

        //Trocar o apelido pela informação
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_participacao", $id_participacao);
        
        $stm->execute();

        if ( $stm->rowCount() > 0 ) {
            //return "Dados deletados com sucesso!";
            return [
                'erro' => false,
                'mensagem' => 'Dados deletados com sucesso!',
                'dados' => []
            ];
        } else {
            //return "Erro!";
            return [
                'erro' => true,
                'mensagem' => 'Erro!',
                'dados' => []
            ];
        }
    }
}

// Handlers: criar/listar/deletar via HTTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'criar') {
    session_start();
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) {
        echo json_encode(['erro' => true, 'mensagem' => 'Dados inválidos', 'dados' => []]);
        exit;
    }
    $input['id_usuario'] = $_SESSION['user_id'] ?? 0;
    $input['id_evento'] = $input['id_evento'] ?? 0;
    if (!$input['id_usuario'] || !$input['id_evento']) {
        echo json_encode(['erro' => true, 'mensagem' => 'Usuário ou evento inválido', 'dados' => []]);
        exit;
    }
    $resultado = Participacoes::inserir($input);
    echo json_encode($resultado);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'listar') {
    session_start();
    header('Content-Type: application/json');
    $id_usuario = $_SESSION['user_id'] ?? 0;
    if (!$id_usuario) {
        echo json_encode(['erro' => true, 'mensagem' => 'Usuário não autenticado', 'dados' => []]);
        exit;
    }
    $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
    $sql = "SELECT p.id_participacao, p.id_evento, e.nome, e.descricao, e.data_inicio, e.data_fim FROM participacoes p JOIN eventos e ON p.id_evento = e.id_evento WHERE p.id_usuario = :id_usuario ORDER BY e.data_inicio DESC";
    $stm = $conexao->prepare($sql);
    $stm->bindValue(":id_usuario", $id_usuario);
    $stm->execute();
    if ($stm->rowCount() > 0) {
        $dados = $stm->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['erro' => false, 'mensagem' => 'Participações encontradas', 'dados' => $dados]);
    } else {
        echo json_encode(['erro' => true, 'mensagem' => 'Nenhuma participação encontrada', 'dados' => []]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'deletar') {
    session_start();
    header('Content-Type: application/json');
    $id = $_GET['id'] ?? 0;
    $id_usuario = $_SESSION['user_id'] ?? 0;
    if (!$id || !$id_usuario) {
        echo json_encode(['erro' => true, 'mensagem' => 'Dados inválidos', 'dados' => []]);
        exit;
    }
    $resultado = Participacoes::deletar($id);
    echo json_encode($resultado);
    exit;
}

?>