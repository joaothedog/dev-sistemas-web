import sqlite3
from dao.db_connection import obter_conexao
from dao.minicurso_dao import MinicursoDAO
from dao.inscrito_dao import InscritoDAO
from models.minicurso import Minicurso

def inicializar_banco():
    # Cria a tabela de minicursos primeiro, pois a de inscritos depende dela
    MinicursoDAO.criar_tabela()
    
    conexao = obter_conexao()
    cursor = conexao.cursor()
    
    # Verifica a estrutura da tabela de inscritos para realizar migracao se necessario
    tabela_inscritos_existe = False
    try:
        cursor.execute("PRAGMA table_info(inscritos)")
        colunas = [col[1] for col in cursor.fetchall()]
        if colunas:
            tabela_inscritos_existe = True
    except sqlite3.OperationalError:
        colunas = []
        
    # Se a tabela contem a coluna antiga 'minicurso' (texto), migra para a nova estrutura
    if tabela_inscritos_existe and "minicurso" in colunas and "minicurso_id" not in colunas:
        # Recupera as inscricoes ja existentes
        cursor.execute("SELECT id, nome, minicurso FROM inscritos")
        registros_antigos = cursor.fetchall()
        
        # Salva os nomes dos minicursos antigos na tabela correspondente
        nomes_minicursos = set([reg[2] for reg in registros_antigos if reg[2]])
        for nome in nomes_minicursos:
            try:
                cursor.execute("INSERT OR IGNORE INTO minicursos (nome) VALUES (?)", (nome,))
            except sqlite3.Error:
                pass
        
        # Remove a tabela antiga para reconstrui-la
        cursor.execute("DROP TABLE inscritos")
        conexao.commit()
        
        # Cria a nova tabela com suporte a chaves estrangeiras
        InscritoDAO.criar_tabela()
        
        # Insere novamente as inscricoes associando ao ID correto do minicurso
        conexao_nova = obter_conexao()
        cursor_novo = conexao_nova.cursor()
        for id_insc, nome_insc, nome_mc in registros_antigos:
            cursor_novo.execute("SELECT id FROM minicursos WHERE nome = ?", (nome_mc,))
            res = cursor_novo.fetchone()
            if res:
                minicurso_id = res[0]
                cursor_novo.execute(
                    "INSERT INTO inscritos (id, nome, minicurso_id) VALUES (?, ?, ?)",
                    (id_insc, nome_insc, minicurso_id)
                )
        conexao_nova.commit()
        conexao_nova.close()
    else:
        # Apenas garante a criacao da tabela se ela nao existir
        InscritoDAO.criar_tabela()
        
    conexao.close()
    
    # Popula o banco com os minicursos padrao caso esteja vazio
    if len(MinicursoDAO.find_all()) == 0:
        minicursos_iniciais = ["Java", "Python", "Javascript", "Typescript"]
        for nome in minicursos_iniciais:
            MinicursoDAO.save(Minicurso(nome=nome))
