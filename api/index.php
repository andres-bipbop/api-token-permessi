<?php
	require("TokenEndpoints.php");

    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST");
    header("Access-Control-Allow-Headers: Content-Type");

    $json = file_get_contents("php://input");
    $data = json_decode($json, true);
    $requestMethod = $_SERVER["REQUEST_METHOD"];
	$request = $_SERVER["REQUEST_URI"];
    $segments = explode('/', ltrim($request, "/"));

    // -- INIZIO GESTIONE ENDPOINT PER CREAZIONE E VERIFICA TOKEN JWT -- //

    //primo contatto con api (es. a seguito della login)
    if ($segments[1] == 'token' && $segments[2] == 'refresh') {
        $username = $data["username"] ?? null;
        $id = $data["id"] ?? null;
        TokenEndpoints::generateRefreshToken($id, $username);
        exit;
    }

    //ottenere tutti gli header
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["error" => "Auth error", "message" => "Missing authorization header."]);
        exit;
    }

    //authHeader[0] = tipo ---- authHeader[1] = token
    $authHeader = explode(' ', $headers['Authorization'], 2);

    if ($authHeader[0] !== 'Bearer') {
        http_response_code(401);
        echo json_encode(["error" => "Auth error", "message" => "Invalid token type."]);
        exit;
    }

    //verify-access per accedere alle risorse dell'applicazione
    if ($segments[1] == 'token' && $segments[2] == 'verify-access') {
        TokenEndpoints::validateAccessToken($authHeader[1]);
    }

    //verify-refresh per ottener nuovo access token
    if ($segments[1] == 'token' && $segments[2] == 'verify-refresh') {
        TokenEndpoints::validateRefreshToken($authHeader[1]);
    }

    // -- FINE GESTIONE ENDPOINT PER CREAZIONE E VERIFICA TOKEN JWT -- //

    return null;
?>