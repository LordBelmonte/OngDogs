<?php

    include_once __DIR__ . "/../models/Animais.php";

    class AnimaisService {

        public function get ($id_animal = null) {
            if ( $id_animal ) {
                return Animais::buscarAnimaisPeloId($id_animal);
            } else {
                return Animais::buscarTodosAnimais();
            }
        }

        public function post () {
            $dados = json_decode(file_get_contents("php://input"), true, 512);
            if ($dados == null) {
                throw new Exception("Falta os dados para incluir");
            }
            return Animais::inserir( $dados );
        }

        public function put ( $id_animal = null ) {
            if ($id_animal == null) {
                throw new Exception("Falta o código");
            }
            $dados = json_decode(file_get_contents("php://input"), true, 512);
            if ($dados == null) {
                throw new Exception("Falta os dados para alterar");
            }
            return Animais::alterar( $id_animal, $dados );
        }

        public function delete ( $id = null ) {
            if ($id == null) {
                throw new Exception("Falta o código");
            }
            return Animais::deletar( $id );
        }

    }

?>
