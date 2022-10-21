<?php

    session_start();
    require_once "../../lib/funcoes.php";
    require_once "../../lib/GetProduct.php";
    
    // verifica se o usuário está logado. Se não estivar, manda ele pra página de login.
    if (!isset($_SESSION['id_user'])){
        session_unset();
        session_destroy();
        header("Location: login.php");
    }

    if ($_SERVER['REQUEST_METHOD'] !== "POST"){
        header("Location: 404.php");
    }

    

    if (isset($_POST['submit'], $_POST['quantidade_comprada'])){

        // Busca os dados do produto no DB.
        $id_produto = limpeza($_POST['id_produto']);

        // Busca os dados do produto em questão no banco de dados.
        $product = new GetProduct($id_produto);

        // Se o usuário estiver comprando seu próprio produto, manda ele pra 404.php.
        if ( (int)$_SESSION['id_user'] === (int)$product->getSellerUserId() ){ 
        
            header("Location: 404.php");
    
        }

        // Se o usuário estiver comprando seu próprio produto, manda ele para a 404.php
        // Ou se o produto não existir no banco de dados, também manda para a 404.php
        if ( isset($_SESSION['id_user']) || (count($product) === 0) ){

            if ($_SESSION['id_user'] === $product->getSellerUserId()){ 
                header("Location: 404.php");
            }

        }
        
        // Limpa os dados recebidos através do método post.
        $quant = limpeza($_POST['quantidade_comprada']);
        if (is_numeric($quant)){
            
            $quant = (int)$quant;

            $quantityInStock = $product->getQuantityInStock();

            if (is_nan($quant)){

                header("Location: 404.php");

            }
            elseif ($quant > 0 && $quant <= $quantityInStock){
                
                try {

                    // Inicia a conexão com o DB.
                    $conn = conexao_db();
                    $conn->beginTransaction();

                    // Calcula o preço total e a quantidade de produtos que sobrará no estoque.
                    $preco = $product->getPrice() * $quant;
                    $nova_quantidade = $quantityInStock - $quant;

                    // Retira a quantidade de produtos da tabela "produtos_a_venda".
                    if ($quant === $quantityInStock){

                        $conn->exec("
                            delete from produtos_a_venda
                            where id_produto='{$id_produto}';
                        ");

                    }
                    elseif ($quant < $quantityInStock){

                        $conn->exec("
                            update produtos_a_venda
                            set quantidade_estoque={$nova_quantidade}
                            where id_produto='{$id_produto}';
                        ");

                    }

                    // Adiciona o produto comprado e a quantidade na tabela "historico_compra_venda".
                    // Estrutura SQL com placeholders preparada para execução.
                    $stat = $conn->prepare("
                        insert into historico_compra_venda (
                            id_comprador,
                            id_vendedor,
                            id_produto,
                            titulo_produto,
                            quantidade_negociada,
                            preco_unidade,
                            data_negociacao
                        )
                        values (
                            :id_comprador,
                            :id_vendedor,
                            :id_produto,
                            :titulo_produto,
                            :quantidade_negociada,
                            :preco_unidade,
                            :data_negociacao
                        );
                    ");

                    // Vincula os dados da negociacao à cada placeholder correspondente.
                    $stat->bindParam(":id_comprador", $_SESSION['id_user']);
                    $stat->bindParam(":id_vendedor", $product->getSellerUserId());
                    $stat->bindParam(":id_produto", $product->getProductId());
                    $stat->bindParam(":titulo_produto", $product->getProductTitle());
                    $stat->bindParam(":quantidade_negociada", $quant);
                    $stat->bindParam(":preco_unidade", $product->getPrice());

                    // Cria um timestamp do momento da negociação.
                    $t = time();
                    $stat->bindParam(":data_negociacao", $t);
                    $stat->execute();
                    
                    // Se tudo deu certo, salva as modificações no DB.
                    $conn->commit();
                    
                    header("Location: ../../account.php?link=compras");

                }
                catch(PDOException $e){
                    db_erro($e);
                }

            }

        }

    }

?>