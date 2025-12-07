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

        $sql = "INSERT INTO $tabela (id_usuario, id_animal, valor_mensal) VALUES (:id_usuario, :id_animal, :valor_mensal)";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_usuario", $dados["id_usuario"]);
        $stm->bindValue(":id_animal", $dados["id_animal"]);
        $stm->bindValue(":valor_mensal", $dados["valor_mensal"] ?? null);
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
        $sql = "SELECT * FROM $tabela";
        $stm = $conexao->prepare($sql);
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            $valores = $stm->fetchAll(PDO::FETCH_ASSOC);
            return [ 'erro' => false, 'mensagem' => 'Registros encontrados!', 'dados' => $valores ];
        }
        return [ 'erro' => true, 'mensagem' => 'Tabela está vazia!', 'dados' => [] ];
    }

    public static function alterar( $id, $dados ) {
        $tabela = "apadrinhamentos";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "UPDATE $tabela SET id_usuario = :id_usuario, id_animal = :id_animal, valor_mensal = :valor_mensal WHERE id_apadrinhamento = :id_apadrinhamento";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_usuario", $dados["id_usuario"]);
        $stm->bindValue(":id_animal", $dados["id_animal"]);
        $stm->bindValue(":valor_mensal", $dados["valor_mensal"] ?? null);
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

?>
