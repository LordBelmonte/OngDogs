<?php
header('Content-Type: application/json; charset=utf-8');

// Caminhos baseados no diretório atual
$base = __DIR__ . "/../";

// Incluir todos os services automaticamente (evita precisar listar manualmente)
foreach (glob($base . "app/services/*.php") as $serviceFile) {
    include_once $serviceFile;
}
include_once $base . "utils/util.php";

// Captura a URL do PATH_INFO
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';

if ($path == "" || $path == "/") {
    echo FormatarMensagemJson(true, "Endpoint incorreto (nenhuma rota informada).", []);
    exit;
}

// Remove barras
$path = trim($path, "/");

// Quebra por "/"
$url = explode("/", $path);

// Primeira parte deve ser api
if ($url[0] !== "api") {
    echo FormatarMensagemJson(true, "Endpoint incorreto (esperado /api).", []);
    exit;
}

// Remove "api"
array_shift($url);

// Se não houver service, retorna mensagem clara
if (count($url) === 0 || $url[0] === "") {
    echo FormatarMensagemJson(true, "Service não informado.", []);
    exit;
}

// Nome do service
$serviceName = ucfirst($url[0]) . "Service";
array_shift($url);

// Método HTTP
$method = strtolower($_SERVER["REQUEST_METHOD"]);

try {
    if (!class_exists($serviceName)) {
        throw new Exception("Service '$serviceName' não encontrado.");
    }

    $service = new $serviceName();

    if (!method_exists($service, $method)) {
        throw new Exception("Método '$method' não suportado por '$serviceName'.");
    }

    // Executa a operação
    $response = call_user_func_array([$service, $method], $url);

    http_response_code(200);
    echo FormatarMensagemJson(
        $response['erro'],
        $response['mensagem'],
        $response['dados']
    );

} catch (Exception $erro) {
    http_response_code(500);
    echo FormatarMensagemJson(true, $erro->getMessage(), []);
}
