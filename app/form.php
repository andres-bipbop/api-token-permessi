<?php
    require "secret_pepper.php";
    require "send_requests.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];
        try {
            $result = sendQuery($username);
            $data = json_decode($result, true)[0];
            if ($data) {
                if ($data["status"] == "active") {
                    if (explode(".", $data["password"])[1] === hash("sha256", explode(".", $data["password"])[0] . $password . $secretPepper)) {
                        $refreshJWT = json_decode(getRefreshToken(), true)["refreshToken"];
                        $accessJWT = json_decode(getAccessToken($refreshJWT), true)["accessToken"];
                        var_dump($refreshJWT);
                        var_dump($accessJWT);
                        if(setcookie(name:"jwtAccess", value:$accessJWT, httponly:true)) echo "Access token cookie set<br>";
                        if(setcookie(name:"jwtRefresh", value:$refreshJWT, httponly:true)) echo "Refresh token cookie set<br>";
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