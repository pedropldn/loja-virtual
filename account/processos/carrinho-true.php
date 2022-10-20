<section class="container-fluid col-12 col-md-9 col-lg-10">  

    <form id="products-list" class="container-fluid col-12" action="account/processos/confirmar-compra-carrinho.php" method="post">
        <h1>Confirme Sua Compra: </h1>
    <?php

    if (count($products) === 0){
        header("Location: account.php?link=carrinho");
    }

    $preco_total = 0;

    foreach ($products as $k => $p){ 

        if ($p['quant'] === 0){
            remove_do_carrinho($p['id_produto']);
            continue;
        }

        // Calcula o preço total.
        $preco = $p['quant'] * $p['preco'];
        $preco_total += $preco;

        ?>
        
        <div class="container-fluid row col-12">
            
            <div class="container-fluid col-12 col-sm-8">
                <h4><?php echo $p['titulo_produto']; ?></h4>
                <p><strong><?php echo $p['quant']; ?></strong> unidade(s).</p>
                <p><strong>R$ <?php echo formatar_preco($preco); ?></strong></p>
            </div>
            <div class="col-12 col-sm-4 ">
                <img src="imagem-produto.php?id_produto=<?php echo $p['id_produto']; ?>" 
                    alt="<?php echo $p['titulo_produto']; ?>"
                >
            </div>

            <input type="hidden" name="preco_<?php echo $p['id_produto']; ?>" value="<?php echo $preco; ?>">

            <!-- CONCATENA O PREFIXO "quant_" COM O ID DO PRODUTO PARA FACILITAR NO PROCESSAMENTO DA COMPRA-->
            <input type="hidden" name="quant_<?php echo $p['id_produto']; ?>" value="<?php echo $p['quant']; ?>">

        </div>

    <?php
    } ?>
        <div class="container-fluid col-12">
            <p>Total: <strong>R$ <?php echo formatar_preco($preco_total); ?></strong></p>
            <input class="btn btn-success" type="submit" name="submit" value="Confirmar Compra">
            <a href="">Cancelar Compra</a>
        </div>

    </form>

</section>