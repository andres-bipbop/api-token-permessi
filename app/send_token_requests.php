<?php

    function getRefreshToken($username, $id) {
        $curl = curl_init();

        $userdata = json_encode([
            "username" => $username,
            "id" => $id
        ]);

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://pellegrinelliandres5ie.altervista.org/api/token/refresh', //http://localhost/compito_gpo_tpsit/api/token/refresh
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>$userdata,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        return $response;
    }

    function getAccessToken($refreshJWT) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://pellegrinelliandres5ie.altervista.org/api/token/verify-refresh', //http://localhost/compito_gpo_tpsit/api/token/verify-refresh
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '. $refreshJWT
        ),
        ));

        $response = curl_exec($curl);

        return $response;
    }

    function verifyAccessToken($accessJWT) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://pellegrinelliandres5ie.altervista.org/api/token/verify-access', //http://localhost/compito_gpo_tpsit/api/token/verify-refresh
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '. $accessJWT
        ),
        ));

        $response = curl_exec($curl);

        return $response;
    }

    //check del token: se non valida access token,
    //prova a validare refresh token e genera un nuovo access token,
    //se non è valido refresh token, torna alla login form
    function checkToken($jwtAccess, $jwtRefresh) {
        $data = json_decode(verifyAccessToken($jwtAccess),true);
        if ($data["http_code"] != "200") {
            $data = json_decode(getAccessToken($jwtRefresh), true);
            if ($data["http_code"] != "200") {
                header("location: form.php");
                exit;
            } else {
                return checkToken($data["accessToken"], $jwtRefresh);
            }
        }
        return $data;
    }
?>
