<?php 

    session_start();
    require_once "../../lib/funcoes.php";
    require_once "../../lib/ShopCart.php";

    // verifica se o usuário está logado. Se não estivar, manda ele pra página de login.
    if (!isset($_SESSION['id_user'])){
        session_unset();
        session_destroy();
        header("Location: login.php");
    }

    if ($_SERVER['REQUEST_METHOD'] !== "POST"){
        header("Location: 404.php");
    }

    if (isset($_POST['submit'])){

        $shopCart = new ShopCart($_SESSION['id_user']);
        $shopCartProductsIds = $shopCart->getProductsIds();

    }
    else {
        header("Location: 404.php");
    }

    // Prepara a busca para cada um dos produtos, individualmente.
    $conn = conexao_db();
    $conn->beginTransaction();

    // PDOStatement que servirá para buscar cada produto individualmente na tabela "produtos_a_venda".
    $stat_busca = $conn->prepare("
        select * from produtos_a_venda
        where id_produto=:id;
    ");

    // PDOStatement que servirá para modificar a quantidade de produtos no estoque.
    $stat_modifica = $conn->prepare("
        update produtos_a_venda
        set quantidade_estoque=:nova_quantidade
        where id_produto=:id;
    ");

    // PDOStatement que servirá para remover o produto inteiro da tabela "produtos_a_venda".
    $stat_remove = $conn->prepare("
        delete from produtos_a_venda
        where id_produto=:id;
    ");

    // Valida cada um dos dados de cada produto do carrinho.
    foreach ($shopCartProductsIds as $id){

        if (isset($_POST[ ('quant_' . $id) ])){

            // Se a quantidade não for numérico, cancela todo o processo.
            if (!is_numeric($_POST[ ('quant_' . $id) ])){

                $conn->rollBack();
                header("Location: 404.php");

            }

            $quant = (int)limpeza($_POST[ ('quant_' . $id) ]);

            $stat_busca->bindParam(":id", $id);
            $stat_busca->execute();
            $produto = $stat_busca->fetchAll()[0];

            // Se a quantidade desejada for 0, apenas remove o produto do carrinho.
            if ($quant === 0){
                remove_do_carrinho($id);
                continue;
            }

            // Se os dados da quantidade não forem válidos, cancela todo o processo.
            if ($quant > $produto['quantidade_estoque'] || 
                $quant < 1){

                $conn->rollBack();
                header("Location: 404.php");

            }
            elseif ($quant < $produto['quantidade_estoque']){

                $nova_quant = $produto['quantidade_estoque'] - $quant;
                $stat_modifica->bindParam(":id", $id);
                $stat_modifica->bindParam(":nova_quantidade", $nova_quant);
                $stat_modifica->execute();

            }
            elseif ($quant === $produto['quantidade_estoque']){

                $stat_remove->bindParam(":id", $id);
                $stat_remove->execute();

            }

            // Adiciona o produto comprado e a quantidade na tabela "historico_compra_venda".
            // Estrutura SQL com placeholders preparada para execução.
            
            // A seguinte variavel representa um objeto statement para criar um registro na
            // tabela "historico_compra_venda". 
            $stat_historico = $conn->prepare("
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
            $stat_historico->bindParam(":id_comprador", $_SESSION['id_user']);
            $stat_historico->bindParam(":id_vendedor", $produto['id_user_vendedor']);
            $stat_historico->bindParam(":id_produto", $produto['id_produto']);
            $stat_historico->bindParam(":titulo_produto", $produto['titulo_produto']);
            $stat_historico->bindParam(":quantidade_negociada", $quant);
            $stat_historico->bindParam(":preco_unidade", $produto['preco']);

            // Cria um timestamp do momento da negociação.
            $t = time();
            $stat_historico->bindParam(":data_negociacao", $t);
            $stat_historico->execute();

        }
        else {
            $conn->rollBack();
            header("Location: 404.php");
        }

    }

    $conn->commit();
    $shopCart->cleanAllProducts();
    header("Location: ../../account.php?link=compras");

?>