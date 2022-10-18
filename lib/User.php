<?php

    require_once "lib/funcoes.php";

    // Esta classe serve para a obtenção e manipulação dos dados de cada usuário.
    abstract class User {

        protected $conn;           // Guarda a conexão com o banco de dados.
        protected $success;        // Diz se ocorreu tudo certo na inicialização do objeto.
        protected $errorMessage;   // Guarda a mensagem de erro que possa acontecer durante a utilização da classe.

        protected $idUser;         // Guarda "id_user" do usuário
        protected $email;          // Guarda "email" do usuário
        protected $name;           // Guarda "nome" do usuário
        protected $password;       // Guarda "senha" do usuário

        protected function __construct(){

            $this->conn = conexao_db();
            $this->success = false;
            $this->idUser = null;         
            $this->name = null;  
            $this->email = null;  
            $this->password = null;  

        }

        public function success(){
            return $this->success;
        }

        public function getErrorMessage(){
            return $this->errorMessage;
        }

        public function getIdUser(){
            return $this->idUser;
        }

        public function getName(){
            return $this->name;
        }

        public function getEmail(){
            return $this->idUser;
        }

        public function getPassword(){
            return $this->password;
        }

    }

?>