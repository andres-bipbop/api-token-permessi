<?php
    require __DIR__ . '/vendor/autoload.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    class TokenEndpoints {

        private const ACCESS_TOKEN_KEY = "r7e9];gI#bQQ!(co<*?:89{EmGP?0R(r";
        private const REFRESH_TOKEN_KEY = "W71uX5&Hw}m|*^9wtobTRcU;)!4[OaR{";

        public static function generateRefreshToken($username, $id) {
            if (!self::checkCredentials($username, $id)) {
                http_response_code(401);
                echo json_encode(["http_code" => "401", "error" => "Bad request", "message" => "Missing id or username."]);
                exit;
            }

            $refreshPayload = array(
                "iss" => "https://pellegrinelliandres5ie.altervista.org",
                "aud" => "https://pellegrinelliandres5ie.altervista.org", //firebase jwt non lo controlla ma va messo per completteza
                "iat" => time(),
                "nbf" => time(),
                "exp" => time() + (60 * 60 * 24 * 7),
                "userdata" => [
                    "username" => $username,
                    "id" => $id
                ]
            );
            try {
                $refreshJWT = JWT::encode($refreshPayload, self::REFRESH_TOKEN_KEY,'HS256');
                http_response_code(201);
                echo json_encode(["message" => "Token generated.", "refreshToken" => $refreshJWT]);
            }
            catch (UnexpectedValueException $e) 
            { 
                http_response_code(500);
                echo json_encode(["error" => $e->getMessage(), "message" => "Internal Server Error."]);
            }
        }

        public static function generateAccessToken($username, $id) {
            if (!self::checkCredentials($username, $id)) {
                http_response_code(401);
                echo json_encode(["http_code" => "401", "error" => "Bad request", "message" => "Missing username."]);
                exit;
            }
            $accessPayload = array(
                "iss" => "https://pellegrinelliandres5ie.altervista.org/",
                "aud" => "https://pellegrinelliandres5ie.altervista.org", //firebase jwt non lo controlla ma va messo per completteza
                "iat" => time(),
                "nbf" => time(),
                "exp" => time() + (60 * 10),
                "userdata" => [
                    "username" => $username,
                    "id" => $id
                ]
            );
            try {
                $accessJWT = JWT::encode($accessPayload, self::ACCESS_TOKEN_KEY,'HS256');
                http_response_code(200);
                echo json_encode(["http_code" => "200", "message" => "Token generated.", "accessToken" => $accessJWT]);
            }
            catch (UnexpectedValueException $e) 
            {
            	http_response_code(500);
                echo json_encode(["http_code" => "500", "error" => $e->getMessage(), "message" => "Internal Server Error."]);
            }
        }

        public static function validateAccessToken($accessToken) {
            try {
                $decoded = JWT::decode($accessToken, new Key(self::ACCESS_TOKEN_KEY, "HS256"));
                http_response_code(200);
                echo json_encode(["http_code" => "200", "message" => "Access Granted.", "data" => $decoded]);
            } catch (Exception $e) {
                http_response_code(401);
                echo json_encode(["http_code" => "401", "error" => $e->getMessage(), "message" => "Access Denied, invalid Token."]);
            }
        }

        public static function validateRefreshToken($refreshToken) {
            try {
                $decoded = JWT::decode($refreshToken, new Key(self::REFRESH_TOKEN_KEY, "HS256"));
                $username = $decoded->userdata->username;
                $id = $decoded->userdata->id;
                self::generateAccessToken($username, $id);
            } catch (Exception $e) {
                http_response_code(401);
                echo json_encode(["http_code" => "401", "error" => $e->getMessage(), "message" => "Access Denied, invalid Token."]);
            }
        }
        private static function checkCredentials($username, $id) {
            if ($username === null || $id === null) {
                return false;
            } else return true;
        }
    }
?>