<?php

    require_once "funcoes.php";
    require_once "Product.php";

    class CreateProduct extends Product {

        public function __construct(int $id_user){

            parent::__construct();

            $this->sellerUserId = $id_user;
            
            // Constrói o "id_produto" automaticamente.
            $this->productId = cria_id_produto($id_user);

        }

        // Tenta configurar o título do produto, se der tudo certo retorna "true",
        // caso contrário retorna uma string com a mensagem de erro.
        public function setProductTitle($productTitle){

            if (strlen($dados_produto['titulo_produto']) > 80){
                return "O título do produto NÃO pode ser maior que 80 caracteres!";
            }
            elseif ((!empty($dados_produto['titulo_produto'])) && strlen($dados_produto['titulo_produto']) <= 80){
                return true;
            }

        }

    }

?>