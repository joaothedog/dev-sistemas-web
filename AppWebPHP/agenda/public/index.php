<?php

// Iniciamos a sessão para poder verificar o login e o token CSRF
session_start();

// Se o usuário não estiver logado, mandamos ele para a tela de login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Carrega as dependências necessárias para o funcionamento
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/Controllers/ContatoController.php';

use App\Controllers\ContatoController;

// Instancia o controlador passando a conexão com o banco ($pdo está definido em db.php)
$controller = new ContatoController($pdo);

// Executa a ação padrão de listar os contatos
$controller->index();
