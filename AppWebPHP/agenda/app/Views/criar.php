<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Contatos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

<!-- Container principal com marcação semântica de acessibilidade -->
<div class="container" role="main">

<h1>Novo Contato</h1>

<!-- Formulário acessível para criação de contatos com token de proteção CSRF -->
<form method="POST" action="criar.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

    <label for="nome">Nome:</label>
    <input type="text" id="nome" name="nome" required>
    <br>
    <label for="telefone">Telefone:</label>
    <input type="tel" id="telefone" name="telefone" placeholder="(XX)9XXXX-XXXX" pattern="\(\d{2}\)\d{5}-\d{4}" required>
    <br>
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <br>
    <button class="botao-destaque link-externo" type="submit">Salvar</button>
</form>

<a href="index.php" class="botao-destaque link-externo">Voltar</a>

</div>
</body>
</html>
