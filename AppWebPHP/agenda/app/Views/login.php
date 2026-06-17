<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Agenda de Contatos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<!-- Container principal com marcação de acessibilidade (role main) -->
<div class="container" role="main">

    <h1>Login</h1>

    <!-- Exibição de mensagem de erro de login de forma amigável e acessível -->
    <?php if (!empty($erro)): ?>
        <p class="mensagem-erro" role="alert" style="color: #e74c3c; margin: 10px; font-weight: bold;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <!-- Formulário de login acessível e com proteção CSRF -->
    <form method="POST" action="login.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required autocomplete="email">
        
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required autocomplete="current-password">
        
        <button class="botao-destaque link-externo" type="submit">Entrar</button>
    </form>

</div>

</body>
</html>
