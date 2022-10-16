<style>
    input[type=submit]{
        padding: 1em 3em;
    }
</style>

<section class="container-fluid col-12 col-md-9 col-lg-10">

    <form id="products-list" action="" method="post">
        <h1>Produtos adicionados ao carrinho: </h1>

    <?php

        foreach ($produtos as $k => $p){ 

            ?>
            
            <div class="container-fluid row col-12">
                <div class="container-fluid col-12 col-md-8">
                    <h4><?php echo $p['titulo_produto']; ?></h4>
                    <p>R$ <?php echo formatar_preco($p['preco']); ?></p>
                    <label>Quantidade: </label>
                    <input type="number" name="<?php echo $p['id_produto']; ?>" min="1" max="<?php echo $p['quantidade_estoque']; ?>">
                    <p><a href="account.php?link=carrinho&remove_shop_cart=<?php echo $p['id_produto'] ?>">Remover do Carrinho</a></p>
                </div>
                <div class="col-12 col-md-4 ">
                    <img src="imagem-produto.php?id_produto=<?php echo $p['id_produto']; ?>" 
                        alt="<?php echo $p['titulo_produto']; ?>"
                    >
                </div>
            </div>

        <?php
        } ?>

        <div class="container">
            <input class="btn btn-success" type="submit" value="Comprar">
        </div>
    </form>
</section>