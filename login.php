<?php
require_once "config.php";

if (!empty($_SESSION["usuario_id"])) {
    header("Location: feed.php");
    exit;
}

$erro = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $senhaDigitada = $_POST["senha"] ?? "";

    if ($email === "" || $senhaDigitada === "") {
        $erro = "Preencha o e-mail e a senha.";
    } else {
        $stmt = $pdo->prepare("SELECT id_usuario, nome, username, email, senha FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        // Aceita senhas criadas por este front (password_hash) e senhas antigas em texto puro.
        $senhaValida = $usuario && (
            password_verify($senhaDigitada, $usuario["senha"]) ||
            hash_equals((string)$usuario["senha"], (string)$senhaDigitada)
        );

        if ($senhaValida) {
            session_regenerate_id(true);
            $_SESSION["usuario_id"] = (int)$usuario["id_usuario"];
            $_SESSION["usuario_nome"] = $usuario["nome"];
            $_SESSION["usuario_username"] = $usuario["username"];
            header("Location: feed.php");
            exit;
        } else {
            $erro = "E-mail ou senha incorretos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | Socialize</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
    <main class="auth-card">
        <h1 class="logo">Socialize</h1>
        <p class="subtitulo">Compartilhe seus momentos.</p>
        <?php if ($erro): ?><div class="alerta"><?= proteger($erro) ?></div><?php endif; ?>
        <form method="POST">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" required autocomplete="email">
            <label for="senha">Senha</label>
            <input id="senha" type="password" name="senha" required autocomplete="current-password">
            <button type="submit" class="botao">Entrar</button>
        </form>
        <p class="troca-pagina">Ainda não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
    </main>
</body>
</html>
