<?php

    require_once "Product.php";

    class GetProduct extends Product {

        protected $errorMessage;

        public function __construct($product_id){

             parent::__construct();

            $queryResult = $this->conn->query("
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
                where id_produto='{$product_id}';
             ")->fetchAll();

             if (count($queryResult) === 0){
                $this->errorMessage = "Esse produto não existe no estoque!";
             }
             elseif (count($queryResult) > 1){

                throw new Exception("ID do produto está duplicado no banco de dados!!!");
                exit;

             }
             else {

                $this->productId = $queryResult[0]['id_produto'];
                $this->sellerUserId = $queryResult[0]['id_user_vendedor'];
                $this->productTitle  = $queryResult[0]['titulo_produto'];
                $this->quantityInStock = $queryResult[0]['quantidade_estoque'];
                $this->price = $queryResult[0]['preco'];
                $this->description = $queryResult[0]['descricao'];
                $this->sellerName = $queryResult[0]['nome'];

             }

        }

        public function getSellerName(){
            return $this->sellerName;
        }

    }

?>