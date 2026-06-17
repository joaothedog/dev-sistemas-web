<?php

// Iniciamos a sessão para poder destruí-la com segurança
session_start();

// Carrega as dependências necessárias
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';

use App\Controllers\AuthController;

// Instancia o controlador de autenticação
$controller = new AuthController($pdo);

// Executa a saída do usuário da agenda pelo controlador
$controller->logout();
