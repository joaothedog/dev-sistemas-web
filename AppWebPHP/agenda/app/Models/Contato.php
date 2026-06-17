<?php

namespace App\Models;

/**
 * Esta classe representa um contato da nossa agenda.
 * Ela serve para guardar os dados estruturados de cada pessoa que cadastramos.
 */
class Contato {
    // Identificador único do contato no banco de dados
    public $id;
    // Nome do contato
    public $nome;
    // Telefone de contato
    public $telefone;
    // E-mail do contato
    public $email;

    // Construtor simples para inicializar os atributos do contato de forma rápida
    public function __construct($id = null, $nome = '', $telefone = '', $email = '') {
        $this->id = $id;
        $this->nome = $nome;
        $this->telefone = $telefone;
        $this->email = $email;
    }
}
