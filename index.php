<?php
    //pegando as info da classe pessoa e jogando no index
    require_once 'classe-pessoa.php';
    //passando a conexão com o banco para uma variável $p
    $p = new Pessoa("crudpdo","localhost","root","");
?>

<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <title>Título da página</title>
    <meta charset="utf-8">
    <!--Utilizar o link para pegar o link do css-->
    <link rel="stylesheet" href="estilo.css">
  </head>
  <body>
    <?php
    //aqui eu preciso verificar se o usuário realmente apertou o buttom de cadastrar/atualizar pelo metodo POST 
        if (isset($_POST['nome'])){
        //aqui eu verifico se a pessoa clicou no botão de atualizar pelo id_up se ele não estiver vazio (!empty) é porque tem dado a ser alterado
        if (isset($_GET['id_up']) && !empty($_GET['id_up'])){
            
            //---------------ATUALIZAR DADO---------------------------
            $id_upd = addslashes($_GET['id_up']);
            $nome = addslashes($_POST['nome']);
            $telefone = addslashes($_POST['telefone']);
            $email = addslashes($_POST['email']);

            //preciso verificar se todos os campos estão preenchidos com a pergunta.. Se todos os campos não estiverem vazios (empty) então cadastre.
            if (!empty($nome) && !empty($telefone) && !empty($email)) {
                //esse $p é o que me une com o banco de dados que foi feito logo no inicio do index
                $p->atualizarPessoa($id_upd, $nome, $telefone, $email);
                //depois de apertar o botão de atualizar, eu preciso dá um f5 na pagina pelo header
                header("location: index.php");
            }else{
            //caso algum campo esteja vazio 
            echo "Preencha todos os campos!";
        }
        //aqui agora vai entrar no cadastrar
        }else{
            
            //----------------CADASTRAR DADO---------------------------
            //aqui eu preciso colocar para as variáveis o que foi digitado pelo usuario. Utilizar o addslashes como modo de segurança para não entrar no banco nenhum vírus malicioso.
            $nome = addslashes($_POST['nome']);
            $telefone = addslashes($_POST['telefone']);
            $email = addslashes($_POST['email']);
            //preciso verificar se todos os campos estão preenchidos com a pergunta.. Se todos os campos não estiverem vazios (empty) então cadastre.
            if (!empty($nome) && !empty($telefone) && !empty($email)) {
                //esse $p é o que me une com o banco de dados que foi feito logo no inicio do index
                //se ele não conseguir cadastrar é porque o email está repetido visto que em cadastrarPessoa() o retorno é true ou false
            if (!$p->cadastrarPessoa($nome, $telefone, $email)){
                echo "Email já cadastrado!";
            }
        }else{
            //caso algum campo esteja vazio 
            echo "Preencha todos os campos!";
        }
    }
        }   
    ?>
    <?php
    //aqui eu preciso verificar se realmente existe algum valor no id_up pelo GET  
    if (isset($_GET['id_up'])) {
        $id_update = addslashes($_GET['id_up']);
        //precisei colocar tudo em uma variável $res para mostrar la na tela do usuário 
        $res = $p->buscarDadosPessoa($id_update);
    }
    ?>
    <!--Estou dividindo a tela em duas sessões com o section lembrando que elas precisam de um id-->
    <section id="esquerda">
        <!--Utiilizo a  tag form porque vou fazer um formulário-->
        <form method="POST">
            <h2>CADASTRAR PESSOAS</h2>
            <!--O label e o input são os campos necessários para a pessoa digitar na página-->
            <label for="nome">Nome</label>
            <!--Esses names são para pegar info que a pessoa digitar dentro da caixa-->
            <!--Utilizar o id para ligar com o label-->
            <!--Esse input informa basicamente o tipo do label-->
            <!--Esse value no input serve na hora do editar dados onde se tiver algum dado a ser alterado será jogado as info no campo para ser alterado-->
            <input type="text" name="nome" id="nome" value="<?php if(isset($res)){echo $res['nome'];} ?>">
            <label for="telefone">Telefone</label>
            <input type="text" name="telefone" id="telefone" value="<?php if(isset($res)){echo $res['telefone'];} ?>">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" value="<?php if(isset($res)){echo $res['email'];} ?>">
            <!--Criar um campo para o botão-->
            <!--Esse botao caso o usuario queira editar algo o buttom será modificado para "atualizar" então temos que definir isso no value-->
            <input type="submit" value="<?php if(isset($res)){echo "Atualizar";}else{echo "Cadastar";}?>">
        </form>
    </section>
    <!--Fazendo a outra sessão da direita ainda na mesma página-->
    <section id="direita">
        <!--Construindo a tabela de resposta do banco de dado-->
        <table>
            <!--Esse primeiro <tr> é para o título da tabela-->
            <tr id="titulo">
                <td>NOME</td>
                <td>TELEFONE</td>
                <!--Esse colspan é apenas para a linha do titulo email ocupar duas colunas para ficar organizado-->
                <td colspan="2">EMAIL</td>
            </tr>
        <?php
            //aqui eu preciso exibir bonitinho os dados para o usuario dentro da tabela
            $dados = $p->buscarDados();
            //somente pode exibir dado se tiver alguem cadastrado no banco
            if(count($dados)>0){
                //preciso pecorrer todas as info de pessoa
                for ($i=0; $i < count($dados); $i++) { 
                    //a cada passagem do for uma linha na tabela é criada fora do foreach
                    echo "<tr>";
                    //esse foreach é para exibir na tela do usuário de maneira organizada a solicitação do select 
                    foreach($dados[$i] as $k => $v){
                        //só será exibido as info que não tiver o campo id
                        if ($k != "id") {
                            echo "<td>".$v."</td>";
                        }
                    }
                    //lembrando que eu preciso fechar e abrir a tag php para exibir um html
                    ?>
                    <!--finalizando a linha da pessoa cadastrada no banco com seus respectivos buttom-->
                    <td> 
                        <a href="index.php?id_up= <?php echo $dados[$i]['id']; ?>">Editar</a> 
                        <!--ou seja, quando eu aperto o buttom de excluir então eu envio para a variavel id na propria pagina index.php o meu respectivo id de quem eu quero excluir-->
                        <!--eu preciso de $dados[$i]['id'] porque o primeiro [$i]  eu digo em que linha se encontra pelo for e o segundo ['id'] eu digo o que eu quero dessa linha que é o id-->
                        <a href="index.php?id= <?php echo $dados[$i]['id']; ?>">Excluir</a> 
                    </td>
                    <?php
                    echo "</tr>";
                }
            }else{
                echo "Ainda não há pessoas cadastradas!";
            }
            ?>
        </table>
    </section>
  </body>
</html>

<?php
    //pegando o id que foi selecionado no buttom de excluir pelo GET 
    //ou seja, se tiver algum id para ser excluido então exclua
    if (isset($_GET['id'])){
        //criei uma variavel para receber de forma segura (addslashes) o id a ser exluido 
        $id_pessoa = addslashes($_GET['id']);
        //joguei para a função excluirPessoa 
        $p->excluirPessoa($id_pessoa);
        //atualizo a pagina para o próprio site pela função header
    ?>
        <!--Precisei usar um java script porque o header se usado mais de uma vez no mesmo index, dá ruim. Sendo que é fundamental eu atualizar a página.. sorry ;)-->
        <script>
            window.location.href="index.php"
        </script>
<?php
    }
?>