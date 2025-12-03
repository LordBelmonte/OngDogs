<?php
// admin.php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo - ONG Dogs</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f5f5f5;
            padding: 20px;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px;
            background-color: #8B4513; 
            z-index: 1000;
            flex-wrap: wrap;
            box-sizing: border-box;
        }
        header .logo {
            height: 70px;
            width: auto;
            border-radius: 50%;
        }

        header nav {
        display: flex;
        gap: 30px; 
        align-items: center;
        flex-wrap: wrap;
        padding: 0 10px;
        max-width: 70%;
        justify-content: flex-end;
        }

        h1 {
            text-align: center;
            margin-bottom: 25px;
        }

        section{
            padding: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #0077cc;
            color: white;
        }

        button {
            padding: 6px 12px;
            margin-right: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-delete {
            background: #d9534f;
            color: white;
        }

        .btn-edit {
            background: #f0ad4e;
            color: white;
        }

        .btn-refresh {
            background: #5cb85c;
            color: white;
            margin: 65px 0px 12px 0px;
            float: right;
        }

        .hidden {
            display: none;
        }

        #edit-form {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <header>
        <img src="imagens/logo_ong.png" alt="Logo Instituto Eu Sou Bicho" class="logo">
        <h1>Painel Administrativo</h1>
            <nav>
                <a href="home.php">Home</a>
            </nav>
    </header><br>

    <section>
    <button class="btn-refresh" onclick="carregarUsuarios()">🔄 Atualizar Lista</button>
    </section>

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

    <script src="admin.js"></script>

</body>
</html>
