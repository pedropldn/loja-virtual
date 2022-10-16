<head>
    <meta charset="utf-8">
    <meta name="author" content="Pedro Luiz">
    <meta name="description" content="Página de cadastro de conta na Market Simulation">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Bem Vindo à Market Simulation</title>

    <!-- CSSs personalizados -->
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/body.css">
    <?php
        $page = limpeza($_SERVER['PHP_SELF']);
        
        if ($page === "/loja-virtual/account.php" &&
            isset($_GET['link']) ) {

            $link = limpeza($_GET['link']);

            switch ($link){
                case "vender":
                case "edit":
                    echo '<link rel="stylesheet" href="css/forms.css">';
                    break;
                case "my_products":
                case "compras":
                case "vendidos":
                case "carrinho":
                    echo '<link rel="stylesheet" href="css/account-products-list.css">';
                    break;
                default:
                    break;

            }

        }

        switch ($page){
            case "/loja-virtual/search.php":
            case "/loja-virtual/index.php":
                echo '<link rel="stylesheet" href="css/products-list.css">';
                break;
            case "/loja-virtual/criar-conta.php":
            case "/loja-virtual/login.php":
                echo '<link rel="stylesheet" href="css/forms.css">';
                break;
            default:
                break;
        }


    ?>
    
    <!-- REQUISITOS PARA O BOOTSTRAP FUNCIONAR -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</head>