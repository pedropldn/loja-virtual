<?php

    require "lib/funcoes.php";
    session_start();

    // DOMINIO
    $domain = "https://programadorpedroluiz.epizy.com/";

    // verifica se o usuário está logado. Se não estivar, manda ele pra página de login.
    checkUserIsLogged();
    
    // Formata o nome do usuario pra aparecer apenas o primeiro nome.
    $nome = limpeza($_SESSION['nome']);
    $pos = strpos($nome, " ");
    $primeiro_nome_usuario = substr($nome, 0, $pos);

    // Faz a escolha de qual página será carregada de acordo com a variavel link do GET.
    if (count($_GET) === 0){
        $pagina = "home";
    }
    elseif (isset($_GET['link'])){
        $pagina = limpeza($_GET['link']);
    }
    elseif (isset($_GET['link'], $_GET['id_produto'])){
        $pagina = limpeza($_GET['link']);
        $id_produto = limpeza($_GET['id_produto']);
    }
    else {
        $pagina = false;
    }

?>
<!DOCTYPE html>
<html lang="pt-br">
<?php require_once "templates/head.php"; ?>
<body>
    <!-- IMPORTA O CABEÇALHO PADRÃO DO SITE -->
    <?php require_once "header.php"; ?>

    <main class="container row col-12">
        <?php require_once "account/account-navigation.php"; ?>
        <?php
            
            // bloco switch que faz a inserção da página específica
            switch ($pagina){
                case "home":
                    require "account/home.php";
                    break;
                case "my_products":
                    require "account/my-products.php";
                    break;
                case "edit":
                case "vender":
                    require "account/vender.php";                 
                    break;
                case "delete":
                    require "account/delete-product.php";
                    break;
                case "compras":
                    require "account/minhas-compras.php";
                    break;
                case "vendidos":
                    require "account/vendidos.php";
                    break;
                case "carrinho":
                    require "account/carrinho.php";
                    break;
                default:
                    require "404.php";
            }           

        ?>
    </main>

    <!-- IMPORTA O RODAPÉ PADRÃO DO SITE -->
    <?php require "footer.php" ?>

</body>
</html>