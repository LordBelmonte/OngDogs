<?php

     include_once __DIR__ . "/../models/Participacoes.php";

    class ParticipacoesService {
        //Método GET para buscar Participacoes
        //Método POST para inserir Participacoes
        //Método PUT para atualizar Participacoes
        //Método DELETE para deletar Participacoes


        public function get ($id_participacao = null) {
            if ( $id_participacao ) {            
                return Participacoes::buscarParticipacoesPeloId( id_participacao: $id_participacao); //A consulta será feita pelo código do aluno            
            } else {              
                return Participacoes::buscarTodosParticipacoes(); //A consulta será de TODOS os Alunos             
            }
        }

        public function post () {
            //Pegar os dados no formato JSON para gravar no banco de dados
            $dados = json_decode(file_get_contents("php://input"), true, 512);
            if ($dados == null) {
                throw new Exception("Falta os dados para incluir");
            }
            return Participacoes::inserir( $dados );      

        }

        public function put ( $id_participacoes = null ) {
           if ($id_participacoes == null) {
                throw new Exception("Falta o código");
            }

            //Pegar os dados no formato JSON para gravar no banco de dados
            $dados = json_decode(file_get_contents("php://input"), true, 512);
            if ($dados == null) {
                throw new Exception("Falta os dados para alterar");
            }
            return Participacoes::alterar( $id_participacoes, $dados );

        }

        public function delete ( $id = null ) {
            if ($id == null) {
                throw new Exception("Falta o código");
            }
            return Participacoes::deletar( $id );
        }

    }

?>