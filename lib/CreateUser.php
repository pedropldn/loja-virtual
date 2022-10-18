<?php

    require_once "lib/funcoes.php";
    require_once "lib/User.php";

    class CreateUser extends User {

        public function __construct(string $name, string $email, string $password){

            parent::__construct();

            // Procura por um email já existente dentro do resultado da busca SQL;
            $res = $this->conn->query("
                select email from usuarios
                where email=\"{$email}\";
            ")->fetchAll();

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

                if ($success) {

                    $this->conn->commit();
                    $this->success = true;

                }
                else {

                    $pdostat->rollBack();
                    $this->success = false;
                    $this->errorMessage = "Erro no sistema! Avise a equipe de programação da Market Simulation";

                }

            }

        }

    }

?>