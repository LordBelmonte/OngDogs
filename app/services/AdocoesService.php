<?php

    include_once __DIR__ . "/../models/Adocoes.php";

    class AdocoesService {

        public function get ($id_adocao = null) {
            if ( $id_adocao ) {
                return Adocoes::buscarAdocoesPeloId($id_adocao);
            } else {
                return Adocoes::buscarTodosAdocoes();
            }
        }

        public function post () {
            $dados = json_decode(file_get_contents("php://input"), true, 512);
            if ($dados == null) {
                throw new Exception("Falta os dados para incluir");
            }
            return Adocoes::inserir( $dados );
        }

        public function put ( $id_adocao = null ) {
            if ($id_adocao == null) {
                throw new Exception("Falta o código");
            }
            $dados = json_decode(file_get_contents("php://input"), true, 512);
            if ($dados == null) {
                throw new Exception("Falta os dados para alterar");
            }
            return Adocoes::alterar( $id_adocao, $dados );
        }

        public function delete ( $id = null ) {
            if ($id == null) {
                throw new Exception("Falta o código");
            }
            return Adocoes::deletar( $id );
        }

    }

?>
