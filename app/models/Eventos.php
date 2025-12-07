<?php

// Linha 3: Define o caminho absoluto para a pasta raiz do projeto.
$rootPath = dirname(dirname(dirname(__FILE__)));

// Usa $rootPath para incluir o arquivo de configuração,
// garantindo que o separador de diretório seja o correto para o SO.
require_once $rootPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

class Eventos {
    public static function inserir( $dados ) {
        $tabela = "eventos";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "INSERT INTO $tabela (nome, descricao, data_inicio, data_fim) VALUES (:nome, :descricao, :data_inicio, :data_fim)";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":nome", $dados["nome"] ?? $dados["titulo"] ?? '');
        $stm->bindValue(":descricao", $dados["descricao"] ?? null);
        $stm->bindValue(":data_inicio", $dados["data_inicio"] ?? $dados["data"] ?? null);
        $stm->bindValue(":data_fim", $dados["data_fim"] ?? null);
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            $id = $conexao->lastInsertId();
            return [ 'erro' => false, 'mensagem' => 'Registro inserido com sucesso!', 'dados' => ['id_evento' => $id] ];
        }
        return [ 'erro' => true, 'mensagem' => 'Erro ao inserir registro!', 'dados' => [] ];
    }

    public static function buscarEventosPeloId( $id_evento ) {
        $tabela = "eventos";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "SELECT * FROM $tabela WHERE id_evento = :id_evento";
        $stm = $conexao->prepare( $sql );
        $stm->bindValue(":id_evento", $id_evento );
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            $valores = $stm->fetch(PDO::FETCH_ASSOC);
            return [ 'erro' => false, 'mensagem' => "Registro encontrado!", 'dados' => $valores ];
        }
        return [ 'erro' => true, 'mensagem' => "Codigo não cadastrado!", 'dados' => [] ];
    }

    public static function buscarTodosEventos() {
        $tabela = "eventos";
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

    public static function alterar( $id_evento, $dados ) {
        $tabela = "eventos";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "UPDATE $tabela SET nome = :nome, descricao = :descricao, data_inicio = :data_inicio, data_fim = :data_fim WHERE id_evento = :id_evento";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":nome", $dados["nome"] ?? $dados["titulo"] ?? '');
        $stm->bindValue(":descricao", $dados["descricao"] ?? null);
        $stm->bindValue(":data_inicio", $dados["data_inicio"] ?? $dados["data"] ?? null);
        $stm->bindValue(":data_fim", $dados["data_fim"] ?? null);
        $stm->bindValue(":id_evento", $id_evento);
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            return [ 'erro' => false, 'mensagem' => 'Dados alterados com sucesso!', 'dados' => [] ];
        }
        return [ 'erro' => true, 'mensagem' => 'Erro!', 'dados' => [] ];
    }

    public static function deletar( $id_evento ) {
        $tabela = "eventos";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "DELETE FROM $tabela WHERE id_evento = :id_evento";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_evento", $id_evento);
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            // tenta remover imagem associada, se existir
            $rootPath = dirname(dirname(dirname(__FILE__)));
            $imgPathJpg = $rootPath . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'imagens' . DIRECTORY_SEPARATOR . 'eventos' . DIRECTORY_SEPARATOR . 'evento_' . $id_evento . '.jpg';
            $imgPathPng = $rootPath . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'imagens' . DIRECTORY_SEPARATOR . 'eventos' . DIRECTORY_SEPARATOR . 'evento_' . $id_evento . '.png';
            if (file_exists($imgPathJpg)) @unlink($imgPathJpg);
            if (file_exists($imgPathPng)) @unlink($imgPathPng);
            return [ 'erro' => false, 'mensagem' => 'Dados deletados com sucesso!', 'dados' => [] ];
        }
        return [ 'erro' => true, 'mensagem' => 'Erro!', 'dados' => [] ];
    }
}

// Processar POST requests se houver action=criar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'criar') {
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        echo json_encode(['erro' => true, 'mensagem' => 'Dados inválidos', 'dados' => []]);
        exit;
    }

    // inserir usando campos esperados: nome, descricao, data_inicio, data_fim
    $dados = [];
    $dados['nome'] = $input['nome'] ?? $input['titulo'] ?? '';
    $dados['descricao'] = $input['descricao'] ?? null;
    $dados['data_inicio'] = $input['data_inicio'] ?? $input['data'] ?? null;
    $dados['data_fim'] = $input['data_fim'] ?? null;

    $resultado = Eventos::inserir($dados);
    echo json_encode($resultado);
    exit;
}

// Processar GET requests para listar eventos
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'listar') {
    header('Content-Type: application/json');
    
    $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
    $sql = "SELECT * FROM eventos ORDER BY data_inicio DESC";
    $stm = $conexao->prepare($sql);
    $stm->execute();
    
    if ($stm->rowCount() > 0) {
        $dados = $stm->fetchAll(PDO::FETCH_ASSOC);

        // anexa caminho de imagem para cada evento (se existir arquivo em public/imagens/eventos/evento_<id>.(jpg|png))
        foreach ($dados as &$ev) {
            $id = $ev['id_evento'] ?? $ev['id'] ?? null;
            $imgRel = 'imagens/banner_ong.jpeg';
            if ($id) {
                $jpg = $rootPath . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'imagens' . DIRECTORY_SEPARATOR . 'eventos' . DIRECTORY_SEPARATOR . 'evento_' . $id . '.jpg';
                $png = $rootPath . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'imagens' . DIRECTORY_SEPARATOR . 'eventos' . DIRECTORY_SEPARATOR . 'evento_' . $id . '.png';
                if (file_exists($jpg)) {
                    $imgRel = 'imagens/eventos/evento_' . $id . '.jpg';
                } elseif (file_exists($png)) {
                    $imgRel = 'imagens/eventos/evento_' . $id . '.png';
                }
            }
            $ev['imagem'] = $imgRel;
        }

        echo json_encode(['erro' => false, 'mensagem' => 'Eventos encontrados', 'dados' => $dados]);
    } else {
        echo json_encode(['erro' => true, 'mensagem' => 'Nenhum evento encontrado', 'dados' => []]);
    }
    exit;
}

// Processar DELETE requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'deletar') {
    session_start();
    header('Content-Type: application/json');
    
    $id = $_GET['id'] ?? 0;
    
    if (!$id) {
        echo json_encode(['erro' => true, 'mensagem' => 'Dados inválidos', 'dados' => []]);
        exit;
    }
    
    $resultado = Eventos::deletar($id);
    echo json_encode($resultado);
    exit;
}

?>


