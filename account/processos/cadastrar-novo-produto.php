<?php
// Essa variável é o countador para saber se todos os dados do produto são válidos.
$validacao_produto = 6;

// Loop para limpeza e verificação de campos vazios recebidos pelo metodo post.
foreach ($dados_produto as $key => $value){
    if (isset($_POST[$key])){
        $dados_produto[$key] = limpeza($_POST[$key]);
        if (empty($dados_produto[$key])){
            $msgs_erros[$key] = "Obrigatório preencher este campo";
        }
    }
}

// Validação do título do produto.
if (strlen($dados_produto['titulo_produto']) > 80){
    $msgs_erros['titulo_produto'] = "O título do produto NÃO pode ser maior que 80 caracteres!";
}
elseif ((!empty($dados_produto['titulo_produto'])) && strlen($dados_produto['titulo_produto']) <= 80){
    $validacao_produto--;
}

// Validação da quantidade de produtos disponível no estoque.
if (validacao_quantidade($dados_produto, $msgs_erros)){
    $validacao_produto--;
}

if (validacao_preco($dados_produto, $msgs_erros)){
    $validacao_produto--;
}

// Validação da descrição do produto.
if (!empty($dados_produto['descricao']) && (strlen($dados_produto['descricao']) <= 2000)){
    $validacao_produto--;
}
 
// Validação da imagem de upload do produto!
// A função validacao_imagem() depende do array $msg_erros pra funcionar, caso contrário dará erro.
// Se der tudo certo na validacao ela retornará true;
$arquivo_imagem = validacao_imagem($msgs_erros);
if (isset($_FILES['imagem']) && $arquivo_imagem){
    $validacao_produto--;
    $tipo_imagem = strtolower(pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION));
}

// Verifica se o usuario está logado, para evitar problemas na hora de aplicar os dados so DB.
if (isset($_SESSION['id_user'])){
    $validacao_produto--;
}

// Se deu tudo certo nas validações, aplica os dados ao DB.
if ($validacao_produto === 0){
    
    try {

        // Inicia a conexão com o DB.
        $conn = conexao_db();
        $conn->beginTransaction();

        // Cria um id para o produto:
        $id = cria_id_produto($_SESSION['id_user']);

        // Prepara o SQL para a aplicação dos dados no banco.
        $stat = $conn->prepare("insert into produtos_a_venda (id_user_vendedor, titulo_produto, quantidade_estoque, 
                                                                    descricao, preco, id_produto)
                                    values (
                                        :id_user_vendedor,
                                        :titulo_produto,
                                        :quantidade_estoque,
                                        :descricao,
                                        :preco,
                                        :id_produto
                                    )");

        // Realiza a operação SQL. E se der tudo certo, salva (commit) os dados no DB.
        $stat->bindParam(":id_user_vendedor",$_SESSION['id_user']);
        $stat->bindParam(":titulo_produto", $dados_produto['titulo_produto']);
        $stat->bindParam(":quantidade_estoque", $dados_produto['quantidade']);
        $stat->bindParam(":descricao", $dados_produto['descricao']);
        $stat->bindParam(":preco" , $dados_produto['preco']);
        $stat->bindParam(":id_produto", $id);
        $stat->execute();

        // Adiciona as imagens ao DB imagens_produtos
        $stat2 = $conn->prepare("
            insert into imagens_produtos (id_produto, imagem_produto, tipo_imagem)
            values (:id_produto, :imagem_produto, :tipo_imagem);
        ");
        $stat2->bindParam(":id_produto", $id);
        $stat2->bindParam(":imagem_produto", $arquivo_imagem, PDO::PARAM_LOB);
        $stat2->bindParam(":tipo_imagem", $tipo_imagem);
        $stat2->execute();

        $conn->commit();

        header("Location: ?link=my_products");

    }
    catch (PDOException $e){
        echo "<h1 style='color: red; background yellow;'>" . $e->getMessage() . "</h1>";
    }

}
?>