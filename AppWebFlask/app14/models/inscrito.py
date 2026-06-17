# Classe que representa um inscrito em um minicurso
class Inscrito:
    def __init__(self, id=None, nome=None, minicurso_id=None, minicurso_nome=None):
        self.id = id
        self.nome = nome
        self.minicurso_id = minicurso_id
        self.minicurso_nome = minicurso_nome # Nome do minicurso obtido por meio de JOIN
