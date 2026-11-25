<?php
// Configuração do front-end Socialize.
// Ajuste estes dados caso seu MySQL use outra configuração.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$banco = "instagram_db";
$usuario = "root";
$senha = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit("Não foi possível conectar ao banco de dados. Confira os dados em front/config.php e se o banco instagram_db existe.");
}

function proteger($valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}

function exigir_login(): void {
    if (empty($_SESSION["usuario_id"])) {
        header("Location: login.php");
        exit;
    }
}
