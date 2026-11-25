
<?php

function inserirCurtida($con, $dados){
    $stmt = $con->prepare(
        "INSERT INTO curtidas (id_usuario, id_publicacao)
        VALUES (?, ?)"
    );

    $stmt->execute([
        $dados["id_usuario"],
        $dados["id_publicacao"]
    ]);

    return $con->lastInsertId();
}

function listarCurtidas($con){
    $stmt = $con->query("SELECT * FROM curtidas");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function listarCurtidasPublicacao($con, $id_publicacao){
    $stmt = $con->prepare(
        "SELECT * FROM curtidas WHERE id_publicacao = ?"
    );

    $stmt->execute([$id_publicacao]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function deletarCurtida($con, $dados){
    $stmt = $con->prepare(
        "DELETE FROM curtidas
        WHERE id_usuario = ? AND id_publicacao = ?"
    );

    $stmt->execute([
        $dados["id_usuario"],
        $dados["id_publicacao"]
    ]);
}

?>
