
<?php

require_once __DIR__ . "/../models/UsuarioModel.php";

function loginUsuario($con){

    $dados = json_decode(file_get_contents("php://input"), true);

    if(!isset($dados["email"]) || !isset($dados["senha"]) ||
       trim($dados["email"]) === "" || trim($dados["senha"]) === ""){

        http_response_code(400);

        echo json_encode([
            "erro" => "Informe o email e a senha"
        ]);

        return;
    }

    $usuario = buscarUsuarioPorEmail($con, $dados["email"]);

    if(!$usuario || !password_verify($dados["senha"], $usuario["senha"])){

        http_response_code(401);

        echo json_encode([
            "erro" => "Email ou senha incorretos"
        ]);

        return;
    }

    session_set_cookie_params([
        "lifetime" => 0,
        "path" => "/redesocial/",
        "httponly" => true,
        "samesite" => "Lax"
    ]);

    session_start();

    session_regenerate_id(true);

    $_SESSION["id_usuario"] = $usuario["id_usuario"];
    $_SESSION["nome"] = $usuario["nome"];
    $_SESSION["username"] = $usuario["username"];
    $_SESSION["email"] = $usuario["email"];

    echo json_encode([
        "mensagem" => "Login realizado com sucesso!",
        "usuario" => [
            "id_usuario" => $usuario["id_usuario"],
            "nome" => $usuario["nome"],
            "username" => $usuario["username"]
        ]
    ]);
}
