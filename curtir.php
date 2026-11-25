<?php
require_once "config.php";

if (isset($_GET["sair"])) {
    $_SESSION = [];
    session_destroy();
    header("Location: login.php");
    exit;
}

exigir_login();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: feed.php");
    exit;
}

$idPublicacao = filter_input(INPUT_POST, "id_publicacao", FILTER_VALIDATE_INT);
if (!$idPublicacao) {
    header("Location: feed.php");
    exit;
}

$existePost = $pdo->prepare("SELECT id_publicacao FROM publicacoes WHERE id_publicacao = ?");
$existePost->execute([$idPublicacao]);

if ($existePost->fetch()) {
    $verificar = $pdo->prepare("SELECT id_curtida FROM curtidas WHERE id_usuario = ? AND id_publicacao = ?");
    $verificar->execute([$_SESSION["usuario_id"], $idPublicacao]);
    $curtida = $verificar->fetch();

    if ($curtida) {
        $remover = $pdo->prepare("DELETE FROM curtidas WHERE id_curtida = ?");
        $remover->execute([$curtida["id_curtida"]]);
    } else {
        $inserir = $pdo->prepare("INSERT INTO curtidas (id_usuario, id_publicacao) VALUES (?, ?)");
        $inserir->execute([$_SESSION["usuario_id"], $idPublicacao]);
    }
}

header("Location: feed.php");
exit;
