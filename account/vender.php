<?php 

    $dados_produto = [
        "titulo_produto" => "",
        "quantidade" => "",
        "preco" => "",
        "descricao" => ""
    ];

    $msgs_erros = [
        "titulo_produto" => "",
        "quantidade" => "",
        "preco" => "",
        "descricao" => "",
        "imagem" => ""
    ];
    
    
    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['submit'])){

        $imagem = "Imagem do Produto: ";
        $submit = limpeza($_POST['submit']);

        if ($submit === "Cadastrar Produto"){
            require "processos/cadastrar-novo-produto.php";
        }
        elseif ($submit === "Salvar Alterações"){
            require "processos/edit-product.php";
        }

    }
    elseif ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['link'])){

        // Se o usuário for editar as informações do produto, é necessário carregar as informações existentes no banco de dados.
        if ($_GET['link'] === "edit" && isset($_GET['id_produto'])){

            $imagem = "Trocar imagem do produto: ";
            require "processos/edit-product.php";
        
        }
        elseif ($_GET['link'] === "vender"){
            
            $imagem = "Imagem do Produto: ";

        }

    }
    else {
        header("Location: 404.php");
    }
?>
<?php require "processos/form-product.php"; ?>

