<?php
    
    // Parte do código responsável por remover produtos do carrinho.
    if (isset($_GET['remove_shop_cart'])){
        
        $id = limpeza($_GET['remove_shop_cart']);
        remove_do_carrinho($id);

    }

    $confirmacao = FALSE;

    // Inicia conexão com o DB.
    $conn = conexao_db();

    // Busca pelos "id_produto" que foram colocoados no carrinho deste usuário.
    $carrinho = busca_carrinho_usuario($_SESSION['id_user']);

    if ($_SERVER['REQUEST_METHOD'] === "GET"){

        // Formata os ids para conseguirem se encaixar corretamente na estrutura SQL.
        $ids = implode( "','" , $carrinho['produtos']);

        // Agora busca os dados de cada produto que foi colocado no carrinho.
        $produtos = $conn->query("
            select titulo_produto, preco, id_produto, quantidade_estoque
            from produtos_a_venda
            where id_produto in ('{$ids}')
        ")->fetchAll();

    }

    // Se for a confirmação do produto, Busca os produtos no DB de forma diferente.
    if ($_SERVER['REQUEST_METHOD'] === "POST"){

        $produtos = [];

        $stat = $conn->prepare("
            select titulo_produto, id_produto, preco, quantidade_estoque
            from produtos_a_venda
            where id_produto=:id;
        ");

        foreach ($_POST as $k => $v){
            
            if(array_search($k, $carrinho['produtos']) !== FALSE){

                if (!is_numeric($v)){
                    header("Location: 404.php");
                }

                $id = limpeza($k);
                $quant = (int)floor((float)limpeza($v));

                if ($quant < 0){
                    continue;
                }

                $stat->bindParam(":id", $id);
                $stat->execute();
                $prod = $stat->fetchAll();

                if (count($prod) === 1){

                    // Adiciona a quantidade à ser comprada ao array com os dados do produto.
                    $prod[0]['quant'] = $quant;

                    array_push($produtos, $prod[0]);

                }

            }

        }

        foreach ($produtos as $p){

            // Verifica se a quantidade à ser comprada é um número válido em relação ao estoque.
            if ($p['quant'] > $p['quantidade_estoque'] || $p['quant'] < 0){
                
                $confirmacao = FALSE;
                //header("Location: 404.php");

            }
            else {

                $confirmacao = TRUE;

            }

        }

    }


    if (count($produtos) === 0){ ?>

        <h1>Seu carrinho está vazio!</h1>
    
    <?php
    }
    elseif ($confirmacao === FALSE) { 
        require "processos/carrinho-false.php";
    } 
    else {
        require "processos/carrinho-true.php";
    }
    
?>
