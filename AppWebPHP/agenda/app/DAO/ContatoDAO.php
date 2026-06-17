<?php

namespace App\DAO;

use App\Models\Contato;
use PDO;

require_once __DIR__ . '/../Models/Contato.php';

/**
 * Esta classe é responsável por fazer a ponte entre a tabela de contatos no banco de dados e os nossos objetos PHP.
 * É aqui que ficam as queries SQL para que o restante do código não precise lidar diretamente com o banco.
 */
class ContatoDAO {
    // Instância da conexão com o banco de dados (PDO)
    private $db;

    // O construtor recebe a conexão do banco de dados para podermos usá-la nas funções
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Busca todos os contatos salvos no banco de dados, ordenando por nome
    public function listarTodos() {
        // Usamos query simples porque não há parâmetros vindos do usuário (sem risco de SQL Injection)
        $stmt = $this->db->query("SELECT * FROM contatos ORDER BY nome");
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $contatos = [];
        foreach ($resultados as $linha) {
            $contatos[] = new Contato(
                $linha['id'],
                $linha['nome'],
                $linha['telefone'],
                $linha['email']
            );
        }
        return $contatos;
    }

    // Busca um contato específico pelo ID
    public function buscarPorId($id) {
        // Usamos prepared statement para evitar ataques de SQL Injection
        $stmt = $this->db->prepare("SELECT * FROM contatos WHERE id = ?");
        $stmt->execute([$id]);
        $linha = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($linha) {
            return new Contato(
                $linha['id'],
                $linha['nome'],
                $linha['telefone'],
                $linha['email']
            );
        }
        return null;
    }

    // Salva um novo contato no banco de dados
    public function inserir(Contato $contato) {
        // Prepared statement garante que os dados informados sejam tratados com segurança
        $stmt = $this->db->prepare("INSERT INTO contatos (nome, telefone, email) VALUES (?, ?, ?)");
        return $stmt->execute([
            $contato->nome,
            $contato->telefone,
            $contato->email
        ]);
    }

    // Atualiza as informações de um contato existente
    public function atualizar(Contato $contato) {
        // Prepared statement protege os campos durante a atualização
        $stmt = $this->db->prepare("
            UPDATE contatos
            SET nome = ?, telefone = ?, email = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $contato->nome,
            $contato->telefone,
            $contato->email,
            $contato->id
        ]);
    }

    // Remove um contato do banco de dados pelo ID dele
    public function deletar($id) {
        // Prepared statement protege a deleção contra SQL Injection
        $stmt = $this->db->prepare("DELETE FROM contatos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
