<?php

    require_once "lib/EditProduct.php";

    // Busca o produto no DB, pelo "id_produto".
    $product = new EditProduct(
        limpeza($_GET['id_produto'])
    );

    if ($product->getSellerUserId() !== (int)$_SESSION['id_user']){
        header("Location: 404.php");
    }
    else {

        // Adiciona o id_produto ao array dos dados.
        $dados_produto['id_produto'] = $product->getProductId();

        if ($_SERVER['REQUEST_METHOD'] === "GET"){

            // Atribui os dados do produto no DB à cada campo correspondente.
            $dados_produto['titulo_produto'] = $product->getProductTitle();
            $dados_produto['quantidade'] = $product->getQuantityInStock();
            $dados_produto['preco'] = formatar_preco($product->getPrice());
            $dados_produto['descricao'] = $product->getDescription();
        
        }
        elseif ($_SERVER['REQUEST_METHOD'] === "POST"){

            // Essa variável é o countador para saber se todos os dados do produto são válidos.

            // Loop para limpeza e verificação de campos vazios recebidos pelo metodo post.
            foreach ($dados_produto as $key => $value){
                if (isset($_POST[$key])){
                    $dados_produto[$key] = limpeza($_POST[$key]);
                    if (empty($dados_produto[$key])){
                        $msgs_erros[$key] = "Obrigatório preencher este campo";
                    }
                }
            }

            $msgs_erros['titulo_produto'] = $product->setProductTitle($dados_produto['titulo_produto']);
            $msgs_erros['quantidade'] = $product->setQuantityInStock($dados_produto['quantidade']);
            $msgs_erros['preco'] = $product->setPrice($dados_produto['preco']);
            $msgs_erros['descricao'] = $product->setDescription($dados_produto['descricao']);

            // Verifica se uma nova imagem foi enviada.
            // Se não foi, mantém a mesma no banco de dados. Caso contrário, chama a função
            // que irá preparar a aplicação da nova imagem ao banco de dados. 
            if ( empty($_FILES['imagem']['tmp_name']) ){

                $msgs_erros['imagem'] = $product->setImage( NULL );

            }
            elseif (isset($_FILES['imagem'])){
                
                $msgs_erros['imagem'] = $product->setImage("imagem");

            }

            // Verifica se o usuario está logado, para evitar problemas na hora de aplicar os dados no banco de dados.
            if (!isset($_SESSION['id_user'])){

                header("Location: login.php");

            }
        
            if ($product->save()){
                    
                header("Location: account.php?link=my_products");

            }
            else {

                throw new Exception("Erro na edição do produto! Contate a equipe de programação");
                exit;

            }

        }
    }

?>