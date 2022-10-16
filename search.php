<?php

    // Importa a biblioteca de funções e do objeto de busca de produtos.
    require_once "lib/funcoes.php";
    require_once "lib/SearchProducts.php";

    if (isset($_GET['search'])){

        $search = limpeza($_GET['search']);

        $lista_de_produtos = new SearchProducts($search);

        if (isset($_GET['page'])){

            if (is_numeric($_GET['page'])){

                $page_number = (int)limpeza($_GET['page']);
                $produtos = $lista_de_produtos->getPage($page_number);

            }
            else {
                header("Location: 404.php");
            }

        }
        else {
            
            $produtos = $lista_de_produtos->getPage(1);

        }

    }

?>

<!DOCTYPE html>
<html lang="pt-br">
<?php require_once "templates/head.php"; ?>
<body>
    <!-- IMPORTA O CABEÇALHO PADRÃO DO SITE -->
    <?php require "header.php" ?>

    <main>
        <section class="row container col-12">
            <?php
            if ($produtos === false) { ?>

                <h1>Nenhum Produto Encontrado!</h1>

            <?php
            }
            else {

                foreach ($produtos as $p){ ?>

                    <div class="css-container-product col-12 col-sm-6 col-md-4 col-lg-3">
                        <a href="produto.php?id_produto=<?php echo $p['id_produto']; ?>">
                            <div>
                                <img src="imagem-produto.php?id_produto=<?php echo $p['id_produto']; ?>" 
                                    alt="<?php echo $p['titulo_produto']; ?>"
                                    style="max-width: 100%; height: 160px;"
                                >
                                <h4><?php 
                                    echo $p['titulo_produto']; ?>
                                </h4>
                                <p>R$ <?php echo formatar_preco($p['preco']); ?><p>
                            </div>
                        </a>
                    </div>

                <?php
                }
            } ?>
        </section>
    </main>

    <!-- IMPORTA O RODAPÉ PADRÃO DO SITE -->
    <?php require "footer.php" ?>

</body>
</html>