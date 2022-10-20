<?php

    require_once "funcoes.php";

    class ShopCart {

        protected $shopCart;    // Essa função guarda o objeto JSON que será decodificado do banco de dados.
        protected $list;        // Essa função guarda a lista de produtos, já filtrados, no carrinho.
        protected $userId;      // Guarda o id do usuário.

        public function __construct(int $user_id){

            checkUserIsLogged();

            $this->conn = conexao_db();
            $this->userId = $user_id;
            $this->list = [];

            $queryResult = $this->conn->query("
                select * from carrinho
                where id_user={$user_id};
            ")->fetchAll();

            // Se não houver um objeto JSON armazenando os produtos do carrinho, cria ele.
            if (count($queryResult) === 0){

                $shopCartEncoded = json_encode(["produtos" => []]);
                $this->conn->exec("
                    insert into carrinho (id_user, json_produtos)
                    values ({$this->userId}, '{$shopCartEncoded}');
                ");

                $shopCartDecoded = ["produtos" => []];

            }
            // Se já houver, decodifica ela para um array associativo.
            elseif (count($queryResult) > 0){

                $shopCartDecoded = json_decode($queryResult[0]['json_produtos'], true);

            }

            $this->shopCart = $shopCartDecoded;

        }

        // Essa função retorna a lista de ids de todos os produtos que estão no carrinho.
        public function getProductsIds(){

            return $this->shopCart['produtos'];

        }

        // Essa função retorna a lista de produtos no carrinho, com todos os dados de cada produto.
        public function getProducts(){

            // Prepara a SQL pra buscar um produto de cada vez.
            $pdoStat = $this->conn->prepare("
                select titulo_produto, preco, id_produto, quantidade_estoque
                from produtos_a_venda
                where id_produto=:id;
            ");

            // Essa variável dirá se é necessário atualizar o banco de dados do carrinho ou não,
            // de acordo com as alterações na tabela "produtos_a_venda".
            $update = false;

            // Realiza um loop para buscar cada produto individualmente no carrinho.
            foreach ($this->shopCart['produtos'] as $key => $id){

                $pdoStat->bindParam(":id", $id);
                $pdoStat->execute();

                $prod = $pdoStat->fetchAll();

                // Se o produto não for encontrado na tabela "produtos_a_venda", remove ele do carrinho.
                if (count($prod) === 0){

                    array_splice($this->shopCart['produtos'], $key, 1);
                    $update = true;

                }
                // Senão, adiciona ele à lista de produtos no carrinho.
                else {

                    array_push($this->list, $prod[0]);

                }

            }

            // Faz a atualização no banco de dados "carrinho", se necessário.
            if ($update) {

                // Depois de adicionar o produto ao carrinho, encodifica em JSON e salva no DB.
                $shopCartEncoded = json_encode($this->shopCart);

                $this->conn->exec("
                    update carrinho
                    set json_produtos='{$shopCartEncoded}'
                    where id_user={$_SESSION['id_user']};
                ");

            }

            return $this->list;

        }

        // Função interna que atualizará no banco de dados, as modificações que forem feitas no carrinho.
        protected function updateDB(){

            $shopCartEncoded = json_encode($this->shopCart);
            
            $this->conn->exec("
                update carrinho 
                set id_user={$this->userId}, json_produtos='{$shopCartEncoded}';
            ");                  

        }

        // Essa função serve pra remover todos os produtos do carrinho de compras.;
        public function cleanAllProducts(){

            // Remove os registros do banco de dados.
            $this->conn->exec("
                delete from carrinho
                where id_user={$this->userId};
            ");

            // Limpa as variáveis que armazenam as informações do carrinho.
            $this->shopCart = ["produtos" => []];
            $this->list = [];

        }

        // Adiciona um novo produto ao carrinho. 
        public function addProduct(string $productId){

            // Limpa o dado que foi recebido.
            $productId = limpeza($productId);

            // Verifica se o produto existe no carrinho.
            // Se não existir, aplica ele ao carrinho.
            if (array_search($productId, $this->shopCart['produtos'], true) === false){
                
                array_push($this->shopCart['produtos'], $productId);

                // Atualiza o banco de dados, de acordo com a modificação que foi feita.
                $this->updateDB();     

            }

        }

        // Remove um produto específico do carrinho.
        public function removeProduct(string $productId){

            // Verifica se o produto existe no carrinho.
            // Se existir, remove do carrinho.
            $key = array_search($productId, $this->shopCart['produtos'], true);
            if ($key !== false){
                
                array_splice($this->shopCart['produtos'], $key, 1);

                // Atualiza o banco de dados, de acordo com a modificação que foi feita.
                $this->updateDB();     

            }

        }

    }

?>