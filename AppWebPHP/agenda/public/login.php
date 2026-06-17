<?php

// Iniciamos a sessão para poder guardar os dados de login e o token CSRF
session_start();

// Carrega as dependências necessárias
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/Controllers/AuthController.php';

use App\Controllers\AuthController;

// Instancia o controlador de autenticação
$controller = new AuthController($pdo);

// Executa o fluxo de login
$controller->login();
