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
        $sql = "INSERT INTO $tabela (titulo, data_evento, local) VALUES (:titulo, :data_evento, :local)";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":titulo", $dados["titulo"]);
        $stm->bindValue(":data_evento", $dados["data"] ?? $dados["data_evento"] ?? null);
        $stm->bindValue(":local", $dados["local"] ?? null);
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            return [ 'erro' => false, 'mensagem' => 'Registro inserido com sucesso!', 'dados' => [] ];
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
        $sql = "UPDATE $tabela SET titulo = :titulo, data_evento = :data_evento, local = :local WHERE id_evento = :id_evento";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":titulo", $dados["titulo"]);
        $stm->bindValue(":data_evento", $dados["data"] ?? $dados["data_evento"] ?? null);
        $stm->bindValue(":local", $dados["local"] ?? null);
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
            return [ 'erro' => false, 'mensagem' => 'Dados deletados com sucesso!', 'dados' => [] ];
        }
        return [ 'erro' => true, 'mensagem' => 'Erro!', 'dados' => [] ];
    }
}

?>
