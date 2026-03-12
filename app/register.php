<?php
    session_start();

    require "secret_pepper.php";
    require "db_credentials.php";
    require 'vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $verify_link = "https://pellegrinelliandres5ie.altervista.org/informatica/login/verify.php?token=$validationCode";
    
    $subject = "Verifica il tuo account";
    $body = "Ciao $name,\n\n"
        . "Grazie per esserti registrato!\n"
        . "Per completare la registrazione, clicca sul link seguente:\n\n"
        . "$verify_link\n\n";

    //$headers = "From: noreply@pellegrinelliandres5ie.altervista.org\r\n";
    //$headers .= "Reply-To: noreply@pellegrinelliandres5ie.altervista.org\r\n";
    //$headers .= "X-Mailer: PHP/" . phpversion();

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
    } catch (Exception $e) {
        echo "Errore: {$mail->ErrorInfo}";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica Email</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="alert alert-success text-center shadow-sm p-4 rounded">
                    <h4 class="alert-heading mb-3">Ci siamo quasi!</h4>
                    <p>Abbiamo inviato un'email di verifica all'indirizzo che hai fornito.</p>
                    <hr>
                    <p>Per completare la registrazione, apri la tua casella email e clicca sul link di verifica.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
