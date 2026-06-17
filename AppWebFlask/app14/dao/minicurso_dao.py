from dao.db_connection import obter_conexao
from models.minicurso import Minicurso

class MinicursoDAO:
    @staticmethod
    def criar_tabela():
        # Cria a tabela de minicursos se ela nao existir no banco de dados
        conexao = obter_conexao()
        conexao.execute("""
            CREATE TABLE IF NOT EXISTS minicursos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nome TEXT NOT NULL UNIQUE
            )
        """)
        conexao.commit()
        conexao.close()

    @staticmethod
    def find_all():
        # Busca todos os minicursos cadastrados e ordena por nome
        conexao = obter_conexao()
        cursor = conexao.execute("SELECT id, nome FROM minicursos ORDER BY nome")
        linhas = cursor.fetchall()
        conexao.close()
        return [Minicurso(id=linha[0], nome=linha[1]) for linha in linhas]

    @staticmethod
    def find_by_id(minicurso_id):
        # Busca um minicurso especifico pelo seu identificador
        conexao = obter_conexao()
        cursor = conexao.execute("SELECT id, nome FROM minicursos WHERE id = ?", (minicurso_id,))
        linha = cursor.fetchone()
        conexao.close()
        if linha:
            return Minicurso(id=linha[0], nome=linha[1])
        return None

    @staticmethod
    def save(minicurso):
        # Cadastra um novo minicurso ou atualiza um existente
        conexao = obter_conexao()
        if minicurso.id:
            conexao.execute("UPDATE minicursos SET nome = ? WHERE id = ?", (minicurso.nome, minicurso.id))
        else:
            cursor = conexao.execute("INSERT INTO minicursos (nome) VALUES (?)", (minicurso.nome,))
            minicurso.id = cursor.lastrowid
        conexao.commit()
        conexao.close()

    @staticmethod
    def delete(minicurso_id):
        # Exclui um minicurso do banco de dados pelo seu ID
        conexao = obter_conexao()
        conexao.execute("DELETE FROM minicursos WHERE id = ?", (minicurso_id,))
        conexao.commit()
        conexao.close()

    @staticmethod
    def existe_nome(nome, ignorar_id=None):
        # Verifica se ja existe um minicurso com o mesmo nome
        conexao = obter_conexao()
        if ignorar_id:
            cursor = conexao.execute("SELECT COUNT(*) FROM minicursos WHERE nome = ? AND id != ?", (nome, ignorar_id))
        else:
            cursor = conexao.execute("SELECT COUNT(*) FROM minicursos WHERE nome = ?", (nome,))
        quantidade = cursor.fetchone()[0]
        conexao.close()
        return quantidade > 0
