<?php

session_start();
// INICIA A SESSÃO

// INCLUÍDA A CONEXÃO
include_once './conexao.php';
?>

<!DOCTYPE html>
<html lang="pt-br">

  <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Recicla Cohab</title>
     <link rel="stylesheet" href="estilo.css">
  </head>

<body>
  
  <header>
     <div id="title"> <!--RELATIVO À ID NO CSS INICIA-SE COM "#"-->
        <h1>Recicla</h1> <!--RELATIVO À CLASS NO CSS IICIA-SE COM "."-->
        <h1>Cohab</h1> <!--PODE-SE COMBINAR "CLASS" COM "ID"-->
     </div> <!--CTRL + D + CLICK SELECIONA OS IGUAIS-->
     <ul>
        <a href="index.php"><li class="inicio">Início</li></a>
        <a href="index1.php"><li class="sobre">Sobre</li></a>
     </ul>
  </header>
  
    <div>
       <img src="imagem.jpg" class="img">
    </div>

  <!--OBRIGATÓRIO O USO DO ATRIBUTO 'enctype' PARA TRABALHAR COM IMAGEM-->
    <form method='POST' action='' enctype='multipart/form-data'>
        <input type='text' name='nome' placeholder='Digite seu nome:' required>
        <input type='email' name='email' placeholder='Digite seu email:' required>
        <input type='telefone' name='telefone' placeholder='Digite seu telefone:' required>
        <input type='file' name='imagens[]' multiple='multiple' required>
        <input type='submit' name='SendCadUser' value='Cadastrar'>
    </form>

<?php
  
  // RECEBE OS DADOS DO FORMULÁRIO
  $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
  //var_dump( $dados );

  // ACESSA O 'if' QUANDO O USUÁRIO CLICA NO BOTÃO
  if (!empty($dados['SendCadUser'])) {
    //var_dump( $dados );

    // QUERY CADASTRAR USUÁRO NO BANCO DE DADOS
    $query_usuario = 'INSERT INTO usuarios (nome, email, telefone) VALUES (:nome, :email, :telefone)';

    // PREPARA A QUERY
    $cad_usuario = $conn->prepare($query_usuario);

    // SUBSTITUI OS LINKS PELOS VALORES DO FORMULÁRIO
    $cad_usuario->bindParam(':nome', $dados['nome']);
    $cad_usuario->bindParam(':email', $dados['email']);
    $cad_usuario->bindParam(':telefone', $dados['telefone']);

    // EXECUTA A QUERY
    $cad_usuario->execute();

    // ACESSA O 'if' QUANDO CADASTRA O USUÁRIO NO BANCO DE DADOS
    if ($cad_usuario->rowCount()) {

      //RECEBE O 'id' DO REGISTRO CADASTRADO
      $usuario_id = $conn->lastInsertId();

      // ENDEREÇO DO DIRETÓRIO
      $diretorio = "imagens/$usuario_id/";

      // CRIA O DIRETÓRIO
      mkdir($diretorio, 0755);

      // RECEBE OS ARQUIVOS DO FORMULÁRIO
      $arquivo = $_FILES['imagens'];
      //var_dump( $arquivo );

      // LÊ O ARRAY DE ARQUIVOS
      for ($cont = 0; $cont < count($arquivo['name']); $cont++) {
        //RECEBER O NOME DA IMAGEM
        $nome_arquivo = $arquivo['name'][$cont];

        // CRIAR O ENDEREÇO DE DESTINO DAS IMAGENS
        $destino = $diretorio . $arquivo['name'][$cont];

        //ACESSA O 'if' QUANDO REALIZAR O UPLOAD CORRETAMENTE
        if (move_uploaded_file($arquivo['tmp_name'][$cont], $destino)) {
          $query_imagem = 'INSERT INTO imagens (nome_imagem, usuario_id) VALUES (:nome_imagem, :usuario_id)';
          $cad_imagem = $conn->prepare($query_imagem);
          $cad_imagem->bindParam(':nome_imagem', $nome_arquivo);
          $cad_imagem->bindParam(':usuario_id', $usuario_id);

          if ($cad_imagem->execute()) {
            $_SESSION['msg'] = "<p style='color: green;'>Usuário cadastrado com sucesso.</p>";
          } else {
            $_SESSION['msg'] = "<p style='color: red;'> Erro: Imagem não cadastrada com sucesso.</p>";
          }
        } else {
          $_SESSION['msg'] = "<p style='color: red;'> Erro: Usuário não cadastrado com sucesso.</p>";
        }
      }
    } else {
      $_SESSION['msg'] = "<p style='color: red;'> Erro: Usuário não cadastrado com sucesso.</p>";
    }
  }

  if (isset($_SESSION['msg'])) {
    echo $_SESSION['msg'];
    unset($_SESSION['msg']);
  }

  ?>

</body>

</html>