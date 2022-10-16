<?php
    
    // Inicia conexão com o DB.
    $conn = conexao_db();

    // Busca pelos produtos que foram vendidos por este usuário 
    // e ordena eles descendentemente pela data e hora de negociação.
    $compras = $conn->query("
        select * from historico_compra_venda
        where id_vendedor={$_SESSION['id_user']}
        order by data_negociacao desc;
    ")->fetchAll();

    if (count($compras) === 0){ ?>

        <h1>Você ainda não vendeu nenhum produto!</h1>
    
    <?php
    }
    else { ?>

        
        <section id="products-list" class="container-fluid col-12 col-md-9 col-lg-10">

            <h1>Produtos Que Você Vendeu: </h1>

    <?php

        foreach ($compras as $value){
                        
            // Formata a string do preço.
            $preco_total = formatar_preco( $value['preco_unidade'] * $value['quantidade_negociada'] );
            $data = date("d/m/Y à\s H:i:s", $value['data_negociacao']); ?>
            
            <div class="container-fluid row">

                <div class="container-fluid row">
                    <div class="col-12 col-md-9">
                        <h4> <?php echo $value['titulo_produto']; ?> </h4>
                        <p>Quantidade: <?php echo $value['quantidade_negociada']; ?></p>
                        <p>Valor Unidade: R$ <?php echo $value['preco_unidade']; ?><p>
                        <p>Valor Total: R$ <?php echo $preco_total; ?><p>
                    </div>
                    
                    <div class="col-12 col-md-3 css-image">
                        <img src="imagem-produto.php?id_produto=<?php echo $value['id_produto']; ?>" 
                            alt="<?php echo $value['titulo_produto']; ?>"
                        >
                    </div>
                </div>

                <div class="container-fluid">Vendido em: <?php echo $data; ?>
                    para: 
                    <?php 
                        // Essa função retorna o nome do usuário desejado através do id_user.
                        echo busca_usuario($value['id_comprador']); 
                    ?>
                </div>
            </div>
       
        <?php
        } ?>
        
        </section>

    <?php
    } ?>
