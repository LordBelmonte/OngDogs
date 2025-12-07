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

?>
