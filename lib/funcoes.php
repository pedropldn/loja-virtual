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

// Essa função retorna o nome do usuário desejado através do argumento "id_user" que foi passado.
// Se não encontrar o usuário no banco de dados, lança uma Exceção.
function busca_usuario($id_user){

    $conn = conexao_db();
    $queryResult = $conn->query("
        select nome from usuarios
        where id_user={$id_user};
    ")->fetchAll();
    
    if (count($queryResult) === 0){

        throw new Exception("Usuário não existe no banco de dados! (function busca_usuario() -> funcoes.php)");

    }
    else {

        return $queryResult[0]['nome'];

    }

}

// Essa função verifica se o usuário está logado! Se não estiver, destrói a sessão e o manda para
// a página de login.
function checkUserIsLogged(){

    if (!isset($_SESSION['id_user'])){
        session_unset();
        session_destroy();
        header("Location: login.php");
    }

}

?>