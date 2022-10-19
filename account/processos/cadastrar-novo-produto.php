<?php

require_once "lib/CreateProduct.php";

// Loop para limpeza e verificação de campos vazios recebidos pelo metodo post.
foreach ($dados_produto as $key => $value){
    if (isset($_POST[$key])){
        $dados_produto[$key] = limpeza($_POST[$key]);
        if (empty($dados_produto[$key])){
            $msgs_erros[$key] = "Obrigatório preencher este campo";
        }
    }
}

// Verifica se o usuario está logado, para evitar problemas na hora de aplicar os dados so DB.
if (!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit;
}

$product = new CreateProduct((int)$_SESSION['id_user']);

$msgs_erros['quantidade'] = $product->setProductTitle($dados_produto['titulo_produto']);
$msgs_erros['quantidade'] = $product->setQuantityInStock($dados_produto['quantidade']);
$msgs_erros['preco'] = $product->setPrice($dados_produto['preco']);
$msgs_erros['descricao'] = $product->setDescription($dados_produto['descricao']);
$msgs_erros['imagem'] = $product->setImage("imagem");
 
// Se deu tudo certo nas validações, aplica os dados ao DB.
if ($product->save()){

    header("Location: ?link=my_products");

}
?>