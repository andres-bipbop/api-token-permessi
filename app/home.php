<?php
    require "send_token_requests.php";
    require "send_queries.php";

    if (!isset($_COOKIE['jwtAccess']) || !isset($_COOKIE['jwtRefresh'])) {
        header("location: form.php");
        exit;
    }

    $jwtAccess = $_COOKIE['jwtAccess'];
    $jwtRefresh = $_COOKIE['jwtRefresh'];

    $tokenData = checkToken($jwtAccess, $jwtRefresh); //(send_token_resquests.php)
    $userData = getUserMemberships($tokenData["data"]["userdata"]["id"]); //(send_queries.php)

    var_dump($userData);

    if ($userData["role_title"] === "Admin User") {
        $users = sendCustomQuery("SELECT 
    u.username,
    u.name,
    r.title AS role
FROM app_users u
INNER JOIN app_members m ON u.id = m.user_id
INNER JOIN app_roles r ON m.role_id = r.role_id
WHERE u.status = 'active';
");
    }



    /*

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
    */
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
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-body">
                                <?php echo "<h5 class='card-title' id='form-title'>Hey, {$userData['user_name']} </h5>\n
                                <h6 class='card-subtitle mb-2 mt-2 text-muted'>{$userData['username']}</h6>\n
                                <p class='card-text'>Your role: {$userData['role_title']}</p>"
                                ?>
                                <button class="btn btn-danger" onclick="window.location.href='logout.php'">Logout</button>
                                <button class="btn btn-danger" onclick="window.location.href='visualizzaUtente.php?deleteUser=<?php echo $userData['username']; ?>'">Delete this user</button>
                                <?php
                                    if ($userData["role_title"] === "Standard User") {
                                        echo "<button class='btn btn-danger' onclick=\"window.location.href = 'visualizzaUtente.php?removeAdmin={$userData['username']}'\">Remove this admin</button>";
                                    }
                                ?>
                                <?php if ($userData["role_title"] === "Admin User"): ?>

                                <!-- Modal -->
                                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="staticBackdropLabel">Roles & Permissions</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                    <input type="radio" class="btn-check" name="btnradio" id="btnradioStandardMember" autocomplete="off">
                                                    <label class="btn btn-outline-primary" for="btnradioStandardMember">Standard Member</label>
                                                    <input type="radio" class="btn-check" name="btnradio" id="btnradioCollaborator" autocomplete="off">
                                                    <label class="btn btn-outline-primary" for="btnradioCollaborator">Collaborator</label>
                                                    <input type="radio" class="btn-check" name="btnradio" id="btnradioAdministrator" autocomplete="off">
                                                    <label class="btn btn-outline-primary" for="btnradioAdministrator">Administrator</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
                                                    if ($user["username"] == $userData["username"]) continue;
                                                    echo "<tr><th scope='row'>{$user["username"]}</th><td>{$user["name"]}</td><td>{$user["role"]}</td>";
                                                    echo "<td><button class='btn btn-secondary me-2' onclick=\"window.location.href = 'visualizzaUtente.php?deleteUser={$user["username"]}'\">Delete</button>";
                                                    if($user["role"] == "admin"){
                                                        echo "<button class='btn btn-danger' onclick=\"window.location.href = 'visualizzaUtente.php?removeAdmin={$user["username"]}'\">Remove Admin</button>";
                                                    } else {
                                                        echo "<button class='btn btn-primary' onclick=\"window.location.href = 'visualizzaUtente.php?addAdmin={$user["username"]}'\">Add Admin</button>";
                                                    }
                                                    echo "<button type='button' onclick=\"addCheckedAttribute('btnradio{$user["role"]}')\"  class='btn btn-primary ms-2' data-bs-toggle='modal' data-bs-target='#staticBackdrop'>Manage Role</button></td>";
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

<script>
    function addCheckedAttribute(id) {
        document.getElementById(id).setAttribute("checked", "true");
    }
</script>
