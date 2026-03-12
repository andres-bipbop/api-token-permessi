<?php
    session_start();

    require "db_credentials.php";
    require "secret_pepper.php";
    require '../../vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $email = $_POST["email"];
        $salt = bin2hex(random_bytes(16));
        $password = $salt . '.' . hash("sha256", $salt . $_POST["password"] . $secretPepper);
        try {
            $link = mysqli_connect($servername, $username_db, $password_db, $database);
            if (!$link) {
                die("Connection failed: " . mysqli_connect_error());
            }

            $validationCode = mt_rand(100000, 999999);
            $status = "pending";

            $stmt = mysqli_stmt_init($link);
            mysqli_stmt_prepare($stmt, "INSERT INTO app_users (username, name, email, password, validationCode, Status) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssssss", $username, $name, $email, $password, $validationCode, $status);
            if (!mysqli_stmt_execute($stmt)) {
                die("Error: " . mysqli_stmt_error($stmt));
            }
        }
        catch (Exception $t) {
            echo $t;
            die();
        }

        $verify_link = "https://pellegrinelliandres5ie.altervista.org/informatica/login/verify.php?token=$validationCode";
    
        $subject = "Verifica il tuo account";
        $body = "Ciao $username,\n\n"
            . "Grazie per esserti unito!\n"
            . "Per completare la registrazione, clicca sul link seguente:\n\n"
            . "$verify_link\n\n";

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'localhost';
            $mail->Port = 1025;
            $mail->SMTPAuth = false;

            $mail->setFrom('info@theconduit.com', 'The Conduit');
            $mail->addAddress($email);

            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();

            echo "Email inviata!";
            $success = true;
        } catch (Exception $e) {
            echo $e;//"Errore: {$mail->ErrorInfo}";
            die();
        }

        mysqli_stmt_close($stmt);
        mysqli_close($link);
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
    <title>Register Form</title>
</head>

<body class="text-(--electric-blue) inter-normal h-screen flex flex-col overflow-hidden select-none">

    <header class="flex justify-between items-start px-6 py-5 border-b border-(--electric-blue)">
        <div>
            <p class="text-2xl font-medium uppercase tracking-widest2">The Conduit</p>
            <p class="text-[0.65rem] font-normal uppercase tracking-widest opacity-85 mt-0.5">Member Access</p>
        </div>
        <div class="text-[0.65rem] font-normal uppercase tracking-widest opacity-85 text-right leading-relaxed">
            Form — 002<br>Register
        </div>
    </header>

    <form id="registrationForm" class="flex flex-col flex-1" action="registrationForm.php" method="POST">

        <!-- 01 / Username -->
        <div class="flex-1 flex flex-col border-b border-(--electric-blue) relative group">
            <span class="absolute top-4 left-5 text-[0.55rem] uppercase tracking-[0.18em] opacity-35 z-10">01 / Username</span>
            <input type="text"
                class="flex-1 w-full bg-transparent border-none font-sans text-(--electric-blue) caret-blue px-5 pb-5 focus:outline-none"
                name="username" id="username" required
                style="font-size: clamp(2.8rem, 7vw, 6rem); font-weight: 500; letter-spacing: -0.03em; padding-top: 3.5rem;"/>
        </div>

        <!-- 02 / Email -->
        <div class="flex-1 flex flex-col border-b border-(--electric-blue) relative group">
            <span class="absolute top-4 left-5 text-[0.55rem] uppercase tracking-[0.18em] opacity-35 z-10">02 / Email</span>
            <input type="email"
                class="flex-1 w-full bg-transparent border-none font-sans text-(--electric-blue) caret-blue px-5 pb-5 focus:outline-none"
                name="email" id="email" required
                style="font-size: clamp(2.8rem, 7vw, 6rem); font-weight: 500; letter-spacing: -0.03em; padding-top: 3.5rem;"/>
        </div>

        <!-- 03 / Password -->
        <div class="flex-1 flex flex-col border-b border-(--electric-blue) relative group">
            <span class="absolute top-4 left-5 text-[0.55rem] uppercase tracking-[0.18em] opacity-35 z-10">03 / Password</span>
            <input type="password"
                class="flex-1 w-full bg-transparent border-none font-sans text-(--electric-blue) caret-blue px-5 pb-5 focus:outline-none"
                name="password" id="password" required
                style="font-size: clamp(2.8rem, 7vw, 6rem); font-weight: 500; letter-spacing: -0.03em; padding-top: 3.5rem;"/>
        </div>

        <!-- 04 / Submit -->
        <button type="submit"
            class="flex-1 w-full bg-(--electric-blue) text-white border-none flex items-end justify-between px-5 pb-5 hover:bg-white hover:text-(--electric-blue) group border-b border-(--electric-blue)"
            style="padding-top: 3.5rem;"
            id="send-form-button">
            <span class="text-[0.55rem] uppercase tracking-[0.18em] opacity-60 group-hover:opacity-40">04 / Submit</span>
            <span style="font-size: clamp(2.8rem, 7vw, 6rem); font-weight: 500; letter-spacing: -0.03em; line-height: 1;">register</span>
        </button>

    </form>

    <footer class="flex justify-between items-center px-5 py-3 border-t border-(--electric-blue) inter-normal shrink-0">
        <div class="text-[0.6rem] uppercase tracking-widest opacity-85 leading-relaxed">
            © The Conduit<br>All rights reserved
        </div>
        <div class="text-[0.6rem] uppercase tracking-widest opacity-85 leading-relaxed text-right">
            TC-REGISTER-002<br>Ver. 1.0
        </div>
    </footer>
porca puttana zio
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

<script>
    let successfulOperation = <?php echo $success ?>;
    if (successfulOperation) {
        document.body.innerHTML = '<h1 class="inter-medium" style="color: var(--electric-blue);">Check your email</h1>'
    }
</script>