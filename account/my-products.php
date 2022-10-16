<?php 
    
    // Busca os produtos à venda do usuário no DB.
    try {

        $id_user = limpeza($_SESSION['id_user']);
        $conn = conexao_db();
        $stat = $conn->query("
            select 
                produtos_a_venda.titulo_produto, 
                produtos_a_venda.id_produto, 
                produtos_a_venda.preco, 
                imagens_produtos.imagem_produto, 
                imagens_produtos.tipo_imagem 
            from produtos_a_venda 
            cross join imagens_produtos 
            where 
                produtos_a_venda.id_user_vendedor={$id_user} and 
                produtos_a_venda.id_produto=imagens_produtos.id_produto;
        ");

    }
    catch (PDOException $e){
        db_erro($e);
    }

?>
<section id="products-list" class="container col-12 col-md-9 col-lg-10">

    <h1>Produtos Que você está Vendendo:</h1>

    <!-- AQUI COMEÇA O PHP QUE VAI PROCESSAR E FORMATAR A LISTA DE PRODUTOS -->
    <?php 
        if ($stat !== false){

            $produtos = $stat->fetchAll();

            foreach ($produtos as $key => $value){
                
                // Formata a string do preço.
                $preco = formatar_preco($value['preco']);

            ?>
        
            <div>
                <div class="container-fluid row col-12">

                    <div class="container-fluid col-12 col-md-9">
                        <a class=""
                            href="produto.php?id_produto=<?php echo $value['id_produto']; ?>">
                            <h4><?php 
                                echo $value['titulo_produto']; ?>
                            </h4>
                        </a>
                        <p>R$ <?php echo $preco; ?><p>
                        
                    </div>

                    <div class="container-fluid col-12 col-md-3 css-image" >
                        <img 
                            src="imagem-produto.php?id_produto=<?php echo $value['id_produto']; ?>" 
                            alt="<?php echo $value['titulo_produto']; ?>"
                        >
                    </div>

                </div>

                <div class="container-fluid col-12">
                    <a href="account.php?link=edit&id_produto=<?php echo $value['id_produto']; ?>">Editar Produto</a>
                    <a href="account.php?link=delete&id_produto=<?php echo $value['id_produto']; ?>">Excluir Produto</a>
                </div>
            </div>

    <?php
            }
        }
        else {
    ?>
            <h1>Você não possui produtos cadastrado para vender!</h1>
            <p><a href="vender.php">Cadastre um produto agora!</a></p>
    <?php
        }
    ?>
</section>