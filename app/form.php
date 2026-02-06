<?php
    require "secret_pepper.php";
    require "send_token_requests.php";
    require "send_queries.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];
        try {
            $data = getUserData($username);
            var_dump($data);
            if ($data) {
                if ($data["status"] == "active") {
                    if (explode(".", $data["password"])[1] === hash("sha256", explode(".", $data["password"])[0] . $password . $secretPepper)) {
                        $refreshJWT = json_decode(getRefreshToken($data["username"], $data["id"]), true)["refreshToken"];
                        $accessJWT = json_decode(getAccessToken($refreshJWT), true)["accessToken"];
                        setcookie(name:"jwtAccess", value:$accessJWT, httponly:true);
                        setcookie(name:"jwtRefresh", value:$refreshJWT, httponly:true);
                        header("location: home.php");
                        die();
                    }
                    else {
                        echo "Invalid credentials";
                        die();
                    }
                }
                else {
                    echo "Account not verified";
                    die();
                }
            }
            else {
                echo "User not found";
                die();
            }
        }
        catch (Exception $t) {
            echo $t;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login Form</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" id="form-title">LOGIN</h5>
                        <form id="registrationForm" action="form.php" method="POST">
                            <div class="form-group" id="username-form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Username" required />
                            </div>
                            <div class="form-group mt-3" id="email-form-group" style="display: none;">
                                <label for="username">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email" />
                            </div>
                            <div class="form-group mt-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password" required />
                            </div>
                            <button type="submit" class="btn btn-danger mt-3" id="send-form-button">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
