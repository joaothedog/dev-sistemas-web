<?php

// Iniciamos a sessão para validação do login e CSRF
session_start();

// Se o usuário não estiver logado, redirecionamos para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Obtém o ID do contato a ser editado
$id = $_GET['id'] ?? null;

// Se não foi informado um ID válido, volta para a agenda principal
if (!$id) {
    header("Location: index.php");
    exit;
}

// Carrega as dependências necessárias
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/Controllers/ContatoController.php';

use App\Controllers\ContatoController;

// Instancia o controlador
$controller = new ContatoController($pdo);

// Executa a ação de edição do contato
$controller->editar($id);
