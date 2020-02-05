<?php

class Pessoa{

    private $pdo;
    //aqui está a primeira conexão da classe que é a função construct que tem que ser a conexão com o banco de dados
    public function __construct($dbname, $host, $user, $senha){
        //passando as info do banco de dados para a classe construct
        //como essa conexão pode gerar erros então é sempre bom manter ele dentro de um try
        try{
            $this->pdo = new PDO("mysql:dbname=".$dbname.";host=".$host, $user, $senha);
        }
        catch(PDOException $e){
            echo "Erro com o banco de dados: ".$e->getmessage();
        }
        catch(Exception $e){
            echo "Erro genérico: ".$e->getmessage();
        }
        
    }
    //toda parte "funcional" do site irá partir daqui. Exemplo: Mostrar na tela, Cadastrar, Editar, Exlcuir.. para isso será preciso criar um método para cada situação, lembrando que tudo vai está dentro da class Pessoa.
    
    //---------------FUNÇÃO DE BUSCAR DADOS NO BANCO DE DADOS----------------------------
    //criando uma função para buscar os elementos do banco de dados e colocar no canto direito da tela
    public function buscarDados(){
        //caso não tenha nada no banco ele vai retornar um array vazio
        $res = array();
        //acessando as info pela query da variável  pdo
        //as info vem em ordem decrescente pelo id (pilha)
        //não passei parametro nenhum e nem inserir nenhum dado, apenas peguei os valores então eu uso query() 
        $cmd = $this->pdo->query("select*from pessoa order by id desc");
        //usando o fetchAll para limpar as info
        $res = $cmd->fetchAll(PDO::FETCH_ASSOC);
        //irá retornar todas as info encontrada no banco
        return $res;
    }

    //----------------FUNCAO DE CADASTRAR PESSOAS NO BANCO DE DADOS----------------------
    //método para fazer o buttom de cadastrar funcionar
    public function cadastrarPessoa($nome, $telefone, $email){
        //lembrando que os emails repetidos não são válidos
        //estou procurando com o id se já tem algum email repetido
        //se eu preciso passar um parametro para verficar onde os email são iguais então eu uso o prepare()
        $cmd = $this->pdo->prepare("select id from pessoa where email = :e");
        $cmd->bindValue(":e",$email);
        $cmd->execute();
        //precisamos verificar se já tem alguma pessoa ja com esse email através da contagem do id e se tiver não poderá ser cadastrada
        if ($cmd->rowCount() > 0) {
            return false;
        //caso não tenha ninguém com esse email aí sim é feito um insert 
        }else{
            //como eu preciso passar um parametro para inserir no banco de dados então novamente eu uso o prepare() 
            $cmd = $this->pdo->prepare("insert into pessoa (nome, telefone, email) values (:n,:t,:e)");
            $cmd->bindValue(":n",$nome);
            $cmd->bindValue(":t",$telefone);
            $cmd->bindValue(":e",$email);
            $cmd->execute();
            return true;
        }
    }

    //------------------FUNÇÃO DE EXCLUIR PESSOA NO BANCO DE DADOS-----------------------
    //para deletar uma pessoa basta eu ter o id dela e fazer o comando delete
    public function excluirPessoa($id){
        //como eu vou fazer uma verificação (where) para deletar pelo id então eu uso o prepare 
        $cmd = $this->pdo->prepare("delete from pessoa where id = :id");
        $cmd->bindValue(":id",$id);
        $cmd->execute();
    }

    //----------------FUNÇÃO DE EDITAR DADOS DA PESSOA NO BANCO DE DADOS------------------
    //primeira função: buscar dados da pessoa que foi selecionada para edição
    //é preciso buscar esse dado pelo id 
    public function buscarDadosPessoa($id){
        $res = array();
        //uso o prepare() pois estou comparando o id que foi escolhido pelo usuário para edição 
        $cmd = $this->pdo->prepare("select*from pessoa where id = :id");
        $cmd->bindValue(":id",$id);
        $cmd->execute();
        //como estou buscando dados então é preciso limpar as info com o fetch
        $res = $cmd->fetch(PDO::FETCH_ASSOC);
        return $res;
    }
    //segunda função: atualizar esses dados no banco de dados
    public function atualizarPessoa($id, $nome, $telefone, $email){
        //nunca esqueça de atualizar pelo id porque se não ele vai atualizar a tela inteira 
        $cmd = $this->pdo->prepare("update pessoa set nome = :n, telefone = :t, email = :e where id = :id");
        $cmd->bindValue(":n",$nome);
        $cmd->bindValue(":t",$telefone);
        $cmd->bindValue(":e",$email);
        $cmd->bindValue(":id",$id);
        $cmd->execute(); 
        return true;
    }
}

?>