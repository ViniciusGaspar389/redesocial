<?php
require_once "config.php";
exigir_login();

$sql = "SELECT p.id_publicacao, p.id_usuario, p.legenda, p.imagem_url, p.data_publicacao,
               u.nome, u.username,
               (SELECT COUNT(*) FROM curtidas c WHERE c.id_publicacao = p.id_publicacao) AS total_curtidas,
               (SELECT COUNT(*) FROM curtidas c2 WHERE c2.id_publicacao = p.id_publicacao AND c2.id_usuario = ?) AS usuario_curtiu
        FROM publicacoes p
        INNER JOIN usuarios u ON u.id_usuario = p.id_usuario
        ORDER BY p.id_publicacao DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION["usuario_id"]]);
$publicacoes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed | Socialize</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topo">
    <a class="logo logo-menor" href="feed.php">Socialize</a>
    <nav>
        <span>Olá, <?= proteger($_SESSION["usuario_nome"] ?? "usuário") ?></span>
        <a href="criar_postagem.php">Criar publicação</a>
        <a href="curtir.php?sair=1">Sair</a>
    </nav>
</header>

<main class="feed-container">
    <div class="feed-titulo">
        <h2>Seu feed</h2>
        <a class="botao botao-link" href="criar_postagem.php">+ Nova publicação</a>
    </div>

    <?php if (!$publicacoes): ?>
        <section class="vazio">
            <h3>Seu feed está vazio por enquanto</h3>
            <p>Faça a primeira publicação para começar!</p>
            <a class="botao botao-link" href="criar_postagem.php">Criar publicação</a>
        </section>
    <?php endif; ?>

    <?php foreach ($publicacoes as $post): ?>
        <article class="post-card">
            <div class="post-autor">
                <div class="avatar"><?= proteger(mb_substr($post["nome"], 0, 1, "UTF-8")) ?></div>
                <div><strong><?= proteger($post["nome"]) ?></strong><small>@<?= proteger($post["username"]) ?></small></div>
            </div>

            <?php if (!empty($post["imagem_url"])): ?>
                <img class="post-imagem" src="<?= proteger($post["imagem_url"]) ?>" alt="Imagem da publicação"
                     onerror="this.style.display='none'">
            <?php endif; ?>

            <div class="post-conteudo">
                <?php if (!empty($post["legenda"])): ?><p><?= nl2br(proteger($post["legenda"])) ?></p><?php endif; ?>
                <div class="post-acoes">
                    <form method="POST" action="curtir.php">
                        <input type="hidden" name="id_publicacao" value="<?= (int)$post["id_publicacao"] ?>">
                        <button class="curtir <?= $post["usuario_curtiu"] ? "curtido" : "" ?>" type="submit">
                            <?= $post["usuario_curtiu"] ? "♥ Curtido" : "♡ Curtir" ?>
                        </button>
                    </form>
                    <span><?= (int)$post["total_curtidas"] ?> curtida(s)</span>
                </div>
                <small class="data-post"><?= proteger($post["data_publicacao"] ?? "") ?></small>
            </div>
        </article>
    <?php endforeach; ?>
</main>
</body>
</html>
