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

    <h1>Agenda de Contatos</h1>

    <!-- Links de ação da agenda de contatos -->
    <a href="criar.php" class="botao-destaque link-externo">
        Novo Contato
    </a>
    
    <a href="logout.php" class="botao-destaque link-externo">
        Sair
    </a>

    <!-- Tabela acessível para listagem dos contatos cadastrados -->
    <table>

        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Nome</th>
                <th scope="col">Telefone</th>
                <th scope="col">Email</th>
                <th scope="col">Ações</th>
            </tr>
        </thead>

        <tbody>

        <?php foreach ($contatos as $c): ?>

            <tr>

                <td><?= htmlspecialchars($c->id) ?></td>

                <td><?= htmlspecialchars($c->nome) ?></td>

                <td><?= htmlspecialchars($c->telefone) ?></td>

                <td><?= htmlspecialchars($c->email) ?></td>

                <td>

                    <a href="editar.php?id=<?= htmlspecialchars($c->id) ?>"
                       class="botao-destaque link-externo">
                       Editar
                    </a>

                    <a href="deletar.php?id=<?= htmlspecialchars($c->id) ?>"
                       class="botao-destaque link-externo"
                       onclick="return confirm('Deseja excluir este contato?')">
                       Excluir
                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

</body>
</html>
