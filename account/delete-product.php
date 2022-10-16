<?php

$produto = buscar_produto();

    if ($_SERVER['REQUEST_METHOD'] === "POST"){

        if (isset($_POST['deletar_produto'], $_POST['id_produto'])){   

            if ($_POST['deletar_produto'] === "Excluir Produto"){
                
                $id_produto = limpeza($_POST['id_produto']);
                $id_user = $_SESSION['id_user'];

                try {

                    // Se o usuario for o verdadeiro dono do produto, exclui o produto do DB.
                    $conn = conexao_db();
                    $conn->beginTransaction();
                    $rows = $conn->exec("
                        delete from produtos_a_venda
                        where id_produto='{$id_produto}' and id_user_vendedor={$id_user};
                    ");
                    
                    if ($rows !== 1){
                        header("Location: 404.php");
                    }
                    else {

                        // Se ninguém comprou esse produto, exclui as imagens do DB.
                        $stat = $conn->query("
                            select id_produto from historico_compra_venda
                            where id_produto='{$id_produto}'
                        ")->fetchAll();
 
                        if (count($stat) === 0){
                            $conn->exec("
                                delete from imagens_produtos
                                where id_produto='{$id_produto}';
                            ");
                        }

                    $conn->commit();
                    
                        header("Location: ?link=my_products");
                    }
                
                }
                catch (PDOExcetion $e){
                    db_erro($e);
                }

            }

        }

    }

?>
<section class="container col-12 col-md-9 col-lg-10">
    <div class="col-12 text-center">
        <h1>Você tem certeza que deseja excluir o produto abaixo? </h1>
    </div>

    <div class="container row col-12" style="padding-top: 2em;">
        <div class="col-12 col-md-8">
            <h1><?php echo $produto[0]['titulo_produto'] ?></h1>
            
            <div>
                <h4>R$ <?php echo formatar_preco($produto[0]['preco']); ?></h4>
                <p>Quantidade disponível: <?php echo $produto[0]['quantidade_estoque']; ?></p>
            </div>
        </div>
        <figure class="col-12 col-md-4">
            <figcaption style="display: none;"><?php echo $produto[0]['titulo_produto']; ?></figcaption>
            <img src="imagem-produto.php?id_produto=<?php echo $produto[0]['id_produto']; ?>"
                alt="<?php echo $produto[0]['titulo_produto']; ?>"
                width="200" height="150"
            >
        </figure>
    <div class="col-12">
        <h2>Descrição do Produto: </h2>
        <div><?php echo $produto[0]['descricao']; ?></div>
    </div>

    <form class="col-12" action="" method="post">
        
        <!-- Não modifique o "value" do input abaixo, pois ele é necessário para a validação! -->
        <input class="btn btn-danger" type="submit" name="deletar_produto" value="Excluir Produto">

        <input type="hidden" name="id_produto" value="<?php echo limpeza($_GET['id_produto']); ?>">
        <a class="btn btn-info" href="?link=my_products">Cancelar</a>
    </form>