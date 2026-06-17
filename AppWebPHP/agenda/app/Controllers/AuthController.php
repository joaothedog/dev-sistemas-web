<?php

namespace App\Controllers;

use App\DAO\UsuarioDAO;
use PDO;

require_once __DIR__ . '/../DAO/UsuarioDAO.php';

/**
 * Esta classe é o controlador responsável pelo login e logout dos usuários na agenda.
 * Ela interage com o UsuarioDAO para validar as credenciais e manipula a sessão de forma segura.
 */
class AuthController {
    // Objeto DAO de usuários para consultas de credenciais
    private $usuarioDAO;

    // Construtor que recebe a conexão com o banco de dados e inicializa o DAO do usuário
    public function __construct(PDO $db) {
        $this->usuarioDAO = new UsuarioDAO($db);
    }

    // Gerencia o processo de login do usuário
    public function login() {
        // Requisito de Segurança: Garante que exista pelo menos o administrador padrão
        $this->usuarioDAO->garantirUsuarioPadrao();

        // Se o usuário já estiver logado, redireciona diretamente para a agenda
        if (isset($_SESSION['usuario_id'])) {
            header("Location: index.php");
            exit;
        }

        $erro = '';

        // Processa o envio do formulário de login via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Requisito de Segurança: Proteção contra ataques CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                die("Erro de segurança: Token CSRF inválido ou ausente.");
            }

            // Captura e limpa os campos de entrada informados
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';

            if (!empty($email) && !empty($senha)) {
                // Busca o registro do usuário pelo e-mail
                $usuario = $this->usuarioDAO->buscarPorEmail($email);

                // Requisito de Segurança: Compara a senha digitada com a hash segura (bcrypt) salva no banco
                if ($usuario && password_verify($senha, $usuario->senha)) {
                    // Requisito de Segurança: Regenera o ID da sessão para evitar Session Fixation
                    session_regenerate_id(true);

                    // Armazena as informações do usuário na sessão
                    $_SESSION['usuario_id'] = $usuario->id;
                    $_SESSION['usuario_email'] = $usuario->email;

                    // Redireciona com sucesso para a agenda de contatos
                    header("Location: index.php");
                    exit;
                } else {
                    // Feedback acessível de erro (sem ser específico demais por razões de segurança)
                    $erro = "E-mail ou senha inválidos.";
                }
            } else {
                $erro = "Por favor, preencha todos os campos.";
            }
        }

        // Se não tiver um token CSRF na sessão, gera um novo aleatório
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Carrega a View contendo a tela de login
        require_once __DIR__ . '/../Views/login.php';
    }

    // Efetua o logout do usuário limpando a sessão
    public function logout() {
        // Limpa todas as variáveis da sessão
        $_SESSION = [];

        // Exclui o cookie de sessão do navegador se ele existir
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destrói a sessão no servidor
        session_destroy();

        // Redireciona o usuário deslogado para a tela de login
        header("Location: login.php");
        exit;
    }
}
