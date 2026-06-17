import secrets
from flask import Flask, session, request, render_template
from dao.db_initializer import inicializar_banco
from controllers.inscritos_controller import inscritos_bp
from controllers.minicursos_controller import minicursos_bp

app = Flask(__name__)

# Chave secreta para gerenciamento seguro da sessao e geracao de tokens CSRF
app.secret_key = "semana_de_computacao_chave_secreta_super_segura"

# Inicializa as tabelas do banco de dados e executa migracao de schema se necessario
inicializar_banco()

# Registra os Blueprints dos Controllers do sistema
app.register_blueprint(inscritos_bp)
app.register_blueprint(minicursos_bp)

@app.before_request
def garantir_token_csrf():
    # Garante que um token CSRF exista na sessao do usuario
    if "csrf_token" not in session:
        session["csrf_token"] = secrets.token_hex(16)

@app.before_request
def validar_token_csrf():
    # Middleware de seguranca para validar o token CSRF em todas as requisicoes POST
    if request.method == "POST":
        token_sessao = session.get("csrf_token")
        token_formulario = request.form.get("csrf_token")
        
        if not token_sessao or token_sessao != token_formulario:
            return render_template("erro.html", mensagem="Ação de segurança: Token CSRF inválido ou ausente."), 400

@app.context_processor
def injetar_funcao_csrf():
    # Injeta a funcao csrf_token() no contexto do Jinja2 para uso facil nos templates
    return dict(csrf_token=lambda: session.get("csrf_token", ""))

if __name__ == "__main__":
    app.run(debug=True)
