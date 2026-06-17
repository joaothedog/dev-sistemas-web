<?php

namespace App\Models;

/**
 * Esta classe representa um usuário da agenda.
 * Usamos ela para carregar e comparar os dados de login de quem acessa o painel.
 */
class Usuario {
    // Identificador único do usuário no banco de dados
    public $id;
    // E-mail do usuário, usado como nome de login
    public $email;
    // Senha secreta e criptografada (hash) do usuário
    public $senha;

    // Construtor básico para preencher os dados de login do usuário facilmente
    public function __construct($id = null, $email = '', $senha = '') {
        $this->id = $id;
        $this->email = $email;
        $this->senha = $senha;
    }
}
