<?php
    
    require_once "lib/ShopCart.php";

    // Cria um novo objeto carrinho de compras.
    $shopCart = new ShopCart(
        limpeza($_SESSION['id_user'])
    );

    // Parte do código responsável por remover produtos do carrinho.
    if (isset($_GET['remove_shop_cart'])){
        
        $id = limpeza($_GET['remove_shop_cart']);
        $shopCart->removeProduct($id);

    }

    $confirmacao = FALSE;   // Define qual dos 2 templates será carregado.

    if ($_SERVER['REQUEST_METHOD'] === "GET"){

        $products = $shopCart->getProducts();

    }

    // Se for a confirmação do produto, Busca os produtos no DB de forma diferente.
    if ($_SERVER['REQUEST_METHOD'] === "POST"){

        $products = [];

        $conn = conexao_db();
        $shopCartProductsIds = $shopCart->getProductsIds();

        $stat = $conn->prepare("
            select titulo_produto, id_produto, preco, quantidade_estoque
            from produtos_a_venda
            where id_produto=:id;
        ");

        foreach ($_POST as $k => $v){
            
            if(array_search($k, $shopCartProductsIds) !== FALSE){

                if (!is_numeric($v)){
                    header("Location: 404.php");
                }

                $id = limpeza($k);
                $quant = (int)floor((float)limpeza($v));

                if ($quant < 0){
                    continue;
                }

                $stat->bindParam(":id", $id);
                $stat->execute();
                $prod = $stat->fetchAll();

                if (count($prod) === 1){

                    // Adiciona a quantidade à ser comprada ao array com os dados do produto.
                    $prod[0]['quant'] = $quant;

                    array_push($products, $prod[0]);

                }

            }

        }

        foreach ($products as $p){

            // Verifica se a quantidade à ser comprada é um número válido em relação ao estoque.
            if ($p['quant'] > $p['quantidade_estoque'] || $p['quant'] < 0){
                
                $confirmacao = FALSE;
                //header("Location: 404.php");

            }
            else {

                $confirmacao = TRUE;

            }

        }

    }


    if (count($products) === 0){ ?>
        <div class="container col-12 col-md-9 col-lg-10">
        <h1 class="text-center">Seu carrinho está vazio!</h1>
    
    <?php
    }
    elseif ($confirmacao === FALSE) { 
        require "processos/carrinho-false.php";
    } 
    else {
        require "processos/carrinho-true.php";
    }
    
?>
