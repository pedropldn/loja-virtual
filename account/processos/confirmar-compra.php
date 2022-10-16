<?php

    session_start();
    require_once "../../lib/funcoes.php";
    
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
        $produto = buscar_produto($id_produto);

        // Se o usuário estiver comprando seu próprio produto, manda ele para a 404.php
        // Ou se o produto não existir no banco de dados, também manda para a 404.php
        if ( isset($_SESSION['id_user']) || (count($produto) === 0) ){

            if ($_SESSION['id_user'] === $produto[0]['id_user_vendedor']){ 
                header("Location: 404.php");
            }

        }

        $quant = limpeza($_POST['quantidade_comprada']);
        if (is_numeric($quant)){
            
            // Limpa os dados recebidos através do método post.
            $quant = (int)$quant;

            // Busca os dados do produto no DB.
            $produto = buscar_produto($id_produto);

            if (is_nan($quant)){

                header("Location: 404.php");

            }
            elseif ($quant > 0 && $quant <= $produto[0]['quantidade_estoque']){
                
                try {

                    // Inicia a conexão com o DB.
                    $conn = conexao_db();
                    $conn->beginTransaction();

                    // Calcula o preço total e a quantidade de produtos que sobrará no estoque.
                    $preco = $produto[0]['preco'] * $quant;
                    $nova_quantidade = $produto[0]['quantidade_estoque'] - $quant;

                    // Retira a quantidade de produtos da tabela "produtos_a_venda".
                    if ($quant === $produto[0]['quantidade_estoque']){

                        $conn->exec("
                            delete from produtos_a_venda
                            where id_produto='{$id_produto}';
                        ");

                    }
                    elseif ($quant < $produto[0]['quantidade_estoque']){

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
                    $stat->bindParam(":id_vendedor", $produto[0]['id_user_vendedor']);
                    $stat->bindParam(":id_produto", $produto[0]['id_produto']);
                    $stat->bindParam(":titulo_produto", $produto[0]['titulo_produto']);
                    $stat->bindParam(":quantidade_negociada", $quant);
                    $stat->bindParam(":preco_unidade", $produto[0]['preco']);

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