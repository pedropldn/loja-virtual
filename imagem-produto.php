<?php

    require "lib/funcoes.php";

    if (isset($_GET['id_produto']) && (count($_GET) === 1)){

        $id_produto = (limpeza($_GET['id_produto']));

        try{

            // Busca a imagem no DB. (stat = PDOStatement).
            $conn = conexao_db();
            $stat = $conn->query("
                select imagem_produto, tipo_imagem from imagens_produtos
                where id_produto='{$id_produto}';
            ");

            $res = $stat->fetchAll();
            header("Content-type: image/{$res[0]['tipo_imagem']}");
            echo $res[0]['imagem_produto'];

        }
        catch (PDOException $e){
            // função da lib "funcoes.php"
            db_erro($e);
        }

    }
    else {
        header("Location: index.php");
    }

?>