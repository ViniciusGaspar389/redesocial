<?php


function inserirUsuario($con, $dados){
    $senhaHash = password_hash($dados["senha"], PASSWORD_DEFAULT);

    $stmt = $con->prepare(
        "INSERT INTO usuarios (nome, username, email, senha)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->execute([
        $dados["nome"],
        $dados["username"],
        $dados["email"],
        $senhaHash
    ]);

    return $con->lastInsertId();
}

function listarUsuario($con){
    $stmt = $con->query("SELECT * FROM usuarios");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function atualizarUsuario($con,$id,$dados){
    $stmt = $con->prepare(
        "UPDATE usuarios SET senha = ? WHERE id_usuario = ?"
    );
    $stmt ->execute([$dados["senha"],
                    $id]);
}
    


function deletarUsuario($con, $id){
    $stmt = $con->prepare(
        "DELETE FROM usuarios WHERE id_usuario = ?"
    );

    $stmt->execute([$id]);
}


function buscarUsuarioPorEmail($con, $email){

    $stmt = $con->prepare(
        "SELECT * FROM usuarios WHERE email = ?"
    );

    $stmt->execute([$email]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

