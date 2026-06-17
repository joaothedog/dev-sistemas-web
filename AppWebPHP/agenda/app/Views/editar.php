<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda de Contatos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>

<!-- Container principal com marcação de acessibilidade -->
<div class="container" role="main">

<h1>Editar Contato</h1>

<!-- Formulário acessível para edição contendo dados higienizados e token CSRF -->
<form method="POST" action="editar.php?id=<?= htmlspecialchars($contato->id) ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($contato->nome) ?>" required>
        <br>
        <!-- Corrigido label/id de telefone para acessibilidade adequada -->
        <label for="telefone">Telefone:</label>
        <input type="tel" name="telefone" id="telefone" value="<?= htmlspecialchars($contato->telefone) ?>" placeholder="(XX)9XXXX-XXXX" pattern="\(\d{2}\)\d{5}-\d{4}" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($contato->email) ?>" required>
        <br>
        <button class="botao-destaque link-externo" type="submit">Atualizar</button>
</form>

<a href="index.php" class="botao-destaque link-externo">Voltar</a>

</div>
</body>
</html>
