<?php

    require "lib/funcoes.php";

    // Mensagem que serve pra dizer se o quantidade desejada é inválida.
    $msg_quant = "";

    // Variavel pra determinar a configuração da página: se é confirmação da compra ou a escolha da quantidade.
    $confirmacao = FALSE;

    session_start();
    if (isset($_GET['id_produto'])){
        
        $id_produto = limpeza($_GET['id_produto']);

        if (!isset($_SESSION['id_user'])){
            header("Location: login.php?buy={$id_produto}");
        }
        else{
            
            // Busca os dados do produto no DB.
            $produto = buscar_produto();

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['quantidade'], $_POST['submit'])) ){

                // Faz a validação da quantidade à ser comprada!
                $quant = limpeza($_POST['quantidade']);
                if (is_numeric($quant)){

                    $quant = floor((float)$quant);
                    if ($quant > 0 && $quant <= $produto[0]['quantidade_estoque']){
                        
                        // Calcula o preço total.
                        $preco = $produto[0]['preco'] * $quant;
                        
                        // Ir para a pagina de confirmação da compra.
                        $confirmacao = TRUE;

                    }

                }
                else {
                    $msg_quant = "Valor Inválido";
                }

            }

        }

    }
    else {
        header("Location: 404.php");
    }

    

?>
<!DOCTYPE html>
<html lang="pt-br">
<?php require_once "templates/head.php"; ?>
<body>
    <!-- IMPORTA O CABEÇALHO PADRÃO DO SITE -->
    <?php require "header.php" ?>

    <main class="container row">
    
        <?php 
        if($confirmacao === FALSE){ ?>

            <div class="container col-12 col-md-6">
                <section>
                    <h1><?php echo $produto[0]['titulo_produto']; ?></h1>               
                    <h4>R$ <?php echo formatar_preco($produto[0]['preco']); ?></h4>
                    <p>Vendedor: <?php echo $produto[0]['nome']; ?></p>
                </section>
                <form method="post" action="">
                    <label>Deseja comprar quantas unidades: </label>
                    <input type="number" name="quantidade" value="1" min="1" max="<?php echo $produto[0]['quantidade_estoque'] ?>">

                    <!-- Produz a mensagem de "valor inválido" durante a validação da quantidade -->
                    <label><?php echo $msg_quant; ?></label><br>

                    <input type="submit" name="submit" value="Comprar">
                </form> 
            </div>
            <figure class="col-12 col-md-6">
                <figcaption style="display: none;"><?php echo $produto[0]['titulo_produto']; ?></figcaption>
                <img src="imagem-produto.php?id_produto=<?php echo $produto[0]['id_produto']; ?>"
                    alt="<?php echo $produto[0]['titulo_produto']; ?>"
                    style="max-width: 100%; max-height: 500px;"
                >
            </figure>

        <?php 
        }
        elseif ($confirmacao === TRUE) { ?>
            <div class="col-12 col-sm-6">
                <section>
                    <h1>Confirmar Compra: </h1>
                    <h2><?php echo $produto[0]['titulo_produto'] ?></h2>
                    <h4>Preço total: R$ <?php echo formatar_preco($preco); ?></h4>
                    <p>Vendedor: <?php echo $produto[0]['nome']; ?></p>
                </section>
                <form method="post" action="account/processos/confirmar-compra.php">
                    <label>Você vai comprar <strong><?php echo $quant; ?></strong> unidades</label>
                    <input type="submit" name="submit" value="Confirmar Compra">

                    <!-- GUARDA O ID E A QUANTIDADE DE PRODUTOS À SEREM COMPRADOS -->
                    <input type="hidden" name="quantidade_comprada" value="<?php echo $quant; ?>">
                    <input type="hidden" name="id_produto" value="<?php echo $produto[0]['id_produto']; ?>">

                </form>
            </div>
            <figure class="col-12 col-sm-6">
                <figcaption style="display: none;"><?php echo $produto[0]['titulo_produto']; ?></figcaption>
                <img src="imagem-produto.php?id_produto=<?php echo $produto[0]['id_produto']; ?>"
                    alt="<?php echo $produto[0]['titulo_produto']; ?>"
                    style="max-width: 100%; max-height: 500px;"
                >
            </figure>

        <?php 
        } ?>

    </main>

    <!-- IMPORTA O RODAPÉ PADRÃO DO SITE -->
    <?php require "footer.php" ?>

</body>
</html>