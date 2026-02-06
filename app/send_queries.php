<?php

    function getUserData($username) {
        $curl = curl_init();
        
        $query = json_encode([
            "query" => "SELECT * FROM app_users WHERE username = '$username'"
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
        $data = json_decode($response, true)[0];

        //curl_close($curl); (deprecato)
        return $data;

    }

    function getUserMemberships($id) {
        $curl = curl_init();
        
        $query = json_encode([
            "query" => "SELECT 
    u.id AS user_id,
    u.username,
    u.name AS user_name,
    u.email,
    u.status AS user_status,
    s.id AS space_id,
    s.name AS space_name,
    r.role_id,
    r.title AS role_title,
    r.description AS role_description,
    p.permission_id,
    p.title AS permission_title,
    p.description AS permission_description
FROM app_users u
INNER JOIN app_members m ON u.id = m.user_id
INNER JOIN app_spaces s ON m.space_id = s.id
INNER JOIN app_roles r ON m.role_id = r.role_id
LEFT JOIN app_role_composition rc ON r.role_id = rc.role_id
LEFT JOIN app_permissions p ON rc.permission_id = p.permission_id
WHERE u.id = '$id';
"
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
        $data = json_decode($response, true)[0];

        //curl_close($curl); (deprecato)
        return $data;

    }

    function sendCustomQuery($query) {
        $curl = curl_init();

        $query = json_encode([
            "query" => "$query"
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
        $data = json_decode($response, true);

        //curl_close($curl); (deprecato)
        return $data;

    }
?>
