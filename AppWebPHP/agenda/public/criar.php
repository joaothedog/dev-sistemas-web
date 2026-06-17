<?php

// Iniciamos a sessão para validação do login e token de formulário (CSRF)
session_start();

// Se o usuário não estiver logado, redirecionamos para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Carrega as dependências necessárias
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/Controllers/ContatoController.php';

use App\Controllers\ContatoController;

// Instancia o controlador de contatos
$controller = new ContatoController($pdo);

// Executa a ação de criação de contato
$controller->criar();
