<?php
    require "secret_pepper.php";
    require "send_token_requests.php";
    require "send_queries.php";

    if (isset($_COOKIE['jwtAccess']) || isset($_COOKIE['jwtRefresh'])) {
        header("location: home.php");
        exit;
    }

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
                        setcookie("jwtAccess", $accessJWT, httponly:true);
                        setcookie("jwtRefresh", $refreshJWT, httponly:true);
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
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../../output.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <title>Login Form</title>
</head>

<body class="text-(--electric-blue) inter-normal h-screen flex flex-col overflow-hidden select-none">

    <header class="flex justify-between items-start px-6 py-5 border-b border-(--electric-blue)">
        <div>
            <p class="text-2xl font-medium uppercase tracking-widest2">The Conduit</p>
            <p class="text-[0.65rem] fonr-normal uppercase tracking-widest opacity-85 mt-0.5">Member Access</p>
        </div>
            <div class="text-[0.65rem] font-normal uppercase tracking-widest opacity-85 text-right leading-relaxed">
            Form — 001<br>Login
            </div>
    </header>

    <form id="registrationForm" class="flex flex-col flex-1" action="loginForm.php" method="POST">
        <div class="flex-1 flex flex-col border-b border-(--electric-blue) relative group" id="username-form-group">
            <span class="absolute top-4 left-5 text-[0.55rem] uppercase tracking-[0.18em] opacity-35 z-10">01 / Email</span>
            <input type="" 
            class="flex-1 w-full bg-transparent border-none font-sans text-(--electric-blue) caret-blue px-5 pb-5 focus:outline-none"
            name="username" id="username" required
            style="font-size: clamp(2.8rem, 7vw, 6rem); font-weight: 500; letter-spacing: -0.03em; padding-top: 3.5rem;"/>
        </div>

        <div class="flex-1 flex flex-col border-b border-(--electric-blue) relative group">
            <span class="absolute top-4 left-5 text-[0.55rem] uppercase tracking-[0.18em] opacity-35 z-10">02 / Password</span>
            <input type="password" class="flex-1 w-full bg-transparent focus:outline-none border-none font-sans text-blue caret-blue px-5 pb-5" name="password" id="password" required 
            style="font-size: clamp(2.8rem, 7vw, 6rem); font-weight: 500; letter-spacing: -0.03em; padding-top: 3.5rem;"/>
        </div>

        <button type="submit"class="flex-1 w-full bg-(--electric-blue) text-white border-none flex items-end justify-between px-5 pb-5 hover:bg-white hover:text-(--electric-blue) group border-b border-blue"
        style="padding-top: 3.5rem;"
        id="send-form-button">
            <span class="text-[0.55rem] uppercase tracking-[0.18em] opacity-60 group-hover:opacity-40">03 / Submit</span>
            <span style="font-size: clamp(2.8rem, 7vw, 6rem); font-weight: 500; letter-spacing: -0.03em; line-height: 1;">login</span>
        </button>
    </form>

    <footer class="flex justify-between items-center px-5 py-3 border-t border-(--electric-blue) inter-normal shrink-0">
        <div class="text-[0.6rem] uppercase tracking-widest opacity-85 leading-relaxed">
        © The Conduit<br>All rights reserved
        </div>
        <div class="text-[0.6rem] uppercase tracking-widest opacity-85 leading-relaxed text-right">
        TC-LOGIN-001<br>Ver. 1.0
        </div>
  </footer>
</body>
</html>

<style>
    :root {
        --electric-blue: #0b06ff;
    }

   .inter-medium {
        font-family: "Inter", sans-serif;
        font-optical-sizing: auto;
        font-weight: 500;
        font-style: normal;
    }
    .inter-bold {
        font-family: "Inter", sans-serif;
        font-optical-sizing: auto;
        font-weight: 800;
        font-style: bold;
    }
    .inter-normal {
        font-family: "Inter", sans-serif;
        font-optical-sizing: auto;
        font-weight: 300;
        font-style: normal;
    }
</style>