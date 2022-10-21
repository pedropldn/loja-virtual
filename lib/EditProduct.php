<?php
    
    require_once "funcoes.php";
    require_once "Product.php";

    // Essa classe é responsável por fazer a edição dos dados de um produto
    // que já está cadastrado na loja virtual.
    class EditProduct extends Product {

        public function __construct($product_id){

            parent::__construct();

            // Busca os dados do produto no DB.
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

            // Se mais de um produto for retornado através do mesmo "productId", lança uma exceção.
            if (count($queryResult) > 1){

                throw new Exception("ID do produto está duplicado no banco de dados!!!");
                exit;

            }

            // Guarda cada informação do produto em suas propriedade correspondentes da classe.
            $this->productId = $queryResult[0]['id_produto'];
            $this->sellerUserId = $queryResult[0]['id_user_vendedor'];
            $this->productTitle  = $queryResult[0]['titulo_produto'];
            $this->quantityInStock = $queryResult[0]['quantidade_estoque'];
            $this->price = $queryResult[0]['preco'];
            $this->description = $queryResult[0]['descricao'];

        }

        // Esse método é responsável por salvar no banco de dados, as alterações que forem feitas
        // no objeto EditProduct.
        public function save(){

            if (!parent::save()){
                return false;
            }

            // Prepara o SQL para a aplicação dos dados no banco.
            $stat = $this->conn->prepare("
                update produtos_a_venda 
                set 
                    id_user_vendedor=:id_user_vendedor, 
                    titulo_produto=:titulo_produto, 
                    quantidade_estoque=:quantidade_estoque, 
                    descricao=:descricao, 
                    preco=:preco
                where id_produto='{$this->productId}';
            ");

            // Realiza a operação SQL. E se der tudo certo, salva (commit) os dados no DB.
            $stat->bindParam(":id_user_vendedor",$_SESSION['id_user']);
            $stat->bindParam(":titulo_produto", $this->productTitle);
            $stat->bindParam(":quantidade_estoque", $this->quantityInStock);
            $stat->bindParam(":descricao", $this->description);
            $stat->bindParam(":preco" , $this->price);
            
            $success1 = $stat->execute();
            $success2 = true;

            if (
                $this->productImage !== true &&
                $this->productImageType !== true
            ){

                // Adiciona as imagens ao DB imagens_produtos
                $stat2 = $this->conn->prepare("
                    update imagens_produtos
                    set imagem_produto=:imagem_produto, tipo_imagem=:tipo_imagem
                    where id_produto='{$this->productId}';
                ");
                $stat2->bindParam(":imagem_produto", $this->productImage, PDO::PARAM_LOB);
                $stat2->bindParam(":tipo_imagem", $this->productImageType);
                $success2 = $stat2->execute();

            }

            if ($success1 && $success2){
                $this->conn->commit();
                return true;
            }
            else {
                $this->conn->rollBack();
                return false;
            }

        }

    // ====================================================================

        // Substitui a imagem anterior por uma nova imagem.
        // Se essa função recebe o argumento NULL, mantem a imagem anterior sem alterá-la. 
        public function setImage($imageFieldName){

            // Se o usuário não inseriu uma nova imagem, prepara para manter a mesma no banco de dados.
            if ($imageFieldName === null){

                $this->productImage = true;
                $this->productImageType = true;
                return "";

            }
            // Porém, se uma nova imagem for inserida, chama o "método pai" da classe, para a validação da imagem.
            else {

                parent::setImage($imageFieldName);

            }

        }

    }

?>