<?php

    require_once "lib/funcoes.php";
    require_once "lib/User.php";

    // Essa classe é responsável pela criação de novas contas de usuários, manipulando os
    // dados e fazendo as modificações no banco de dados.
    class CreateUser extends User {

        public function __construct(string $name, string $email, string $password){

            parent::__construct();

            // Procura por um email já existente dentro do resultado da busca SQL;
            $res = $this->conn->query("
                select email from usuarios
                where email=\"{$email}\";
            ")->fetchAll();

            // Se o email já estiver cadastrado, envia uma mensagem de erro.
            if (count($res) > 0){

                $this->success = false;
                $this->errorMessage = "Esse e-email já está cadastrado! Tente outro!";

            }
            else {

                // Insere os dados do novo usuário no banco de dados.
                // A ordem dos itens passados como argumento para o metodo execute() não pode ser alterada.

                $this->conn->beginTransaction();

                $pdostat = $this->conn->prepare("
                    insert into usuarios (email, nome, senha) 
                    values (?, ?, ?);"
                );

                $success = $pdostat->execute([
                    $email,
                    $name,
                    $password,
                ]);

                // Se der tudo certo, salvá as alterações no banco de dados.
                // Caso contrário, desfaz as alterações sem salvá-las e lança uma Exceção.
                if ($success) {

                    $this->conn->commit();
                    $this->success = true;

                }
                else {

                    $pdostat->rollBack();
                    $this->success = false;
                    throw new Exception("Erro no sistema! Avise a equipe de programação da Market Simulation");
                    exit;

                }

            }

        }

    }

?>