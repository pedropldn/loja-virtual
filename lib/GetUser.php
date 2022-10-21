<?php

    require_once "funcoes.php";
    require_once "User.php";

    // Essa class busca as informações de um usuário específico no banco de dados,
    // Principalmente para a realização de login no sistema da loja virtual.
    class GetUser extends User{

        // O parâmetro "$email_id" pode ser do tipo inteiro ou string.
        // Se for inteiro, o método construtor irá buscar o usuário pelo "id_user".
        // Se for string, o construtor irá buscar o usuário pelo email. 
        public function __construct($email_id){

            parent::__construct();

            if (is_int($email_id)){

                $query = $this->conn->query("
                    select email, senha, id_user, nome 
                    from usuarios
                    where id_user=\"{$email_id}\";
                ")->fetchAll();

                if (count($query) === 0){

                    $this->errorMessage = "Esse ID de usuário não existe!";

                }

            
            }
            elseif (is_string($email_id)) {

                $query = $this->conn->query("
                    select email, senha, id_user, nome 
                    from usuarios
                    where email=\"{$email_id}\";
                ")->fetchAll();

                if (count($query) === 0){

                    $this->errorMessage = "Esse e-email não possui conta cadastrada!";

                }

            }
            else {

                // Se chegar aqui, é porque a classe foi inicilizada com argumentos do tipo errado.
                // O IDEAL É LANÇAR UMA EXCEÇÃO.
                throw new Exception("OBJETO GetUser RECEBEU ARGUMENTOS DO TIPO ERRADO NA INSTANCIAÇÃO!");
                exit;

            }


            if (count($query) > 1) {

                // Se chegar aqui é porque existem dados duplicados no banco de dados 
                // que não deveriam estar duplicados.
                throw new Exception("Problemas com usuários duplicados no banco de dados. Contate a equipe de programação!");
                exit;      

            }
            elseif (count($query) === 1){

                // Guarda os dados do usuário em suas propriedades correspondentes.
                $this->idUser = $query[0]['id_user'];
                $this->name = $query[0]['nome'];
                $this->email = $query[0]['email'];
                $this->password = $query[0]['senha'];
                $this->success = true;

            }

        }

    }

?>