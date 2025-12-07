<?php

    include_once __DIR__ . "/../models/Eventos.php";

    class EventosService {

        public function get ($id_evento = null) {
            if ( $id_evento ) {
                return Eventos::buscarEventosPeloId($id_evento);
            } else {
                return Eventos::buscarTodosEventos();
            }
        }

        public function post () {
            $dados = json_decode(file_get_contents("php://input"), true, 512);
            if ($dados == null) {
                throw new Exception("Falta os dados para incluir");
            }
            return Eventos::inserir( $dados );
        }

        public function put ( $id_evento = null ) {
            if ($id_evento == null) {
                throw new Exception("Falta o código");
            }
            $dados = json_decode(file_get_contents("php://input"), true, 512);
            if ($dados == null) {
                throw new Exception("Falta os dados para alterar");
            }
            return Eventos::alterar( $id_evento, $dados );
        }

        public function delete ( $id = null ) {
            if ($id == null) {
                throw new Exception("Falta o código");
            }
            return Eventos::deletar( $id );
        }

    }

?>
