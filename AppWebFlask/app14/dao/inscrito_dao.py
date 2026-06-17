from dao.db_connection import obter_conexao
from models.inscrito import Inscrito

class InscritoDAO:
    @staticmethod
    def criar_tabela():
        # Cria a tabela de inscritos se ela nao existir no banco de dados
        conexao = obter_conexao()
        conexao.execute("""
            CREATE TABLE IF NOT EXISTS inscritos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nome TEXT NOT NULL,
                minicurso_id INTEGER NOT NULL,
                FOREIGN KEY(minicurso_id) REFERENCES minicursos(id) ON DELETE RESTRICT
            )
        """)
        conexao.commit()
        conexao.close()

    @staticmethod
    def find_all():
        # Busca todos os inscritos realizando um JOIN para obter o nome do minicurso
        conexao = obter_conexao()
        cursor = conexao.execute("""
            SELECT i.id, i.nome, i.minicurso_id, m.nome 
            FROM inscritos i 
            JOIN minicursos m ON i.minicurso_id = m.id
            ORDER BY i.nome
        """)
        linhas = cursor.fetchall()
        conexao.close()
        
        # Instancia cada linha em um objeto do tipo Inscrito
        return [
            Inscrito(id=linha[0], nome=linha[1], minicurso_id=linha[2], minicurso_nome=linha[3])
            for linha in linhas
        ]

    @staticmethod
    def save(inscrito):
        # Cadastra uma nova inscricao no banco de dados
        conexao = obter_conexao()
        cursor = conexao.execute(
            "INSERT INTO inscritos (nome, minicurso_id) VALUES (?, ?)",
            (inscrito.nome, inscrito.minicurso_id)
        )
        inscrito.id = cursor.lastrowid
        conexao.commit()
        conexao.close()

    @staticmethod
    def delete(inscrito_id):
        # Remove a inscricao do banco de dados usando o ID
        conexao = obter_conexao()
        conexao.execute("DELETE FROM inscritos WHERE id = ?", (inscrito_id,))
        conexao.commit()
        conexao.close()

    @staticmethod
    def contar_por_minicurso(minicurso_id):
        # Conta quantos inscritos estao matriculados em um minicurso
        conexao = obter_conexao()
        cursor = conexao.execute("SELECT COUNT(*) FROM inscritos WHERE minicurso_id = ?", (minicurso_id,))
        total = cursor.fetchone()[0]
        conexao.close()
        return total
