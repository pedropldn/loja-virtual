<?php
    
    session_start();
    require_once "lib/funcoes.php";
    require_once "lib/GetProduct.php";

    if (isset($_GET['id_produto'])){

        // Busca produto no DB.
        $product = new GetProduct(
            limpeza($_GET['id_produto'])
        );

    }

?>
<!DOCTYPE html>
<html lang="pt-br">
<?php require_once "templates/head.php"; ?>
<body>
    <!-- IMPORTA O CABEÇALHO PADRÃO DO SITE -->
    <?php require "header.php" ?>

    <main class="container">
        <section id="product" class="container row">
            <div class="col-12 col-md-6">
                <h1><?php echo $product->getProductTitle(); ?></h1>
                <h4>R$ <?php echo formatar_preco($product->getPrice()); ?></h4>
                <p>Quantidade disponível: <?php echo $product->getQuantityInStock(); ?></p>
                <p>Vendedor: <?php echo $product->getSellerName(); ?></p>

                <?php 
                
                if (isset($_SESSION['id_user'])){
                    
                    if ( (int)$_SESSION['id_user'] !== (int)$product->getSellerUserId() ){ 
                        require_once "templates/botoes-comprar.php";
                    }
                    else { ?>

                        <p>Lembrando que você não pode comprar seu próprio produto!!!</p>

                    <?php
                    }

                }
                else {
                    require_once "templates/botoes-comprar.php";
                } ?>
                
            </div>
            <figure class="col-12 col-md-6">
                <figcaption style="display: none;"><?php echo $product->getProductTitle(); ?></figcaption>
                <img src="imagem-produto.php?id_produto=<?php echo $product->getProductId(); ?>"
                    alt="<?php echo $product->getProductTitle(); ?>"
                    style="max-width: 100%;"
                >
            </figure>
            <div class="container col-12" style="white-space: normal;">
                <h2>Descrição do Produto: </h2>
                <div><?php echo $product->getDescription(); ?></div>
            </div>
        </section>
    </main>

    <!-- IMPORTA O RODAPÉ PADRÃO DO SITE -->
    <?php require "footer.php" ?>

</body>
</html>