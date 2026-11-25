<?php
require_once "config.php";
exigir_login();

$erro = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $legenda = trim($_POST["legenda"] ?? "");
    $imagemUrl = trim($_POST["imagem_url"] ?? "");

    if ($legenda === "" && $imagemUrl === "") {
        $erro = "Escreva uma legenda ou informe o endereço de uma imagem.";
    } elseif ($imagemUrl !== "" && !filter_var($imagemUrl, FILTER_VALIDATE_URL)) {
        $erro = "O endereço da imagem precisa ser uma URL válida.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO publicacoes (id_usuario, legenda, imagem_url, data_publicacao) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$_SESSION["usuario_id"], $legenda, $imagemUrl]);
        header("Location: feed.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova publicação | Socialize</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topo">
    <a class="logo logo-menor" href="feed.php">Socialize</a>
    <nav><a href="feed.php">Voltar ao feed</a></nav>
</header>
<main class="form-container">
    <h2>Criar publicação</h2>
    <p class="subtitulo">Compartilhe algo com a comunidade.</p>
    <?php if ($erro): ?><div class="alerta"><?= proteger($erro) ?></div><?php endif; ?>
    <form method="POST">
        <label for="legenda">Legenda</label>
        <textarea id="legenda" name="legenda" rows="5" maxlength="2000" placeholder="O que você está pensando?"></textarea>
        <label for="imagem_url">URL da imagem (opcional)</label>
        <input id="imagem_url" type="url" name="imagem_url" placeholder="https://exemplo.com/imagem.jpg">
        <button type="submit" class="botao">Publicar</button>
    </form>
</main>
</body>
</html>
