<header class="container-fluid row">
    <div id="logo" class="container-fluid col-12 col-md-6">
        <a href="https://www.linkedin.com/in/pedro-luiz-dias-neto-943539252/" 
            style="display: block;">Pedro Luiz Dias Neto</a>
    </div>
    <nav id="main-nav" class="container-fluid col-12 col-md-6">
        <ul class="nav justify-content-center">
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="account.php?link=vender">Vender</a></li>

            <!-- Faz os links de "login" e "minha conta", alternarem de acordo com o estado do usuário -->
            <?php
                if (!isset($_SESSION['id_user'])){ ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Fazer Login</a></li>
                <?php }
                else {
                    ?>
                    <li class="nav-item"><a class="nav-link" href="account.php?link=carrinho">Carrinho</a></li>
                    <li class="nav-item"><a class="nav-link" href="account.php">Minha Conta</a></li>
                    <li class="nav-item"><a class="nav-link" href="sair.php">Sair</a></li>
                <?php
                }
            ?>
        </ul>
    </nav>
    <form id="search" class="form-inline input-group" action="search.php" method="get">
        <input class="form-control" type="text" name="search" placeholder="O que você procura?">
        <div class="input-group-append">
            <input class="btn btn-" type="submit" value="Pesquisar">
        </div>
    </form>
</header>