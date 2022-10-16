<?php

    // Busca o produto no DB, pelo "id_produto".
    $produto = buscar_produto();

    if ($produto[0]['id_user_vendedor'] !== (int)$_SESSION['id_user']){
        header("Location: 404.php");
    }
    else {

        if ($_SERVER['REQUEST_METHOD'] === "GET"){

            // Atribui os dados do produto no DB à cada campo correspondente.
            $dados_produto['titulo_produto'] = $produto[0]['titulo_produto'];
            $dados_produto['quantidade'] = $produto[0]['quantidade_estoque'];
            $dados_produto['preco'] = formatar_preco($produto[0]['preco']);
            $dados_produto['descricao'] = $produto[0]['descricao'];
            $dados_produto['id_produto'] = $produto[0]['id_produto'];
        
        }
        elseif ($_SERVER['REQUEST_METHOD'] === "POST"){

            // Adiciona o id_produto ao array dos dados.
            $dados_produto['id_produto'] = $produto[0]['id_produto'];

            // Essa variável é o countador para saber se todos os dados do produto são válidos.
            $validacao_produto = 6;

            // Loop para limpeza e verificação de campos vazios recebidos pelo metodo post.
            foreach ($dados_produto as $key => $value){
                if (isset($_POST[$key])){
                    $dados_produto[$key] = limpeza($_POST[$key]);
                    if (empty($dados_produto[$key])){
                        $msgs_erros[$key] = "Obrigatório preencher este campo";
                    }
                }
            }

            // Validação do título do produto.
            if (strlen($dados_produto['titulo_produto']) > 80){
                $msgs_erros['titulo_produto'] = "O título do produto NÃO pode ser maior que 80 caracteres!";
            }
            elseif ((!empty($dados_produto['titulo_produto'])) && strlen($dados_produto['titulo_produto']) <= 80){
                $validacao_produto--;
            }

            // Validação da quantidade de produtos disponível no estoque.
            if (validacao_quantidade($dados_produto, $msgs_erros)){
                $validacao_produto--;
            }

            if (validacao_preco($dados_produto, $msgs_erros)){
                $validacao_produto--;
            }

            // Validação da descrição do produto.
            if (!empty($dados_produto['descricao']) && (strlen($dados_produto['descricao']) <= 2000)){
                $validacao_produto--;
            }

            // Validação da imagem de upload do produto!
            // A função validacao_imagem() depende do array $msg_erros pra funcionar, caso contrário dará erro.
            // Se der tudo certo na validacao ela retornará true;
            $nova_img = false;

            // Primeiro verifica se não foi feito nenhum upload. Se não houver upload, não iremos mudar a imagem do produto.
            if ($_FILES['imagem']['tmp_name'] === ""){

                $validacao_produto--;

            }
            elseif (isset($_FILES['imagem'])){

                $arquivo_imagem = validacao_imagem($msgs_erros);

                if ($arquivo_imagem){
                    $tipo_imagem = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
                    $nova_img = $arquivo_imagem;
                    $validacao_produto--;
                    var_dump($arquivo_imagem);
                }
            }

            // Verifica se o usuario está logado, para evitar problemas na hora de aplicar os dados so DB.
            if (isset($_SESSION['id_user'])){
                $validacao_produto--;
            }
            
            if ($validacao_produto === 0){

                try {
                    
                    // Inicia a conexão com o DB.
                    $conn = conexao_db();

                    // Verifica no DB se o id_produto condiz com o usuario vendedor dono do produto.
                    $consulta = $conn->query("
                        select id_produto, id_user_vendedor from produtos_a_venda
                        where id_produto='{$produto[0]['id_produto']}' and id_user_vendedor={$_SESSION['id_user']};
                        ")->fetchAll();

                    if (count($consulta) === 0){

                        header("Location: 404.php");

                    }

                    // Inicia uma transação explicita.
                    $conn->beginTransaction();

                    // Prepara as atualizações à serem feitas, excluindo a nova imagem.
                    $stat = $conn->prepare("
                        update produtos_a_venda
                        set titulo_produto=:titulo_produto,
                            quantidade_estoque=:quantidade_estoque,
                            preco=:preco,
                            descricao=:descricao
                        where id_produto='{$produto[0]['id_produto']}' and id_user_vendedor={$_SESSION['id_user']};
                    ");

                    $stat->bindParam(":titulo_produto", $dados_produto['titulo_produto']);
                    $stat->bindParam(":quantidade_estoque", $dados_produto['quantidade']);
                    $stat->bindParam(":preco", $dados_produto['preco']);
                    $stat->bindParam(":descricao", $dados_produto['descricao']);
                    $stat->execute();

                    // Verifica se existe imagem à ser atualizada no DB.
                    if ($nova_img != false){

                        $stat_2 = $conn->prepare("
                            update produtos_a_venda
                            set tipo_imagem=:tipo_imagem,
                                imagem_produto=:nova_img
                            where id_produto={$produto[0]['id_produto']} and id_user_vendedor={$_SESSION['id_user']};
                        ");
                        
                        $stat_2->bindParam(":tipo_imagem", $tipo_imagem);
                        $stat_2->bindParam(":nova_img", $nova_img, PDO::PARAM_LOB);
                        $stat_2->execute();

                    }

                    $conn->commit();
                    header("Location: account.php?link=my_products");
    
                }
                catch (PDOException $e){
                    db_erro($e);
                }

            }

        }
    }

?>