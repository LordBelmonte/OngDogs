<?php
header('Content-Type: application/json; charset=utf-8');

// Endpoint de autenticação e gerenciamento de sessão
include_once __DIR__ . '/Usuarios.php';

session_start();

function resposta($erro, $mensagem, $dados = []){
    echo json_encode(['erro' => $erro, 'mensagem' => $mensagem, 'dados' => $dados]);
    exit;
}

try {
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'POST') {
        // Suporta login e logout via campo 'action'
        $action = isset($_POST['action']) ? $_POST['action'] : 'login';

        if ($action === 'logout') {
            // Destrói sessão
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
            resposta(false, 'Desconectado', []);
        }

        // Login
        $email = isset($_POST['email']) ? trim($_POST['email']) : null;
        $senha = isset($_POST['senha']) ? $_POST['senha'] : null;

        if (!$email || !$senha) {
            http_response_code(400);
            resposta(true, 'Email e senha são obrigatórios', []);
        }

        $res = Usuarios::autenticar($email, $senha);
        if ($res['erro']) {
            http_response_code(401);
            resposta(true, $res['mensagem'], []);
        }

        $user = $res['dados'];
        $_SESSION['user_id'] = $user['id_usuario'];

        resposta(false, 'Autenticado', ['id_usuario' => $user['id_usuario']]);
    }

    if ($method === 'GET') {
        // Retorna dados do usuário logado
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            resposta(true, 'Usuário não autenticado', []);
        }

        $id = $_SESSION['user_id'];
        $res = Usuarios::buscarUsuarioPeloId($id);
        if ($res['erro']) {
            http_response_code(404);
        }
        resposta($res['erro'], $res['mensagem'], $res['dados']);
    }

    // Método não permitido
    http_response_code(405);
    resposta(true, 'Método não permitido', []);

} catch (Exception $e) {
    http_response_code(500);
    resposta(true, $e->getMessage(), []);
}

?>
