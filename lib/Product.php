<?php

    require_once "funcoes.php";

    // Essa classe representa a estrutura básica de um produto.
    abstract class Product {

        protected $conn; 

        protected $productId;
        protected $sellerUserId;
        protected $productTitle = null;
        protected $quantityInStock = null;
        protected $price = null;
        protected $description = null;

        protected function __construct(){

            $this->conn = conexao_db();

        }

    // ================================================================
        // As seguintes funções são específicas para acessar cada informação do produto.
        public function getProductId(){
            return $this->productId;
        }
        public function getSellerUserId(){
            return $this->sellerUserId;
        }
        public function getProductTitle(){
            return $this->productTitle;
        }
        public function getQuantityInStock(){
            return $this->quantityInStock;
        }
        public function getPrice(){
            return $this->price;
        }
        public function getDescription(){
            return $this->description;
        }
    // ================================================================

        // As seguintes funções servirão para criar ou editar cada informação do produto,
        // e deverão ser implementadas nas definições das classes filhas.
        abstract protected function setProductTitle();
        abstract protected function setQuantityInStock();
        abstract protected function setPrice();
        abstract protected function setDescription();

    }

?>