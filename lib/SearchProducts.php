<?php 

// Importa a lib de funções.
require_once "funcoes.php";

// Essa classe é responsável por fazer a busca de vários produtos ao mesmo tempo no banco de dados.
// Serve especialmente para as páginas index.php e search.php;
class SearchProducts {

    protected $conn;                    // Guarda PDO (conexão com o DB)
    protected $strSearch;               // Guarda a string de busca do produto
    protected $returnedProducts;        // Guarda os produtos retornados após a execução da consulta SQL.
    protected $numberProductsReturned;  // Guarda a quantidade de produtos retornados pelo método "fetchAll".

    // Função básica de inicialização do objeto SearchProducts
    public function __construct($search){

        $this->strSearch = limpeza($search); // Faz a limpeza da string de busca

        $this->conn = conexao_db();          // Cria um PDO de conexão com o DB.

        $this->returnedProducts = $this->conn->query("
            select * from produtos_a_venda
            where titulo_produto like '%{$this->strSearch}%';
        ")->fetchAll();

        // Conta quantos produtos foram retornados no total.
        $this->numberProductsReturned = count(
            $this->returnedProducts
        );

    }

    // Essa função retorna os produtos de acordo com cada página que o usuário avançar.
    // Cada página terá 10 produtos, então a página 1 retorna os produtos do índice 0 até o 9,
    // a página 2 retorna os produtos do índice 10 até o 19... e assim por diante.
    public function getPage(int $page_number=1){

        $minIndex = ($page_number * 12) - 12;   // Calcula o índice MÍNIMO do produto de acordo com a página.
        $maxIndex = ($page_number * 12) - 12;    // Calcula o índice MÁXIMO do produto de acordo com a página.

        // Se nenhum produto foi encontrado ou se o índice mínimo do produto for menor que o total
        // de produtos, ou se o número da página for "0", retorna "false".
        if ($this->numberProductsReturned === 0){
            return false;
        }

        // Verifica possíveis falhas nos números de paginação.
        if ($minIndex > $this->numberProductsReturned ||
            $page_number === 0){
                header("Location: 404.php");
            }

        // retorna a página correspondente com a lista de produtos.
        return array_slice(
            $this->returnedProducts,
            $minIndex,
            10
        );

    }

    // Esse método retorna o total de páginas na qual serão mostrados todos os produtos.
    public function totalPages(){

        return ceil($this->numberProductsReturned / 12);

    } 

}

?>