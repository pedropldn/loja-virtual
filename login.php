<?php

    // Verifica se o usuário já está logado. Se estiver, envia ele direto para a conta.
    session_start();
    if (isset($_SESSION['id_user'])){
        header("Location: account.php");
    }
    session_unset();
    session_destroy();

    require "lib/funcoes.php";
    $infos = [
        "email" => "",
        "senha" => ""
    ];

    $msg_erros = [
        "email" => "",
        "senha" => ""
    ];

    if ($_SERVER['REQUEST_METHOD'] === "POST"){

        if (isset($_POST["email"])){
            $infos["email"] = limpeza($_POST["email"]);
        }
        if (isset($_POST["senha"])){
            $infos["senha"] = limpeza($_POST["senha"]);
        }

        $msg_erros['email'] = validacao_email($infos['email']);
        echo $msg_erros['email'];
        // Se o email for valido, procura ele no banco de dados.
        if ($msg_erros['email'] === ''){
            
            try {
                
                // Inicia a conexão com o DB.
                $conn = conexao_db();
                
                // Verifica se o email digitado existe na tabela de usuarios.
                $check_email = "select email, senha, id_user, nome from usuarios
                                where email=\"{$infos['email']}\";";
                $pdostat = $conn->query($check_email);
                $res = $pdostat->fetchAll();
                
                
                if (count($res) === 0){
                    $msg_erros["email"] = "Esse e-email não possui conta cadastrada!";
                }
                elseif ($infos['senha'] !== $res[0]['senha']) {
                    $msg_erros['senha'] = "Senha incorreta!!!";
                }
                else {
                    // Inicia um session para este usuário.
                    session_start();
                    $_SESSION['id_user'] = $res[0]['id_user'];
                    $_SESSION['nome'] = $res[0]['nome'];

                    if (isset($_POST['comprar'])){

                        $id_produto = limpeza($_POST['comprar']);
                        header("Location: comprar.php?id_produto={$id_produto}");

                    }
                    else {

                        header("Location: account.php");
                        
                    }
                }

            }
            catch (PDOException $e){
                echo "<h1 style='color: red; background yellow;'>" . $e->getMessage() . "</h1>";
            }

        }

    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<?php require_once "templates/head.php"; ?>
<body>
    <!-- IMPORTA O CABEÇALHO PADRÃO DO SITE -->
    <?php require "header.php" ?>

    <main class="container-fluid text-center text-capitalize">
        <h1>Bem vindo! Digite seu e-mail e senha para entrar!</h1>
        <form action="login.php" method="post">

            <div class="container row col-12 css-input-box">

                <label class="col-12 col-md-4 text-center" for="email">E-mail: </label>

                <div class="col-12 col-md-8">
                    <input type="email" id="email" name="email" required placeholder="silvasauro@gmail.com"
                            value="<?php echo $infos['email']; ?>">
                    <label>
                        <?php echo $msg_erros['email'] ?>
                    </label>
                </div>

            </div>

            <div class="container row col-12 css-input-box">

                <label class="col-12 col-md-4 text-center" for="senha">Senha: </label>

                <div class="col-12 col-md-8">
                    <input type="password" id="senha" name="senha" required placeholder="Sua senha aqui...">
                    <label>
                        <?php echo $msg_erros['senha'] ?>
                    </label>
                </div>

            </div>

            <?php 
                if (isset($_GET['buy'])){ ?>

                    <input type="hidden" name="comprar" value="<?php echo limpeza($_GET['buy']); ?>">

                <?php }
            ?>

            <div class="container">
                <input id="submit" class="btn btn-primary" type="submit" value="Entrar">
            </div>
            
            <div class="container">
                <p><a href="criar-conta.php">Criar uma Conta!</a></p>
            </div>

        </form>
    </main>

    <!-- IMPORTA O RODAPÉ PADRÃO DO SITE -->
    <?php require "footer.php" ?>

</body>
</html>