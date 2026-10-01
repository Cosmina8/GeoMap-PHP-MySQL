<?php
session_start();
require_once __DIR__ . '/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $errors[] = "Completați username și parola.";
    } else {
        /* 🔐 Luăm și email-ul pentru control admin */
        $stmt = $pdo->prepare("SELECT id, username, email, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);

            /* ✅ Date salvate în sesiune */
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email']    = $user['email'];

            header('Location: ../index.php');
            exit;
        } else {
            $errors[] = "Username sau parolă incorectă.";
        }
    }
}
?>
<!doctype html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <title>Login - PAI 2025</title>
    <link rel="stylesheet" href="css/login.css">

    <!-- FontAwesome pentru icon-uri -->
    <script src="https://kit.fontawesome.com/a2d9d5a64d.js" crossorigin="anonymous"></script>
</head>

<body>

<div class="login-wrapper">

    <!-- STÂNGA -->
    <div class="login-left">
        <h1>Welcome to my personal website</h1>
        <p>
            Autentifică-te pentru a accesa pagina principală, proiectele,
            hobby-urile mele și aplicația Google Maps realizată pentru PAI.
        </p>
    </div>

    <!-- DREAPTA -->
    <div class="login-right">
        <h2>LOGIN</h2>

        <?php if (!empty($errors)): ?>
            <div style="color:red; font-size:14px; margin-bottom:15px;">
                <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">

            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Parola" required>
            </div>

            <button class="login-btn" type="submit">Autentificare</button>

        </form>

        <div class="signup-link">
            Nu ai cont? <a href="register.php">Creează cont</a>
        </div>

    </div>

</div>

</body>
</html>
