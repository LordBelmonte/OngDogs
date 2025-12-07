<?php
session_start();
$user = null;
if (isset($_SESSION['user_id'])) {
    include_once __DIR__ . '/../app/models/Usuarios.php';
    $res = Usuarios::buscarUsuarioPeloId($_SESSION['user_id']);
    if (!$res['erro']) $user = $res['dados'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel Administrativo - ONG Dogs</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { padding-top: 100px; }
        
        section {
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
            color: #8B4513;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #2E8B57;
            color: white;
            font-weight: 600;
        }

        tr:hover {
            background: #f9f9f9;
        }

        button {
            padding: 8px 12px;
            margin-right: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-delete {
            background: #d9534f;
            color: white;
        }

        .btn-delete:hover {
            background: #c9302c;
        }

        .btn-edit {
            background: #f0ad4e;
            color: white;
        }

        .btn-edit:hover {
            background: #ec971f;
        }

        .btn-refresh {
            background: #2E8B57;
            color: white;
            margin: 20px 0 12px 0;
            float: right;
        }

        .btn-refresh:hover {
            background: #249a58;
        }

        .hidden {
            display: none;
        }

        /* Modal/backdrop para formulários de edição */
        #modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.45);
            z-index: 900;
        }

        /* Forms de edição exibidos como overlay */
        #edit-doacao-form, #edit-apadrinhamento-form, #edit-adocao-form, #edit-evento-form, #edit-form {
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            background: #fff;
            padding: 18px;
            border-radius: 8px;
            box-shadow: 0 6px 24px rgba(0,0,0,0.25);
            max-width: 640px;
            width: calc(100% - 40px);
        }

        /* versão 'minimal' para inputs (mais compacta)
           será aplicada adicionando a classe .form-minimal ao form */
        .form-minimal input, .form-minimal textarea {
            padding: 6px;
            font-size: 14px;
        }

        #edit-form {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            max-width: 500px;
        }

        #edit-form h3 {
            color: #8B4513;
            margin-bottom: 15px;
        }

        #edit-form label {
            display: block;
            margin: 10px 0 5px 0;
            color: #333;
            font-weight: 600;
        }

        #edit-form input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        #edit-form button {
            margin-right: 10px;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <header>
        <img src="imagens/logo_ong.png" alt="Logo Instituto Eu Sou Bicho" class="logo">
        <nav>
            <a href="home.php">Home</a>
            <a href="home.php#como-ajudar">Como Ajudar</a>
            <a href="home.php#animais">Animais</a>
            <a href="home.php#missao">Missão</a>
            <a href="home.php#contato">Contato</a>
            <a href="home.php#formularios">Formulários</a>
            <a href="home.php#depoimentos">Depoimentos</a>
            <?php if ($user): ?>
                <a href="admin.php">Admin</a>
                <a id="logoutLink" href="login.php">Sair (<?php echo htmlspecialchars($user['nome'] ?? 'Conta'); ?>)</a>
            <?php else: ?>
                <a href="login.php">Entrar</a>
            <?php endif; ?>
        </nav>
    </header>

    <section>
        <h1>Painel Administrativo</h1>

        <div class="tabs-container" style="margin-bottom:18px;">
            <button class="tab-btn active" data-tab="usuarios">Usuários</button>
            <button class="tab-btn" data-tab="eventos">Eventos</button>
            <button class="tab-btn" data-tab="doacoes">Doações</button>
            <button class="tab-btn" data-tab="apadrinhamentos">Apadrinhamentos</button>
            <button class="tab-btn" data-tab="adocoes">Adoções</button>
            <button class="tab-btn" data-tab="formularios">Formulários</button>
        </div>

        <!-- Aba: Usuários -->
        <div id="tab-usuarios" class="tab-panel">
            <button class="btn-refresh" onclick="carregarUsuarios()">🔄 Atualizar Lista</button>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>CPF</th>
                        <th>Tipo</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tabela-usuarios">
                    <!-- preenchido via admin.js -->
                </tbody>
            </table>

            <!-- Formulário de edição Usuário -->
            <div id="edit-form" class="hidden">
                <h3>Editar Usuário</h3>
                <label>ID:</label>
                <input type="text" id="edit-id" readonly><br><br>
                <label>Nome:</label>
                <input type="text" id="edit-nome"><br><br>
                <label>Email:</label>
                <input type="email" id="edit-email"><br><br>
                <label for="edit-senha">Senha (opcional):</label>
                <input type="password" id="edit-senha" placeholder="Deixe vazio para não alterar">
                <label>Telefone:</label>
                <input type="text" id="edit-telefone"><br><br>
                <label>CPF:</label>
                <input type="text" id="edit-cpf"><br><br>
                <label>Tipo usuário:</label>
                <input type="text" id="edit-tipo"><br><br>
                <button class="btn-edit" onclick="salvarEdicao()">Salvar</button>
                <button onclick="cancelarEdicao()">Cancelar</button>
            </div>
        </div>

        <!-- Aba: Doações -->
        <div id="tab-doacoes" class="tab-panel hidden">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                <button class="btn-refresh" onclick="carregarDoacoesAdmin()">🔄 Atualizar Doações</button>
                <div id="total-doacoes" style="font-weight:700; color:#2E8B57;">Total doado: R$ 0,00</div>
            </div>
            <table style="margin-top:12px;">
                <thead>
                    <tr><th>ID</th><th>Usuário</th><th>Valor</th><th>Descrição</th><th>Data</th><th>Ações</th></tr>
                </thead>
                <tbody id="tabela-doacoes"></tbody>
            </table>
            <div id="edit-doacao-form" class="hidden" style="margin-top:12px;">
                <h3>Editar Doação</h3>
                <label>ID:</label><input id="edit-doacao-id" readonly><br><br>
                <label>Usuário ID:</label><input id="edit-doacao-usuario"><br><br>
                <label>Valor:</label><input id="edit-doacao-valor"><br><br>
                <label>Descrição:</label><input id="edit-doacao-descricao"><br><br>
                <label>Data:</label><input id="edit-doacao-data" type="datetime-local"><br><br>
                <button class="btn-edit" onclick="salvarEdicaoDoacao()">Salvar</button>
                <button onclick="cancelarEdicaoDoacao()">Cancelar</button>
            </div>
        </div>

        <!-- Aba: Apadrinhamentos -->
        <div id="tab-apadrinhamentos" class="tab-panel hidden">
            <button class="btn-refresh" onclick="carregarApadrinhamentosAdmin()">🔄 Atualizar Apadrinhamentos</button>
            <table style="margin-top:12px;">
                <thead>
                    <tr><th>ID</th><th>Usuário</th><th>Animal ID</th><th>Valor Mensal</th><th>Ações</th></tr>
                </thead>
                <tbody id="tabela-apadrinhamentos"></tbody>
            </table>
            <div id="edit-apadrinhamento-form" class="hidden" style="margin-top:12px;">
                <h3>Editar Apadrinhamento</h3>
                <label>ID:</label><input id="edit-apadrinhamento-id" readonly><br><br>
                <label>Usuário ID:</label><input id="edit-apadrinhamento-usuario"><br><br>
                <label>Animal ID:</label><input id="edit-apadrinhamento-animal"><br><br>
                <label>Valor Mensal:</label><input id="edit-apadrinhamento-valor"><br><br>
                <button class="btn-edit" onclick="salvarEdicaoApadrinhamento()">Salvar</button>
                <button onclick="cancelarEdicaoApadrinhamento()">Cancelar</button>
            </div>
        </div>

        <!-- Aba: Adoções -->
        <div id="tab-adocoes" class="tab-panel hidden">
            <button class="btn-refresh" onclick="carregarAdocoesAdmin()">🔄 Atualizar Adoções</button>
            <table style="margin-top:12px;">
                <thead>
                    <tr><th>ID</th><th>Usuário</th><th>Animal ID</th><th>Data Adoção</th><th>Ações</th></tr>
                </thead>
                <tbody id="tabela-adocoes"></tbody>
            </table>
            <div id="edit-adocao-form" class="hidden" style="margin-top:12px;">
                <h3>Editar Adoção</h3>
                <label>ID:</label><input id="edit-adocao-id" readonly><br><br>
                <label>Usuário ID:</label><input id="edit-adocao-usuario"><br><br>
                <label>Animal ID:</label><input id="edit-adocao-animal"><br><br>
                <label>Data:</label><input id="edit-adocao-data" type="datetime-local"><br><br>
                <button class="btn-edit" onclick="salvarEdicaoAdocao()">Salvar</button>
                <button onclick="cancelarEdicaoAdocao()">Cancelar</button>
            </div>
        </div>

        <!-- Aba: Eventos -->
        <div id="tab-eventos-admin" class="tab-panel hidden">
            <button class="btn-refresh" onclick="carregarEventosAdmin()">🔄 Atualizar Eventos</button>

            <h3>Criar Novo Evento</h3>
            <form id="form-eventos-admin" class="form-card">
                <div class="form-group"><label for="admin_nome_evento">Nome do Evento:</label><input type="text" id="admin_nome_evento" name="nome" required maxlength="50"></div>
                <div class="form-group"><label for="admin_descricao_evento">Descrição (máx. 90 caracteres):</label><textarea id="admin_descricao_evento" name="descricao" rows="3" maxlength="90"></textarea></div>
                <div class="form-group"><label for="admin_data_inicio">Data de Início:</label><input type="date" id="admin_data_inicio" name="data_inicio" required></div>
                <div class="form-group"><label for="admin_data_fim">Data de Fim (Opcional):</label><input type="date" id="admin_data_fim" name="data_fim"></div>
                <button type="submit" id="btn-criar-evento" class="btn-primary">Cadastrar Evento</button>
                <div class="form-feedback" id="feedback-eventos-admin"></div>
            </form>

            <table style="margin-top:18px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Data Início</th>
                        <th>Data Fim</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tabela-eventos">
                    <!-- preenchido via admin.js -->
                </tbody>
            </table>

            <!-- Formulário de edição Evento -->
            <div id="edit-evento-form" class="hidden">
                <h3>Editar Evento</h3>
                <label>ID:</label>
                <input type="text" id="edit-evento-id" readonly><br><br>
                <label>Nome:</label>
                <input type="text" id="edit-evento-nome"><br><br>
                <label>Descrição:</label>
                <textarea id="edit-evento-descricao" rows="3"></textarea><br><br>
                <label>Data Início:</label>
                <input type="date" id="edit-evento-data_inicio"><br><br>
                <label>Data Fim:</label>
                <input type="date" id="edit-evento-data_fim"><br><br>
                <button class="btn-edit" onclick="salvarEdicaoEvento()">Salvar</button>
                <button onclick="cancelarEdicaoEvento()">Cancelar</button>
            </div>
        </div>
        
        <!-- Aba: Formulários (migrados para perfil de usuário) -->
        <div id="tab-formularios" class="tab-panel hidden">
            <h3>Formulários</h3>
            <p>Os formulários de <strong>Doação</strong>, <strong>Adoção</strong> e <strong>Apadrinhamento</strong> foram movidos para o perfil do usuário.</p>
            <p>Para criar uma nova doação, solicitar adoção ou apadrinhar um animal, acesse o perfil do usuário em <a href="login.php">Minha Conta</a>.</p>
        </div>
    </section>

    <!-- backdrop usado para modals de edição -->
    <div id="modal-backdrop" class="hidden"></div>

    <script src="script.js"></script>
    <script src="admin.js"></script>

</body>
</html>
