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
            <a href="home.php#animais">Animais</a>
            <a href="home.php#como-ajudar">Como Ajudar</a>
            <a href="home.php#missao">Missão</a>
            <a href="home.php#contato">Contato</a>
            <a href="home.php#formularios">Formulários</a>
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

        <!-- Formulário de edição -->
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
    </section>

    <script src="admin.js"></script>

</body>
</html>
