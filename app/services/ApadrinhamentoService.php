<?php

    include_once __DIR__ . "/../models/Apadrinhamento.php";

    class ApadrinhamentoService {

        public function get ($id = null) {
            if ( $id ) {
                return Apadrinhamento::buscarApadrinhamentoPeloId($id);
            } else {
                return Apadrinhamento::buscarTodosApadrinhamento();
            }
        }

        public function post () {
            $dados = json_decode(file_get_contents("php://input"), true, 512);
            if ($dados == null) {
                throw new Exception("Falta os dados para incluir");
            }
            return Apadrinhamento::inserir( $dados );
        }

        public function put ( $id = null ) {
            if ($id == null) {
                throw new Exception("Falta o código");
            }
            $dados = json_decode(file_get_contents("php://input"), true, 512);
            if ($dados == null) {
                throw new Exception("Falta os dados para alterar");
            }
            return Apadrinhamento::alterar( $id, $dados );
        }

        public function delete ( $id = null ) {
            if ($id == null) {
                throw new Exception("Falta o código");
            }
            return Apadrinhamento::deletar( $id );
        }

    }

?>
