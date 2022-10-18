<?php
    require_once "lib/funcoes.php";
    require_once "lib/CreateUser.php";

    // A seguinte variável será usada como a página pra onde o usuário será direcionado se o cadastro for bem sucedido.
    $pag_sucesso = "usuario-cadastrado.php";

    //Essa variavel é um contador de quantos dados passaram na validação:
    $validacao = 4;

    $infos = [
        "nome" => "",
        "email" => "",
        "senha" => "",
        "conf_senha" => ""
    ];

    $msg_erros = [
        "nome" => "",
        "email" => "",
        "senha" => "",
        "conf_senha" => "",
        "email_ja_existente" => ""
    ];

    if ($_SERVER['REQUEST_METHOD'] === "POST"){
        
        // Verifica se todos as 4 informações foram submetidas e já faz a limpeza contra XSS.
        if (isset($_POST["nome"])){
            $infos["nome"] = limpeza($_POST["nome"]);
        }
        if (isset($_POST["email"])){
            $infos["email"] = limpeza($_POST["email"]);
        }
        if (isset($_POST["senha"])){
            $infos["senha"] = limpeza($_POST["senha"]);
        }
        if (isset($_POST["conf_senha"])){
            $infos["conf_senha"] = limpeza($_POST["conf_senha"]);
        }

        
        // Validação do campo nome:
        if ($infos["nome"] == ""){
            $mgs_erros["nome"] = "Obrigatório preencher este campo!";
        }
        elseif (!preg_match("@(\s){1,}@", $infos["nome"])){
            $msg_erros["nome"] = "Digite seu nome COMPLETO.";
        }
        elseif (preg_match("@[^a-zA-ZÀ-ú\s]@", $infos["nome"])){
            $msg_erros["nome"] = "Digite apenas letras e espaços!";
        }
        else {
            $validacao--;
        }

        // Chama a função que valida o e-mail.
        // Essa função retorna a mensagem de erro, ou então uma string vazia em caso de validação bem sucedida.
        $msg_erros['email'] = validacao_email($infos['email']);

        if ($msg_erros['email'] === ''){
            $validacao--;
        }

        // Valida a senha:
        if (strlen($infos["senha"]) !== 8){
            $msg_erros["senha"] = "Sua senha precisa conter exatamente 8 caractéres.";
        }
        elseif (preg_match("![^a-zA-Z0-9@]!", $infos["senha"])){
            $msg_erros["senha"] = "A senha pode ter somente letras, números e o sinal de (@).";
        }
        else {
            $validacao--;
        }

        // Valida a repetição da senha:
        if ($infos["senha"] !== $infos["conf_senha"]){
            $msg_erros["conf_senha"] = "As duas senhas PRECISAM ser iguais!";
        }
        else {
            $validacao--;
        }

        if ($validacao === 0){
            
            $user = new CreateUser(
                $infos['nome'],
                $infos['email'],
                $infos['senha']
            );

            if ($user->success()){

                // Se deu tudo certo no cadastro do novo usuário, envia ele para a "usuário-cadastrado.php".
                header("Location: {$pag_sucesso}");

            }
            else {

                $msg_erros["email_ja_existente"] = $user->getErrorMessage();
                
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

    <main class="container">
        <div>
            <div class="text-center">
                <h1>Criar Conta na Market Simulation</h1>
                <p>Preencha os dados abaixo para criar sua conta.</p>
            </div>

            <form action="" method="post">

                <div>
                    <label for="nome">Nome Completo: </label>
                    <input type="text" id="nome" name="nome" placeholder="Fulano da Silva Sauro" required
                            value="<?php echo $infos["nome"] ?>">
                    <label class="css-msg-erro">
                        <?php echo $msg_erros["nome"]; ?>
                    </label>
                </div>

                <div>
                    <label for="email">E-mail: </label>
                    <input type="email" id="email" name="email" placeholder="silva_sauro@gmail.com" required
                            value="<?php echo $infos["email"] ?>">
                    <label class="css-msg-erro">
                        <?php echo $msg_erros["email"]; ?>
                    </label>
                </div>

                <div>
                    <label for="senha">Senha: </label>
                    <input type="password" id="senha" name="senha" placeholder="Exatos 8 caractéres." required
                            maxlength="8" value="<?php echo $infos["senha"] ?>">
                    <label class="css-msg-erro">
                        <?php echo $msg_erros["senha"]; ?>
                    </label>
                </div>

                <div>
                    <label for="conf_senha">Confirmação de Senha: </label>
                    <input type="password" id="conf_senha" name="conf_senha" placeholder="Repita sua senha..." required
                            value="<?php echo $infos["conf_senha"] ?>">
                    <label class="css-msg-erro">
                        <?php echo $msg_erros["conf_senha"]; ?>
                    </label>
                </div>

                <div>
                    <label class="css-msg-erro">
                        <?php echo $msg_erros["email_ja_existente"]; ?>
                    </label>
                </div>

                <input id="submit" class="btn btn-primary" type="submit" name="submit" value="Cadastrar">
            </form>
            
        </div>
    </main>

    <!-- IMPORTA O RODAPÉ PADRÃO DO SITE -->
    <?php require "footer.php" ?>

</body>
</html>