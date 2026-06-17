import re
from flask import Blueprint, render_template, request, redirect, url_for
from dao.minicurso_dao import MinicursoDAO
from dao.inscrito_dao import InscritoDAO
from models.inscrito import Inscrito

# Cria o blueprint para as rotas relacionadas aos inscritos
inscritos_bp = Blueprint("inscritos", __name__)

@inscritos_bp.route("/")
def index():
    # Busca todos os minicursos disponiveis para preencher o campo do formulario
    minicursos = MinicursoDAO.find_all()
    return render_template("index.html", minicursos=minicursos)

@inscritos_bp.route("/inscrever", methods=["POST"])
def inscrever():
    nome = request.form.get("nome", "").strip()
    minicurso_id_str = request.form.get("minicurso", "")

    # Valida se o campo de nome esta preenchido
    if not nome:
        return render_template("erro.html", mensagem="O campo nome é obrigatório!"), 400

    # Valida o formato do nome usando expressao regular
    if not re.match(r"^[A-Za-zÀ-ÖØ-öø-ÿ]+(?: [A-Za-zÀ-ÖØ-öø-ÿ]+)*$", nome):
        return render_template("erro.html", mensagem="Nome inválido! Utilize apenas letras e espaços."), 400

    # Valida o tamanho maximo do nome para seguranca
    if len(nome) > 100:
        return render_template("erro.html", mensagem="O nome deve conter no máximo 100 caracteres."), 400

    # Valida se o minicurso foi informado
    if not minicurso_id_str:
        return render_template("erro.html", mensagem="Selecione um minicurso!"), 400

    try:
        minicurso_id = int(minicurso_id_str)
    except ValueError:
        return render_template("erro.html", mensagem="Código de minicurso inválido!"), 400

    # Garante que o minicurso selecionado existe no banco de dados
    minicurso = MinicursoDAO.find_by_id(minicurso_id)
    if not minicurso:
        return render_template("erro.html", mensagem="O minicurso selecionado não existe!"), 400

    # Salva a nova inscricao utilizando o DAO
    novo_inscrito = Inscrito(nome=nome, minicurso_id=minicurso_id)
    InscritoDAO.save(novo_inscrito)

    return render_template("sucesso.html")

@inscritos_bp.route("/inscritos")
def inscritos():
    # Busca e exibe todos os inscritos cadastrados
    inscritos_lista = InscritoDAO.find_all()
    return render_template("inscritos.html", inscritos=inscritos_lista)

@inscritos_bp.route("/excluir", methods=["POST"])
def excluir():
    inscrito_id_str = request.form.get("id")

    if not inscrito_id_str:
        return render_template("erro.html", mensagem="Identificador do inscrito não fornecido!"), 400

    try:
        inscrito_id = int(inscrito_id_str)
    except ValueError:
        return render_template("erro.html", mensagem="Identificador do inscrito inválido!"), 400

    # Remove o registro correspondente
    InscritoDAO.delete(inscrito_id)

    return redirect(url_for("inscritos.inscritos"))
