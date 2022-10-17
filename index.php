<?php
    session_start();

    require_once "lib/funcoes.php";
    require_once "lib/SearchProducts.php";

    // Busca os produtos que serão mostrados na index.php
    $lista_de_produtos = new SearchProducts("");

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

?>

<!DOCTYPE html>
<html lang="pt-br">
<?php require_once "templates/head.php"; ?>
<body>
    <!-- IMPORTA O CABEÇALHO PADRÃO DO SITE -->
    <?php require "header.php" ?>

    

    <main class="container-fluid">
        <h1 id="welcome">Bem vindo à Market Simulation</h1>

        <section class="row container col-12">
            <!-- AQUI COMEÇA O PHP QUE VAI PROCESSAR E FORMATAR A LISTA DE PRODUTOS -->
            <?php 
                if ($produtos !== false){
                    foreach ($produtos as $key => $value){ 
                        
                        // Formata a string do preço.
                        $preco = formatar_preco($value['preco']);

                    ?>
                        <div class="css-container-product col-12 col-sm-6 col-md-4 col-lg-3">
                            <a href="produto.php?id_produto=<?php echo $value['id_produto']; ?>">
                                <div>
                                    <img src="imagem-produto.php?id_produto=<?php echo $value['id_produto']; ?>" 
                                        alt="<?php echo $value['titulo_produto']; ?>"
                                        style="max-width: 100%; height: 150px;"
                                    >
                                    <h4><?php 
                                        echo $value['titulo_produto']; ?>
                                    </h4>
                                    <p>R$ <?php echo $preco; ?><p>
                                </div>
                            </a>
                    </div>
            <?php
                    }
                }
            ?>
        </section>

        <!-- CONSTROI O MENU DE PAGINAÇÃO -->
        <?php require_once "templates/pagination.php"; ?>

    </main>

    <!-- IMPORTA O RODAPÉ PADRÃO DO SITE -->
    <?php require "footer.php" ?>

</body>
</html>