<?php
    function sendQuery($username) {
        $curl = curl_init();

        $query = json_encode([
            "query" => "SELECT * FROM users WHERE username = '$username'"
        ]);

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://pellegrinelliandres5ie.altervista.org/remote/remote_connection.php',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $query,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        //curl_close($curl); (deprecato)
        return $response;

    }

    function getRefreshToken() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://pellegrinelliandres5ie.altervista.org/api/token/refresh', //http://localhost/compito_gpo_tpsit/api/token/refresh
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
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
?>