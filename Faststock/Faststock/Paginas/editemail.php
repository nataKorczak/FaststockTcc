<?php
if(!empty($_GET['id'])) {
    include_once('../banco/config.php');


    $id = $_GET['id'];
    

    $sqlSelect = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conexao->prepare($sqlSelect);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        


        $emailregister = $user_data['email'];


    } else {
        echo "Usuário não encontrado!";
        exit;
    }
} else {
    echo "Erro: ID não fornecido!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../Estilo/Register.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Editar E-mail</title>
</head>
<body>
    <main class="register-container">

        <form action="../banco/alterar_email.php" method="POST">

            <input type="hidden" name="id" value="<?= $id ?>">
            
            <h1>Editar E-mail</h1>

            <div class="input-box">
                <input id="emailregister" name="emailregister" placeholder="E-mail" type="email" value="<?= htmlspecialchars($emailregister) ?>">
            </div>


            <button type="submit" id="submit" name="submit" class="register">Atualizar</button>

            <div class="register-link">
                <a href="javascript:history.back()">Cancelar</a>
            </div>
        </form>
    </main>



     <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
          <div class="vw-plugin-top-wrapper"></div>
        </div>
      </div>
    
      <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
      <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
      </script>

          <div vw class="enabled">
        <div vw-access-button class="active"></div>
        <div vw-plugin-wrapper>
          <div class="vw-plugin-top-wrapper"></div>
        </div>
      </div>
    
      <script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
      <script>
        new window.VLibras.Widget('https://vlibras.gov.br/app');
      </script>

</body>
</html>