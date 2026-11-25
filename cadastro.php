<?php
require_once "config.php";

if (!empty($_SESSION["usuario_id"])) {
    header("Location: feed.php");
    exit;
}

$erro = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST["nome"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senhaDigitada = $_POST["senha"] ?? "";

    if ($nome === "" || $username === "" || $email === "" || $senhaDigitada === "") {
        $erro = "Preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Digite um e-mail válido.";
    } elseif (strlen($senhaDigitada) < 6) {
        $erro = "A senha precisa ter pelo menos 6 caracteres.";
    } else {
        $verificar = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = ? OR username = ? LIMIT 1");
        $verificar->execute([$email, $username]);

        if ($verificar->fetch()) {
            $erro = "Este e-mail ou nome de usuário já está cadastrado.";
        } else {
            $hash = password_hash($senhaDigitada, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, username, email, senha, data_cadastro) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$nome, $username, $email, $hash]);

            $_SESSION["usuario_id"] = (int)$pdo->lastInsertId();
            $_SESSION["usuario_nome"] = $nome;
            $_SESSION["usuario_username"] = $username;
            header("Location: feed.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar conta | Socialize</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
    <main class="auth-card">
        <h1 class="logo">Socialize</h1>
        <p class="subtitulo">Crie sua conta e comece a compartilhar.</p>
        <?php if ($erro): ?><div class="alerta"><?= proteger($erro) ?></div><?php endif; ?>
        <form method="POST">
            <label for="nome">Nome completo</label>
            <input id="nome" type="text" name="nome" maxlength="100" required>
            <label for="username">Nome de usuário</label>
            <input id="username" type="text" name="username" maxlength="50" required>
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" maxlength="120" required>
            <label for="senha">Senha</label>
            <input id="senha" type="password" name="senha" minlength="6" required>
            <button type="submit" class="botao">Criar conta</button>
        </form>
        <p class="troca-pagina">Já tem uma conta? <a href="login.php">Entrar</a></p>
    </main>
</body>
</html>
