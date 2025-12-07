<?php

    include_once __DIR__ . "/../models/Doacoes.php";

    class DoacoesService {

        public function get ($id_doacao = null) {
            if ( $id_doacao ) {
                return Doacoes::buscarDoacoesPeloId($id_doacao);
            } else {
                return Doacoes::buscarTodosDoacoes();
            }
        }

        public function post () {
            $dados = json_decode(file_get_contents("php://input"), true, 512);
            if ($dados == null) {
                throw new Exception("Falta os dados para incluir");
            }
            return Doacoes::inserir( $dados );
        }

        public function put ( $id_doacao = null ) {
            if ($id_doacao == null) {
                throw new Exception("Falta o código");
            }
            $dados = json_decode(file_get_contents("php://input"), true, 512);
            if ($dados == null) {
                throw new Exception("Falta os dados para alterar");
            }
            return Doacoes::alterar( $id_doacao, $dados );
        }

        public function delete ( $id = null ) {
            if ($id == null) {
                throw new Exception("Falta o código");
            }
            return Doacoes::deletar( $id );
        }

    }

?>
