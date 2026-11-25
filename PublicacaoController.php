
<?php

require_once __DIR__ . "/../Models/PublicacaoModel.php";

function receberDadosPublicacao(){
    $dados = json_decode(file_get_contents("php://input"), true);

    if(!is_array($dados)){
        http_response_code(400);
        echo json_encode([
            "erro" => "Envie um objeto JSON com os dados da publicacao"
        ]);
        return null;
    }

    if(!isset($dados["id_usuario"]) ||
        filter_var($dados["id_usuario"], FILTER_VALIDATE_INT,
        ["options" => ["min_range" => 1]]) === false){

        http_response_code(400);
        echo json_encode([
            "erro" => "Informe um id de usuario valido"
        ]);
        return null;
    }

    foreach(["legenda", "imagem_url"] as $campo){
        if(isset($dados[$campo]) && !is_string($dados[$campo])){
            http_response_code(400);
            echo json_encode([
                "erro" => "O campo $campo deve ser um texto"
            ]);
            return null;
        }
    }

    $dados["legenda"] = $dados["legenda"] ?? null;
    $dados["imagem_url"] = $dados["imagem_url"] ?? null;

    return $dados;
}

function receberIDPublicacao(){
    $id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT,
        ["options" => ["min_range" => 1]]);

    if($id === false){
        http_response_code(400);
        echo json_encode([
            "erro" => "Informe um id de publicacao valido"
        ]);
        return null;
    }

    return $id;
}

function createPublicacao($con){
    $dados = receberDadosPublicacao();
    if($dados === null) return;

    $id = inserirPublicacao($con, $dados);

    http_response_code(201);
    echo json_encode([
        "mensagem" => "Publicacao criada com sucesso!",
        "id_publicacao" => $id
    ]);
}

function readPublicacao($con){
    echo json_encode(listarPublicacoes($con));
}

function updatePublicacao($con){
    $id = receberIDPublicacao();
    if($id === null) return;

    $dados = receberDadosPublicacao();
    if($dados === null) return;

    atualizarPublicacao($con, $id, $dados);

    echo json_encode([
        "mensagem" => "Publicacao atualizada com sucesso!"
    ]);
}

function deletePublicacao($con){
    $id = receberIDPublicacao();
    if($id === null) return;

    deletarPublicacao($con, $id);

    echo json_encode([
        "mensagem" => "Publicacao deletada com sucesso!"
    ]);
}

?>
