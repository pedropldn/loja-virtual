<?php

    require_once "funcoes.php";

    // Essa classe representa a estrutura básica de um produto.
    abstract class Product {

        protected $conn;            // Guarda um PDO de conexão com o banco de dados

        // Cada uma dessas variáveis irá guardar as informações correspondentes do produto.
        protected $productId = null;        
        protected $sellerUserId = null;
        protected $productTitle = null;
        protected $quantityInStock = null;
        protected $price = null;
        protected $description = null;

        protected $productImage = null;     // Guarda o arquivo de imagem se será aberto pelo fopen().
        protected $productImageType = null; // Guarda o tipo da imagem (png, jpg, webp, etc)

        protected function __construct(){

            $this->conn = conexao_db();

        }

        // Esse método é a parte abstrata das funções filhas que serão responsáveis por
        // alterar as informações do produto diretamente no banco de dados.
        protected function save(){

            if (
                $this->checkAllData() == false ||
                $this->productImage == false ||
                $this->productImageType == false
            ){
                return false;
            }

            // Inicia uma transação explícita no banco de dados.
            $this->conn->beginTransaction();
            return true;

        }

    // O método abaixo faz a verificação de todas as informações do produto para
    // saber se ele pode ser salvo no banco de dados ou não.
    protected function checkAllData(){

        if (
            $this->productId &&
            $this->sellerUserId &&
            $this->productTitle && 
            $this->quantityInStock &&
            $this->price &&
            $this->description
        ){

            return true;

        }
        else { 
            
            return false;
        
        }

    }

    // ================================================================
        // Os seguintes métodos são específicos para acessar cada informação do produto.
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

        // Tenta configurar o título do produto, se der tudo certo retorna uma string vazia,
        // caso contrário retorna uma string com a mensagem de erro.
        public function setProductTitle($productTitle){

            if (strlen($productTitle) > 80){
                return "O título do produto NÃO pode ser maior que 80 caracteres!";
            }
            elseif ((!empty($productTitle)) && strlen($productTitle) <= 80){
                $this->productTitle = $productTitle;
                return "";
            }

        }

        // Tenta configurar a quantidade de produtos em estoque, se der tudo certo retorna uma 
        // string vazia, caso contrário retorna uma string com a mensagem de erro.
        public function setQuantityInStock($quant){

            // Validação da quantidade de produtos disponível no estoque.
            $quant = (int)$quant;

            if (
                ($quant != 0) && 
                (!is_nan($quant)) &&
                ($quant > 0) &&
                ($quant <= 99)
            ){
                
                $this->quantityInStock = $quant;
                return "";
                
            }
            elseif (!empty($quant)){
                return "A quantidade em estoque precisa ser um número inteiro entre 1 e 99";
            }

            // Se chegar aqui, é porque o campo não foi preenchido.
            return "Obrigatório preencher este campo!";

        }

        // Tenta configurar o preço do produto, se der tudo certo retorna uma 
        // string vazia, caso contrário retorna uma string com a mensagem de erro.
        public function setPrice($price){

            // Validação do preço do produto.
            if (preg_match("@[^0-9,.]@", $price)){
                return "Digite apenas números e virgula/ponto (para digitar centavos!)";
            }
            elseif (preg_match("@^[0-9]{1,5}[\.,][0-9]{1,2}$@", $price)){
                
                // Substitui virgula por ponto antes de validar e converter.
                $preco = str_replace(",", ".", $price);

                // Tenta converter o preço para um valor numérico.
                if (is_numeric($preco)){
                    $this->price = (float)$preco;
                    return "";
                }
                else {
                    return "Esse NÃO é um preço válido!";
                }

            }
            else {
                return "Preço inválido! Digite um preço entre R$ 0,01 e 99999,99! Use apenas 1 virgula ou ponto!";
            }

            // Se chegar aqui é porque não passou na validação.
            return "Preço inválido!";

        }

        // Tenta configurar a descrição do produto, se der tudo certo retorna uma 
        // string vazia, caso contrário retorna uma string com a mensagem de erro.
        public function setDescription($desc){

            // Validação da descrição do produto.
            if (!empty($desc) && (strlen($desc) <= 2000)){
                
                $this->description = $desc;
                return "";
                
            }
            else {

                return "Obrigatório preencher este campo!";

            }

        }

        // Essa função valida a imagem que foi passada no formulário, então DEPENDE OBRIGATORIAMENTE
        // que o atributo "name" do input da imagem seja passado como argumento, se der tudo certo retorna uma 
        // string vazia, caso contrário retorna uma string com a mensagem de erro.
        public function setImage($imageFieldName){

            $imageType = strtolower(pathinfo($_FILES[$imageFieldName]['name'], PATHINFO_EXTENSION));
            
            switch ($imageType){
                case "png":
                case "jpg":
                case "jpeg": 
                case "gif":
                case "webp":
                    break;
                default:
                    return "Formato de imagem inválido!";
            }
        
            // Faz a tentativa de abrir o conteúdo da imagem.
            // Faz a leitura do arquivo de imagem e salva o conteúdo dentro de uma variável.            
            $imageFile = fopen($_FILES[$imageFieldName]['tmp_name'], "rb");
            if($imageFile !== false){
    
                // Se o PHP conseguiu abrir a imagem valida o tamanho dela.
                if ($_FILES[$imageFieldName]['size'] < 800000 && 
                $_FILES[$imageFieldName]['size'] > 0){

                    $this->productImage = $imageFile;
                    $this->productImageType = $imageType;
                    return "";

                }
                else {

                    return "A imagem deve ser de máximo 1Mb.";

                }
    
            }
            else {
                return "Um erro aconteceu enquanto tentavamos salvar a imagem. Tente novamente.";
            }
    
            // Se o código chegar aqui é porque a imagem não foi validada corretamente.
            return "Algum erro ocorreu na validação da imagem! Entre em contato com a equipe de programação!";

        }

    }

?>