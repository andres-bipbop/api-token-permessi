<?php
    require "send_requests.php";

    function checkToken($jwtAccess, $jwtRefresh = null) {
        $data = json_decode(verifyAccessToken($jwtAccess),true);
        if ($data["message"] != "Access Granted.") {
            $data = json_decode(getAccessToken($jwtRefresh), true);
            if ($data["message"] != "Access Granted.") {
                header("location: form.php");
                exit;
            }
            checkToken($jwtAccess);
        }
        return $data["data"]->userdata;
    }

    if (!isset($_COOKIE['jwtAccess']) || !isset($_COOKIE['jwtRefresh'])) {
        header("location: form.php");
        exit;
    }

    $jwtAccess = $_COOKIE['jwtAccess'];
    $jwtRefresh = $_COOKIE['jwtRefresh'];

    $userData = checkToken($jwtAccess);

    var_dump($userData);


    /*
    function getUsers($link) {
        $result = mysqli_query($link, "SELECT username, name, role FROM users");
        $users = [];
        while($row = mysqli_fetch_assoc($result)){
            $users[] = $row;
        }
        return $users;
    }

    if (isset($_GET["deleteUser"])) {
        $username = $_GET["deleteUser"];
        $stmt = mysqli_prepare($link, "DELETE FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($_SESSION["username"] === $username) {
            header("Location: logout.php");
            exit();
        }
        getUsers($link);
    }

    if (isset($_GET["removeAdmin"])) {
        $username = $_GET["removeAdmin"];
        $stmt = mysqli_prepare($link, "UPDATE users SET role = 'user' WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($_SESSION["username"] === $username) {
            header("Location: visualizzaUtente.php");
            exit();
        }
        getUsers($link);
    }

    if (isset($_GET["addAdmin"])) {
        $username = $_GET["addAdmin"];
        $stmt = mysqli_prepare($link, "UPDATE users SET role = 'admin' WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        getUsers($link);
    }

    $users = getUsers($link);
?>

<!doctype html>
<html lang="en">
    <head>
        <title>Title</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />

        <!-- Bootstrap CSS v5.2.1 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
    </head>

    <body>
        <header>
            <!-- place navbar here -->
        </header>
        <main>
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <?php echo "<h5 class='card-title' id='form-title'>Hey, {$_SESSION['name']} </h5>\n
                                <h6 class='card-subtitle mb-2 mt-2 text-muted'>{$_SESSION['username']}</h6>\n
                                <p class='card-text'>Your role: {$_SESSION['role']}</p>"
                                ?>
                                <button class="btn btn-danger" onclick="window.location.href='logout.php'">Logout</button>
                                <button class="btn btn-danger" onclick="window.location.href='visualizzaUtente.php?deleteUser=<?php echo $_SESSION['username']; ?>'">Delete this user</button>
                                <?php
                                    if ($_SESSION["role"] === "admin") {
                                        echo "<button class='btn btn-danger' onclick=\"window.location.href = 'visualizzaUtente.php?removeAdmin={$_SESSION['username']}'\">Remove this admin</button>";
                                    }
                                ?>
                                <?php if ($_SESSION["role"] === "admin"): ?>

                                    <h5 class="mt-5">User List</h5>
                                    <table class="table table-hover mt-2">
                                        <thead>
                                            <tr>
                                            <th scope="col">Username</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Role</th>
                                            <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                foreach($users as $key=>$user){
                                                    if ($user["username"] === $_SESSION["username"]) continue;
                                                    echo "<tr><th scope='row'>{$user["username"]}</th><td>{$user["name"]}</td><td>{$user["role"]}</td>";
                                                    echo "<td><button class='btn btn-secondary me-2' onclick=\"window.location.href = 'visualizzaUtente.php?deleteUser={$user["username"]}'\">Delete</button>";
                                                    if($user["role"] === "admin"){
                                                        echo "<button class='btn btn-danger' onclick=\"window.location.href = 'visualizzaUtente.php?removeAdmin={$user["username"]}'\">Remove Admin</button>";
                                                    } else {
                                                        echo "<button class='btn btn-primary' onclick=\"window.location.href = 'visualizzaUtente.php?addAdmin={$user["username"]}'\">Add Admin</button></td>";
                                                    }
                                                    echo "</tr>";
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer>
            <!-- place footer here -->
        </footer>
        <!-- Bootstrap JavaScript Libraries -->
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
*/