<?php

namespace App\Controllers;

use App\DAO\ContatoDAO;
use App\Models\Contato;
use PDO;

require_once __DIR__ . '/../DAO/ContatoDAO.php';
require_once __DIR__ . '/../Models/Contato.php';

/**
 * Esta classe é o controlador para todas as ações relacionadas a contatos (listar, criar, editar, deletar).
 * Ela recebe os comandos do usuário (vindas das páginas públicas), interage com o ContatoDAO e define qual View deve ser exibida.
 */
class ContatoController {
    // Objeto DAO de contatos para operações de persistência
    private $contatoDAO;

    // Construtor que recebe o banco de dados e inicializa o nosso DAO de contatos
    public function __construct(PDO $db) {
        $this->contatoDAO = new ContatoDAO($db);
    }

    // Exibe a listagem completa de contatos na agenda
    public function index() {
        // Busca a lista completa usando o DAO
        $contatos = $this->contatoDAO->listarTodos();

        // Carrega a View correspondente à listagem dos contatos
        require_once __DIR__ . '/../Views/index.php';
    }

    // Cria um novo contato no banco de dados
    public function criar() {
        // Se a requisição for um POST, estamos salvando um novo contato
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Requisito de Segurança: Proteção contra ataques CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                die("Erro de segurança: Token CSRF inválido ou ausente.");
            }

            // Captura e sanitiza as entradas contra XSS e espaços em branco desnecessários
            $nome = trim($_POST['nome'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $email = trim($_POST['email'] ?? '');

            // Validação simples de campos obrigatórios
            if (!empty($nome) && !empty($telefone) && !empty($email)) {
                $novoContato = new Contato(null, $nome, $telefone, $email);
                // Salva no banco de dados via DAO
                $this->contatoDAO->inserir($novoContato);
            }

            // Redireciona o usuário para a página principal da agenda
            header("Location: index.php");
            exit;
        }

        // Se for um GET, apenas exibe a View com o formulário de cadastro
        require_once __DIR__ . '/../Views/criar.php';
    }

    // Edita os dados de um contato existente
    public function editar($id) {
        // Busca o contato pelo ID. Se não existir no banco de dados, volta para a listagem
        $contato = $this->contatoDAO->buscarPorId($id);
        if (!$contato) {
            header("Location: index.php");
            exit;
        }

        // Se a requisição for do tipo POST, estamos atualizando os dados do contato
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Requisito de Segurança: Proteção contra ataques CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
                die("Erro de segurança: Token CSRF inválido ou ausente.");
            }

            // Captura e limpa as entradas informadas pelo usuário
            $nome = trim($_POST['nome'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $email = trim($_POST['email'] ?? '');

            // Atualiza os dados do contato caso as informações obrigatórias estejam presentes
            if (!empty($nome) && !empty($telefone) && !empty($email)) {
                $contato->nome = $nome;
                $contato->telefone = $telefone;
                $contato->email = $email;

                // Atualiza as informações no banco de dados pelo DAO
                $this->contatoDAO->atualizar($contato);
            }

            // Redireciona de volta para a agenda principal
            header("Location: index.php");
            exit;
        }

        // Se for um GET, exibe a View com o formulário contendo as informações atuais do contato
        require_once __DIR__ . '/../Views/editar.php';
    }

    // Deleta um contato da agenda pelo seu ID correspondente
    public function deletar($id) {
        if (!empty($id)) {
            // Efetua a exclusão no banco de dados usando o DAO
            $this->contatoDAO->deletar($id);
        }

        // Redireciona de volta para a tela inicial
        header("Location: index.php");
        exit;
    }
}
