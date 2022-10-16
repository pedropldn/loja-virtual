<section class="container col-12 col-sm-8 col-md-9 col-lg-10">
    <div class="text-center">
        <?php
            if ($pagina === "vender"){
        ?>
                <h1>Cadastrar Novo Produto</h1>
                <p>Preecha os campos abaixo para colocar seu produto à venda: </p>
        <?php 
            }
            elseif ($pagina === "edit"){
        ?>
                <h1>Editar Produto</h1>
                <p>Preecha os campos abaixo para editar seu produto: </p>
        <?php 
            }
        ?>
    </div>

    
    <form id="info-product" action="" method="post" enctype="multipart/form-data">

        
    
        <div class="container row col-12 css-input-box">

            <label class="col-12 col-md-4 text-center" title="Nome do seu produto. Servirá também como título nas buscas!">Título: </label>

            <div class="col-12 col-md-8">
                <input type="text" placeholder="Insira o Nome do Produto..." name="titulo_produto" 
                        value="<?php echo $dados_produto['titulo_produto']; ?>">
            
                
                <label class="col-12 css-msg-erro">
                    <?php echo $msgs_erros['titulo_produto']; ?>    
                </label>
            </div>

        </div>

        <div class="container row col-12 css-input-box">

            <label class="col-12 col-md-4 text-center" title="Quantidade em Estoque!">Quantidade em Estoque: </label>

            <div class="col-12 col-md-8">
                <input type="number" name="quantidade"
                        min="1" max="99" 
                        value="<?php echo $dados_produto['quantidade']; ?>">
                <label class="col-12 css-msg-erro">
                    <?php echo $msgs_erros['quantidade']; ?>    
                </label>
            </div>

        </div>

        <div class="container row col-12 css-input-box">

            <label class="col-12 col-md-4 text-center" title="Preço do produto!">Preço: R$</label>

            <div class="col-12 col-md-8">
                <input type="text" name="preco"
                        min="0" max="100000"
                        value="<?php 
                            if ($_SERVER['REQUEST_METHOD'] === "POST"){ 
                                echo formatar_preco($dados_produto['preco']); 
                            }
                            else {
                                echo $dados_produto['preco'];
                            }?>">
                <label class="col-12 css-msg-erro">
                    <?php echo $msgs_erros['preco']; ?>    
                </label>
            </div>

        </div>

        <div class="container row col-12 css-input-box">

            <label class="col-12 col-md-4 text-center" title="Faça o upload de uma imagem mostrando o produto!"><?php echo $imagem; ?></label>

            <div class="col-12 col-md-8">
                <input class="btn" type="file" name="imagem"><br>
                <label class="col-12 css-msg-erro"><?php echo $msgs_erros['imagem']; ?>  </label>
            </div>

        </div>

        <div class="container row col-12 css-input-box">

            <label class="col-12 col-md-4 text-center" title="Descreva seu produto em detalhes!">Descrição do Produto: </label>

            <div class="col-12 col-md-8">
                <textarea id= required name="descricao"><?php echo $dados_produto['descricao']; ?></textarea>
                <label class="col-12 css-msg-erro">
                    <?php echo $msgs_erros['descricao']; ?>    
                </label>
            </div>
        </div>

        <!-- IMPORTANTE!!!! NUNCA MUDE O ATTR VALUE DOS INPUTS ABAIXO, SEM ALTERAR OS VALORES
            QUE ESTÃO REGISTRADOS NO vender.php, POIS A FORMA DE VALIDAÇÃO MUDA DE ACORDO
            COM ESSES VALORES -->
        <?php
            if ($pagina === "vender"){
        ?>
                <div class="container text-center css-input-box">
                    <input id="submit" class="btn btn-primary" type="submit" name="submit" value="Cadastrar Produto">
                </div>
        <?php 
            }
            elseif ($pagina === "edit"){
        ?>
            <div class="text-center">
                <input id="submit" type="submit" name="submit" value="Salvar Alterações">
                <a href="account.php?link=my_products">Cancelar Alterações</a>
                <input type="hidden" name="id_produto" value="<?php echo $dados_produto['id_produto']; ?>">
            </div>
        <?php 
            }
        ?>
    </form>
</section>