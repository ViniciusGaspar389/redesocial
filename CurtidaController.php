
<?php

require_once __DIR__ . "/../Models/CurtidaModel.php";

function receberDadosCurtida(){
    $dados = json_decode(file_get_contents("php://input"), true);

    if(!is_array($dados)){
        http_response_code(400);
        echo json_encode([
            "erro" => "Envie um objeto JSON com os dados da curtida"
        ]);
        return null;
    }

    foreach(["id_usuario", "id_publicacao"] as $campo){
        if(!isset($dados[$campo]) ||
            filter_var($dados[$campo], FILTER_VALIDATE_INT,
            ["options" => ["min_range" => 1]]) === false){

            http_response_code(400);
            echo json_encode([
                "erro" => "O campo $campo deve ser um ID valido"
            ]);
            return null;
        }
    }

    return $dados;
}

function createCurtida($con){
    $dados = receberDadosCurtida();
    if($dados === null) return;

    $id = inserirCurtida($con, $dados);

    http_response_code(201);
    echo json_encode([
        "mensagem" => "Curtida registrada com sucesso!",
        "id_curtida" => $id
    ]);
}

function readCurtida($con){
    echo json_encode(listarCurtidas($con));
}

function readCurtidasPublicacao($con){
    $id = filter_var($_GET["id_publicacao"] ?? null,
        FILTER_VALIDATE_INT,
        ["options" => ["min_range" => 1]]);

    if($id === false){
        http_response_code(400);
        echo json_encode([
            "erro" => "Informe um ID de publicacao valido"
        ]);
        return;
    }

    echo json_encode(listarCurtidasPublicacao($con, $id));
}

function deleteCurtida($con){
    $dados = receberDadosCurtida();
    if($dados === null) return;

    deletarCurtida($con, $dados);

    echo json_encode([
        "mensagem" => "Curtida removida com sucesso!"
    ]);
}

?>
