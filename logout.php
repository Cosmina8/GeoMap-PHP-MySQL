<?php
session_start();

// ștergem toate variabilele din sesiune
session_unset();

// distrugem sesiunea complet
session_destroy();

// redirecționăm către pagina de login
header("Location: login.php");
exit();
