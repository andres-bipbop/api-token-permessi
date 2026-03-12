<?php
require "send_token_requests.php";
require "send_queries.php";

if (!isset($_COOKIE['jwtAccess']) || !isset($_COOKIE['jwtRefresh'])) {
    header("location: loginForm.php");
    exit;
}

$tokenData = checkToken($_COOKIE['jwtAccess'], $_COOKIE['jwtRefresh']);
$userId = $tokenData["data"]["userdata"]["id"];
$userData = getUserMemberships($userId);

$isAdmin = false;
$isPublisher = false;

if ($userData["role_title"] === "Admin") {
    $isAdmin = true;
} else if ($userData["role_title"] === "Publisher") {
    $isPublisher = true;
}

$users = $isAdmin ? sendCustomQuery(
    "SELECT u.username, u.name, r.title AS role
    FROM app_users u
    INNER JOIN app_members m ON u.id = m.user_id
    INNER JOIN app_roles r ON m.role_id = r.role_id
    WHERE u.status = 'active'") : [];
?>


<!doctype html>
<html lang="en">
<head>
    <title>Title</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"/>
</head>
<body>
<header></header>
<main>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">

                        <!-- profilo utnte -->
                        <h5 class="card-title">Hey, <?= htmlspecialchars($userData['user_name']) ?></h5>
                        <h6 class="card-subtitle mb-2 mt-2 text-muted"><?= htmlspecialchars($userData['username']) ?></h6>
                        <p class="card-text">Your role: <?= htmlspecialchars($userData['role_title']) ?></p>

                        <button class="btn btn-danger" onclick="window.location.href='logout.php'">Logout</button>
                        <button class="btn btn-danger" onclick="window.location.href='visualizzaUtente.php?deleteUser=<?= htmlspecialchars($userData['username']) ?>'">Delete this user</button>

                        <?php if ($isAdmin): ?>
                            <button class="btn btn-warning" onclick="window.location.href='visualizzaUtente.php?removeAdmin=<?= htmlspecialchars($userData['username']) ?>'">Remove Admin</button>
                        <?php endif; ?>

                        <!-- admin -->
                        <?php if ($isAdmin): ?>
                            <h5 class="mt-5">User List</h5>
                            <table class="table table-hover mt-2">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <?php if ($user['username'] === $userData['username']) continue; ?>
                                        <tr>
                                            <th scope="row"><?= htmlspecialchars($user['username']) ?></th>
                                            <td><?= htmlspecialchars($user['name']) ?></td>
                                            <td><?= htmlspecialchars($user['role']) ?></td>
                                            <td>
                                                <button class="btn btn-secondary me-2" onclick="window.location.href='visualizzaUtente.php?deleteUser=<?= htmlspecialchars($user['username']) ?>'">Delete</button>

                                                <?php if ($user['role'] === 'admin'): ?>
                                                    <button class="btn btn-danger" onclick="window.location.href='visualizzaUtente.php?removeAdmin=<?= htmlspecialchars($user['username']) ?>'">Remove Admin</button>
                                                <?php else: ?>
                                                    <button class="btn btn-primary" onclick="window.location.href='visualizzaUtente.php?addAdmin=<?= htmlspecialchars($user['username']) ?>'">Add Admin</button>
                                                <?php endif; ?>

                                                <button type="button" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#modalManageRole" onclick="setRoleModal('<?= htmlspecialchars($user['role']) ?>')">Manage Role</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Modal Roles -->
                            <div class="modal fade" id="modalManageRole" data-bs-backdrop="static" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Roles & Permissions</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="btn-group" role="group">
                                                <input type="radio" class="btn-check" name="btnradio" id="radioStandardMember" autocomplete="off">
                                                <label class="btn btn-outline-primary" for="radioStandardMember">Standard Member</label>

                                                <input type="radio" class="btn-check" name="btnradio" id="radioCollaborator" autocomplete="off">
                                                <label class="btn btn-outline-primary" for="radioCollaborator">Collaborator</label>

                                                <input type="radio" class="btn-check" name="btnradio" id="radioPublisher" autocomplete="off">
                                                <label class="btn btn-outline-primary" for="radioPublisher">Publisher</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($isPublisher): ?>
                            <div class="mt-4">
                                <h5>Nuovo articolo</h5>
                        
                                <div class="mb-3">
                                    <label for="post-title" class="form-label">Titolo</label>
                                    <input type="text" class="form-control" id="post-title"
                                        placeholder="Inserisci il titolo dell'articolo…" maxlength="120">
                                    <div class="form-text text-end">
                                        <span id="title-count">0</span>/120
                                    </div>
                                </div>
                        
                                <div class="mb-3">
                                    <label class="form-label">Corpo dell'articolo</label>
                        
                                    <div class="btn-toolbar mb-0 p-2 border border-bottom-0 rounded-top bg-light" role="toolbar">
                                        <div class="btn-group btn-group-sm me-2">
                                            <button type="button" class="btn btn-outline-secondary" data-fmt="bold"><b>B</b></button>
                                            <button type="button" class="btn btn-outline-secondary" data-fmt="italic"><i>I</i></button>
                                            <button type="button" class="btn btn-outline-secondary" data-fmt="underline"><u>U</u></button>
                                        </div>
                                        <div class="btn-group btn-group-sm me-2">
                                            <button type="button" class="btn btn-outline-secondary" data-fmt="h2">H2</button>
                                            <button type="button" class="btn btn-outline-secondary" data-fmt="h3">H3</button>
                                        </div>
                                        <div class="btn-group btn-group-sm me-2">
                                            <button type="button" class="btn btn-outline-secondary" data-fmt="ul">• Lista</button>
                                            <button type="button" class="btn btn-outline-secondary" data-fmt="ol">1. Lista</button>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-secondary" data-fmt="quote">&ldquo;&rdquo;</button>
                                            <button type="button" class="btn btn-outline-secondary" data-fmt="link">&#128279; Link</button>
                                        </div>
                                    </div>
                        
                                    <textarea class="form-control rounded-top-0" id="post-body" rows="8"
                                            placeholder="Scrivi qui il contenuto dell'articolo…"></textarea>
                                    <div class="form-text text-end">
                                        <span id="body-count">0</span> caratteri
                                    </div>
                                </div>
                        
                                <div class="mb-3">
                                    <label class="form-label">Immagini</label>
                                    <!-- Nessun onclick/ondragover/ondrop inline: gestiti tutti da post-editor.js -->
                                    <div id="drop-zone" class="border rounded p-4 text-center"
                                        style="cursor:pointer; border-style:dashed !important;">
                                        <p class="mb-1 text-muted">
                                            Trascina immagini qui oppure
                                            <span class="text-primary text-decoration-underline">sfoglia</span>
                                        </p>
                                        <small class="text-muted">PNG, JPG, WEBP — max 5 MB</small>
                                        <input type="file" id="post-images" accept="image/*" multiple style="display:none">
                                    </div>
                                    <div id="image-previews" class="d-flex flex-wrap gap-2 mt-2"></div>
                                </div>
                        
                                <div class="d-flex justify-content-between align-items-center">
                                    <small id="save-status" class="text-muted">Bozza non salvata</small>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-secondary" id="btn-draft">Salva bozza</button>
                                        <button type="button" class="btn btn-primary" id="btn-publish">Pubblica</button>
                                    </div>
                                </div>
                            </div>
                        
                            <!-- Caricato solo per i publisher, quando gli elementi esistono nel DOM -->
                            <script src="/website/compito_gpo_tpsit/app/post_editor.js" defer></script>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<footer></footer>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

<script>
    const roleRadioMap = {
        'StandardMember': 'radioStandardMember',
        'Collaborator':   'radioCollaborator',
        'Publisher':      'radioPublisher',
    };

    function setRoleModal(role) {
        // Deseleziona tutti
        Object.values(roleRadioMap).forEach(id => {
            document.getElementById(id).checked = false;
        });
        // Seleziona il ruolo corrente
        const radioId = roleRadioMap[role];
        if (radioId) document.getElementById(radioId).checked = true;
    }
</script>
</body>
</html>