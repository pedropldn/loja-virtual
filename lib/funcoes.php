<?php

// Inicia uma conexão com o banco de dados e retorna o objeto PDO.
function conexao_db(){
    $servername_db = "localhost";
    $username_db = "pldnmasteradmin";
    $password_db = "whatwhatcanido";
    $database_db = "lojavirtual";

    $conn = new PDO("mysql:host={$servername_db};dbname={$database_db}", $username_db, $password_db);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    return $conn;
}

function limpeza($a){
    return (htmlspecialchars(stripslashes(trim($a))));
}

// Essa função retorna a mensagem de erro, ou então uma string vazia em caso de validação bem sucedida.
function validacao_email( $email="" ){
    if ($email == ""){
        return "Obrigatório preencher este campo!";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        return "Digite um email válido!";
    }
    else {
        return "";
    }
}

function formatar_preco($preco_db){
    $p_format = str_replace( ".", ",", (string)$preco_db );
    $pos_virg = strpos($p_format, ",");
    if ($pos_virg === false){
        $p_format .= ",00";
    }
    else {
        $p_format .= "00";
        // Fatia a string 2 caracteres após a virgula. (o +3 é por causa dos indices começarem a partir do 0)
        $p_format = substr($p_format, 0, ($pos_virg+3));
    }
    return $p_format;

}

function db_erro($pdo_exception){
    echo "<h1 style='color: red; background: yellow;'>" . $pdo_exception->getMessage() . "</h1>";
}

function buscar_produto( $id_produto = null ){

    // Se não for passado nenhum argumento na função, usa o id_produto do $_GET para fazer a busca.
    // Senão, se o id foi passado como argumento na função, usa esse argumento como id_produto.
    if ($id_produto === null){
        $id_produto = $_GET['id_produto'];
    }

    $id_produto = limpeza($id_produto);

    // Busca os dados do produto no DB. (stat = PDOStatement).
    $conn = conexao_db();
    $stat = $conn->query("
        select 
            u.nome, 
            p.id_user_vendedor, 
            p.id_produto, 
            p.titulo_produto, 
            p.preco, 
            p.descricao, 
            p.quantidade_estoque
        from produtos_a_venda as p
        inner join usuarios as u on p.id_user_vendedor=u.id_user 
        where id_produto='{$id_produto}';
    ");

    return $stat->fetchAll();

}

// Essa função retorna o nome do usuário desejado através do argumento "id_user" que foi passado.
function busca_usuario($id_user){

    $conn = conexao_db();
    return $conn->query("
        select nome from usuarios
        where id_user={$id_user};
    ")->fetchAll()[0]['nome'];

}

// Essa função serve pra removes os ids de produtos que não estão mais à venda;
function limpa_carrinho($carrinho){

    // Esse trecho faz a verificação de quais itens do carrinho não existem mais na tabela "produtos_a_venda".
    // Se não existir, atualiza o carrinho no DB.
    $conn = conexao_db();
    $conn->beginTransaction();
    $stat = $conn->prepare("
        select id_produto from produtos_a_venda
        where id_produto=:id;
    ");

    if (isset($carrinho['produtos'])){

        foreach ($carrinho['produtos'] as $id){

            $stat->bindParam(':id', $id);
            $stat->execute();
            $res = $stat->fetchAll();

            if (count($res) === 0){
                remove_do_carrinho($id, $carrinho);
            }

        }

    }

}

// Busca os produtos que estão no carrinho do usuário e retorna eles como um array associativo.
function busca_carrinho_usuario($id_user){

    $conn = conexao_db();
    $consulta = $conn->query("
        select * from carrinho
        where id_user={$id_user};
    ")->fetchAll();

    if (count($consulta) === 0){
        $json_produto = json_encode(["produtos" => []]);
        $conn->exec("
            insert into carrinho (id_user, json_produtos)
            values ({$id_user}, '{$json_produto}');
        ");

        $carrinho = ["produtos" => []];

    }  
    elseif (count($consulta) !== 0){
        $carrinho = json_decode($consulta[0]['json_produtos'], true);
    }

    limpa_carrinho($carrinho);

    return $carrinho;

}

// Essa função verifica se o produto existe no banco de dados e se ele ainda não foi adicionado ao carrinho.
// Se passar nas validações, adiciona id_produto ao carrinho.
function adiciona_ao_carrinho($id_produto){

    $conn = conexao_db();

    // Consulta o produto no DB pra saber se ele existe, porém não pode ser o produto do próprio
    // usuário que estiver tentando adicionar ao carrinho.
    $produto = $conn->query("
        select id_produto from produtos_a_venda
        where id_produto='{$id_produto}' and (id_user_vendedor<>{$_SESSION['id_user']});
    ")->fetchAll();
    
    // Se o produto não existir, manda o usuario para a 404.
    if (count($produto) === 0){
        // header("Location: 404.php");
        return;
    }

    // Se o produto existe, checa se ele já foi adicionado antes no carrinho.
    $carrinho = busca_carrinho_usuario($_SESSION['id_user']);

    if (array_search($id_produto, $carrinho['produtos'], true) === false){
        
        array_push($carrinho['produtos'], $id_produto);

        // Depois de adicionar o produto ao carrinho, encodifica em JSON e salva no DB.
        $carrinho = json_encode($carrinho);

        $conn->exec("
            update carrinho
            set json_produtos='{$carrinho}'
            where id_user={$_SESSION['id_user']};
        ");

    }

}

// Verifica e valida o id do produto. Se estiver no carrinho, remove ele.
function remove_do_carrinho($id, $carrinho=null){

    $conn = conexao_db();

    if ($carrinho === null){
        $carrinho = busca_carrinho_usuario($_SESSION['id_user']);
    }

    if (count($carrinho['produtos']) === 0){
        header("Location: 404.php");
        return;
    }

    // Busca o indice do id_produto e remove ele do array
    $s = array_search($id, $carrinho['produtos']);
    array_splice($carrinho['produtos'], $s, 1);

    // Depois de remover o produto do array, encodifica em JSON e salva no DB.
    $carrinho = json_encode($carrinho);

    $conn->exec("
        update carrinho
        set json_produtos='{$carrinho}'
        where id_user={$_SESSION['id_user']};
    ");

}

function esvaziar_carrinho($id_user){

    $conn = conexao_db();
    $consulta = $conn->exec("
        delete from carrinho
        where id_user={$id_user};
    ");

}

?>