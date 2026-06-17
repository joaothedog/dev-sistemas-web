from flask import Blueprint, render_template, request, redirect, url_for
from dao.minicurso_dao import MinicursoDAO
from dao.inscrito_dao import InscritoDAO
from models.minicurso import Minicurso

# Cria o blueprint para as rotas relacionadas ao CRUD de minicursos
minicursos_bp = Blueprint("minicursos", __name__)

@minicursos_bp.route("/minicursos")
def listar():
    # Carrega e exibe todos os minicursos cadastrados
    minicursos_lista = MinicursoDAO.find_all()
    return render_template("minicursos/listar.html", minicursos=minicursos_lista)

@minicursos_bp.route("/minicursos/criar", methods=["GET", "POST"])
def criar():
    if request.method == "POST":
        nome = request.form.get("nome", "").strip()

        # Validacoes de seguranca do input
        if not nome:
            return render_template("erro.html", mensagem="O nome do minicurso é obrigatório!"), 400

        if len(nome) > 100:
            return render_template("erro.html", mensagem="O nome do minicurso deve conter no máximo 100 caracteres."), 400

        # Evita duplicacao de nomes
        if MinicursoDAO.existe_nome(nome):
            return render_template("erro.html", mensagem="Já existe um minicurso cadastrado com esse nome!"), 400

        # Salva o novo minicurso
        novo_minicurso = Minicurso(nome=nome)
        MinicursoDAO.save(novo_minicurso)
        return redirect(url_for("minicursos.listar"))

    return render_template("minicursos/criar.html")

@minicursos_bp.route("/minicursos/editar/<int:minicurso_id>", methods=["GET", "POST"])
def editar(minicurso_id):
    # Carrega o minicurso que sera editado
    minicurso = MinicursoDAO.find_by_id(minicurso_id)
    if not minicurso:
        return render_template("erro.html", mensagem="Minicurso não encontrado!"), 404

    if request.method == "POST":
        nome = request.form.get("nome", "").strip()

        # Validacoes de seguranca do input
        if not nome:
            return render_template("erro.html", mensagem="O nome do minicurso é obrigatório!"), 400

        if len(nome) > 100:
            return render_template("erro.html", mensagem="O nome do minicurso deve conter no máximo 100 caracteres."), 400

        # Verifica se o novo nome ja pertence a outro minicurso cadastrado
        if MinicursoDAO.existe_nome(nome, ignorar_id=minicurso_id):
            return render_template("erro.html", mensagem="Já existe outro minicurso cadastrado com esse nome!"), 400

        minicurso.nome = nome
        MinicursoDAO.save(minicurso)
        return redirect(url_for("minicursos.listar"))

    return render_template("minicursos/editar.html", minicurso=minicurso)

@minicursos_bp.route("/minicursos/excluir", methods=["POST"])
def excluir():
    id_str = request.form.get("id")
    if not id_str:
        return render_template("erro.html", mensagem="Identificador do minicurso não informado!"), 400

    try:
        minicurso_id = int(id_str)
    except ValueError:
        return render_template("erro.html", mensagem="Identificador do minicurso inválido!"), 400

    # Regra de integridade: impede exclusao se houver inscritos associados
    if InscritoDAO.contar_por_minicurso(minicurso_id) > 0:
        return render_template("erro.html", mensagem="Não é possível excluir este minicurso porque existem inscritos cadastrados nele!"), 400

    # Exclui o minicurso
    MinicursoDAO.delete(minicurso_id)
    return redirect(url_for("minicursos.listar"))
