<?php

    if (isset($_GET['add_shop_cart'])){

        // verifica se o usuário está logado. Se não estivar, manda ele pra página de login.
        if (!isset($_SESSION['id_user'])){
            session_unset();
            session_destroy();
            header("Location: login.php");
        }

        require_once "lib/ShopCart.php";

        // Limpa o id do produto e o id do usuário para trabalhar com o objeto carrinho (ShopCart).
        $productId = limpeza($_GET['id_produto']);
        $shopCart = new ShopCart(
            limpeza($_SESSION['id_user'])
        );

        // Adiciona o id do produto ao carrinho.
        $shopCart->addProduct($productId);

    }
?>

<a class="btn btn-success" href="comprar.php?id_produto=<?php echo $product->getProductId(); ?>">Comprar</a>
<a class="btn btn-warning" href="?id_produto=<?php echo $product->getProductId(); ?>&add_shop_cart=1">Adicionar ao Carrinho</a>