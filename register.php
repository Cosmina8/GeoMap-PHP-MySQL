<?php
session_start();
require_once __DIR__ . '/db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($username === '' || $password === '' || $confirm === '') {
        $errors[] = "Completați toate câmpurile.";
    } elseif ($password !== $confirm) {
        $errors[] = "Parolele nu coincid.";
    } else {
        // verificăm dacă user există deja
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $errors[] = "Acest username există deja.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hash]);
            $success = true;
        }
    }
}
?>
<!doctype html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <title>Înregistrare - PAI</title>

    <link rel="stylesheet" href="css/register.css">

    <script src="https://kit.fontawesome.com/a2d9d5a64d.js" crossorigin="anonymous"></script>
</head>

<body>

<div class="register-wrapper">

    <!-- PANOU STÂNGA -->
    <div class="register-left">
        <h1>Create an account</h1>
        <p>
            Creează-ți un cont pentru a accesa website-ul personal,
            proiectele, CV-ul și aplicația Google Maps.
        </p>
    </div>

    <!-- PANOU DREAPTA -->
    <div class="register-right">

        <h2>Înregistrare</h2>

        <?php if ($success): ?>
            <p style="color:green; font-weight:600; margin-bottom:15px;">
                Cont creat! <a href="login.php">Autentifică-te</a>
            </p>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div style="color:red; margin-bottom:15px;">
                <?php foreach ($errors as $e) echo htmlspecialchars($e) . "<br>"; ?>
            </div>
        <?php endif; ?>

        <form method="post">

            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Email / Username" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Parola" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm" placeholder="Confirmă parola" required>
            </div>

            <button class="register-btn" type="submit">Creează cont</button>

        </form>

        <div class="login-link">
            Ai deja cont? <a href="login.php">Autentificare</a>
        </div>

    </div>

</div>

</body>
</html>
