<?php
    
    session_start();
    require "lib/funcoes.php";

    if (isset($_GET['id_produto'])){

        // Busca produto no DB.
        $produto = buscar_produto($_GET['id_produto']);

        if (count($produto) === 0){
            header("Location: 404.php");
        }

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
                <h1><?php echo $produto[0]['titulo_produto'] ?></h1>
                <h4>R$ <?php echo formatar_preco($produto[0]['preco']); ?></h4>
                <p>Quantidade disponível: <?php echo $produto[0]['quantidade_estoque']; ?></p>
                <p>Vendedor: <?php echo $produto[0]['nome']; ?></p>

                <?php 
                
                if (isset($_SESSION['id_user'])){
                    
                    if ( (int)$_SESSION['id_user'] !== (int)$produto[0]['id_user_vendedor'] ){ 
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
                <figcaption style="display: none;"><?php echo $produto[0]['titulo_produto']; ?></figcaption>
                <img src="imagem-produto.php?id_produto=<?php echo $produto[0]['id_produto']; ?>"
                    alt="<?php echo $produto[0]['titulo_produto']; ?>"
                    style="max-width: 100%;"
                >
            </figure>
            <div class="container col-12" style="white-space: normal;">
                <h2>Descrição do Produto: </h2>
                <div><?php echo $produto[0]['descricao']; ?></div>
            </div>
        </section>
    </main>

    <!-- IMPORTA O RODAPÉ PADRÃO DO SITE -->
    <?php require "footer.php" ?>

</body>
</html>