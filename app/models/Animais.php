<?php

// Linha 3: Define o caminho absoluto para a pasta raiz do projeto.
$rootPath = dirname(dirname(dirname(__FILE__)));

// Usa $rootPath para incluir o arquivo de configuração,
// garantindo que o separador de diretório seja o correto para o SO.
require_once $rootPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

class Animais {
    public static function inserir( $dados ) {
        $tabela = "animais";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );

        $sql = "INSERT INTO $tabela (nome, idade, especie, descricao) VALUES (:nome, :idade, :especie, :descricao)";

        $stm = $conexao->prepare($sql);
        $stm->bindValue(":nome", $dados["nome"]);
        $stm->bindValue(":idade", $dados["idade"] ?? null);
        $stm->bindValue(":especie", $dados["especie"] ?? null);
        $stm->bindValue(":descricao", $dados["descricao"] ?? null);

        $stm->execute();

        if ( $stm->rowCount() > 0 ) {
            return [ 'erro' => false, 'mensagem' => 'Registro inserido com sucesso!', 'dados' => [] ];
        } else {
            return [ 'erro' => true, 'mensagem' => 'Erro ao inserir registro!', 'dados' => [] ];
        }
    }

    public static function buscarAnimaisPeloId( $id_animal ) {
        $tabela = "animais";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "SELECT * FROM $tabela WHERE id_animal = :id_animal";
        $stm = $conexao->prepare( $sql );
        $stm->bindValue(":id_animal", $id_animal );
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            $valores = $stm->fetch(PDO::FETCH_ASSOC);
            return [ 'erro' => false, 'mensagem' => "Registro encontrado!", 'dados' => $valores ];
        } else {
            return [ 'erro' => true, 'mensagem' => "Codigo não cadastrado!", 'dados' => [] ];
        }
    }

    public static function buscarTodosAnimais() {
        $tabela = "animais";
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

    public static function alterar( $id_animal, $dados ) {
        $tabela = "animais";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "UPDATE $tabela SET nome = :nome, idade = :idade, especie = :especie, descricao = :descricao WHERE id_animal = :id_animal";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":nome", $dados["nome"]);
        $stm->bindValue(":idade", $dados["idade"] ?? null);
        $stm->bindValue(":especie", $dados["especie"] ?? null);
        $stm->bindValue(":descricao", $dados["descricao"] ?? null);
        $stm->bindValue(":id_animal", $id_animal);
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            return [ 'erro' => false, 'mensagem' => 'Dados alterados com sucesso!', 'dados' => [] ];
        } else {
            return [ 'erro' => true, 'mensagem' => 'Erro!', 'dados' => [] ];
        }
    }

    public static function deletar( $id_animal ) {
        $tabela = "animais";
        $conexao = new PDO( dbDrive . ":host=" . dbEndereco . ";dbname=" . dbNome, dbUsuario, dbSenha );
        $sql = "DELETE FROM $tabela WHERE id_animal = :id_animal";
        $stm = $conexao->prepare($sql);
        $stm->bindValue(":id_animal", $id_animal);
        $stm->execute();
        if ( $stm->rowCount() > 0 ) {
            return [ 'erro' => false, 'mensagem' => 'Dados deletados com sucesso!', 'dados' => [] ];
        } else {
            return [ 'erro' => true, 'mensagem' => 'Erro!', 'dados' => [] ];
        }
    }
}

?>
