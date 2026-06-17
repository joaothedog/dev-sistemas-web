import sqlite3
import os

# Define o caminho absoluto para o banco de dados na raiz do app14
DB_PATH = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "inscritos.db")

def obter_conexao():
    # Conecta ao banco de dados SQLite
    conexao = sqlite3.connect(DB_PATH)
    # Habilita o suporte a chaves estrangeiras (foreign keys) no SQLite
    conexao.execute("PRAGMA foreign_keys = ON;")
    return conexao
