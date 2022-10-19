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

        public function save(){

            if (!parent::save()){
                return false;
            }

            // Cria um id para o produto:
            $id = ( limpeza($_SESSION['id_user']) . "_" . (time()) );

            // Prepara o SQL para a aplicação dos dados no banco.
            $stat = $conn->prepare("
                insert into produtos_a_venda (
                    id_user_vendedor, titulo_produto, quantidade_estoque, descricao, preco, id_produto
                )
                values (
                    :id_user_vendedor, :titulo_produto, :quantidade_estoque, :descricao, :preco, :id_produto
                )
            ");

            // Realiza a operação SQL. E se der tudo certo, salva (commit) os dados no DB.
            $stat->bindParam(":id_user_vendedor",$_SESSION['id_user']);
            $stat->bindParam(":titulo_produto", $this->productTitle);
            $stat->bindParam(":quantidade_estoque", $this->quantityInStock);
            $stat->bindParam(":descricao", $this->description);
            $stat->bindParam(":preco" , $this->price);
            $stat->bindParam(":id_produto", $this->productId);
            
            $success1 = $stat->execute();

            // Adiciona as imagens ao DB imagens_produtos
            $stat2 = $conn->prepare("
                insert into imagens_produtos (id_produto, imagem_produto, tipo_imagem)
                values (:id_produto, :imagem_produto, :tipo_imagem);
            ");
            $stat2->bindParam(":id_produto", $this->productId);
            $stat2->bindParam(":imagem_produto", $this->productImage, PDO::PARAM_LOB);
            $stat2->bindParam(":tipo_imagem", $this->productImageType);

            $success2 = $stat2->execute();

            if ($success1 && $success2){
                $this->conn->commit();
                return true;
            }
            else {
                $this->conn->rollBack();
                return false;
            }

        }

    }

?>