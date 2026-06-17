<?php

namespace App\DAO;

use App\Models\Usuario;
use PDO;

require_once __DIR__ . '/../Models/Usuario.php';

/**
 * Esta classe cuida de acessar a tabela de usuários no banco de dados.
 * Ela serve principalmente para encontrar usuários pelo e-mail e para garantir que exista pelo menos um usuário administrador padrão para acesso.
 */
class UsuarioDAO {
    // Instância da conexão com o banco de dados (PDO)
    private $db;

    // O construtor recebe a conexão do banco de dados para podermos usá-la nas queries
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Busca um usuário cadastrado no banco pelo endereço de e-mail dele
    public function buscarPorEmail($email) {
        // Usamos prepared statement para garantir total segurança contra injeção SQL no login
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $linha = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($linha) {
            return new Usuario(
                $linha['id'],
                $linha['email'],
                $linha['senha']
            );
        }
        return null;
    }

    // Função que verifica se a tabela de usuários está vazia e, se estiver, cria um usuário padrão: admin@agenda.com / admin123
    public function garantirUsuarioPadrao() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM usuarios");
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultado && $resultado['total'] == 0) {
            // E-mail e senha padrão do administrador
            $emailPadrao = 'admin@agenda.com';
            $senhaPadrao = 'admin123';
            // Criptografa a senha com bcrypt de forma segura usando a função nativa do PHP
            $senhaHash = password_hash($senhaPadrao, PASSWORD_DEFAULT);

            // Insere o usuário padrão no banco
            $stmtInsert = $this->db->prepare("INSERT INTO usuarios (email, senha) VALUES (?, ?)");
            $stmtInsert->execute([$emailPadrao, $senhaHash]);
        }
    }
}
