<?php
session_start();

/* Protecție sesiune */
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

/* ADMIN */
$ADMIN_EMAIL = 'cosmina@email.com';
$isAdmin = (isset($_SESSION['email']) && $_SESSION['email'] === $ADMIN_EMAIL);
?>
<!doctype html>
<html lang="ro">
<head>
    <meta charset="utf-8">
    <title>Hartă - PAI 2025</title>

    <!-- stil general -->
    <link rel="stylesheet" href="../css/site.css">
    <!-- stil map -->
    <link rel="stylesheet" href="css/map.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

<!-- HEADER -->
<header class="main-header">
    <nav class="nav-nav">
        <div class="nav-bar">
            <nav class="nav-links">
                <li><a href="../index.php">Acasă</a></li>
                <li><a href="../cv.php">CV</a></li>
                <li><a href="../proiecte.php">Proiecte</a></li>
                <li><a href="../hobby.php">Hobby</a></li>
                <li><a href="map.php" class="active">Map</a></li>
                <li><a href="logout.php">Logout</a></li>
            </nav>
        </div>
    </nav>
</header>

<!-- HARTA -->
<div id="map"></div>

<!-- FORMULAR ADĂUGARE — DOAR ADMIN -->
<?php if ($isAdmin): ?>
<div id="add-point-box">
    <h3>Adaugă un punct pe hartă</h3>

    <label>Latitudine:</label><br>
    <input type="text" id="lat"><br>

    <label>Longitudine:</label><br>
    <input type="text" id="lng"><br>

    <label>Descriere:</label><br>
    <input type="text" id="desc"><br>

    <button id="addPointBtn">Adaugă</button>
    <p id="resultMsg"></p>
</div>
<?php endif; ?>

<!-- SCRIPT -->
<script>
const IS_ADMIN = <?= $isAdmin ? 'true' : 'false' ?>;

let map;
let markers = [];

function initMap() {
    const center = { lat: 44.4268, lng: 26.1025 }; // București
    map = new google.maps.Map(document.getElementById("map"), {
        center,
        zoom: 6,
    });

    loadMarkers();
}

function loadMarkers() {
    $.getJSON("get_points.php")
        .done(function(data) {
            markers.forEach(m => m.setMap(null));
            markers = [];

            data.forEach(function(p) {
                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(p.lat), lng: parseFloat(p.lng) },
                    map: map,
                    title: p.description || ''
                });

                const info = new google.maps.InfoWindow({
                    content: '<strong>' + (p.description || 'Punct fără descriere') + '</strong>'
                });

                marker.addListener('click', () => info.open(map, marker));

                /* ȘTERGERE — DOAR ADMIN */
                if (IS_ADMIN) {
                    marker.addListener("rightclick", () => {
                        if (confirm("Ștergi acest punct?")) {
                            deletePoint(p.id);
                        }
                    });
                }

                markers.push(marker);
            });
        })
        .fail(() => alert("Eroare la încărcarea markerelor!"));
}

/* ADAUGARE */
$(function() {
    $("#addPointBtn").on("click", function() {
        const lat = $("#lat").val().trim();
        const lng = $("#lng").val().trim();
        const desc = $("#desc").val().trim();

        if (!lat || !lng) {
            $("#resultMsg").text("Introduceți latitudinea și longitudinea.");
            return;
        }

        $.post("add_point.php", { lat, lng, description: desc })
            .done(function(data) {
                $("#resultMsg").text(data.message || "Punct adăugat!");
                loadMarkers();
            })
            .fail(function(xhr) {
                const j = xhr.responseJSON;
                $("#resultMsg").text((j && j.error) || "Eroare la server.");
            });
    });
});

/* ȘTERGERE */
function deletePoint(id) {
    $.post("delete_point.php", { id })
        .done(function() {
            loadMarkers();
        })
        .fail(function(xhr) {
            const j = xhr.responseJSON;
            alert((j && j.error) || "Eroare la ștergere.");
        });
}
</script>

<script async defer
 src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBJNRBYaYIvzxX1_vSq69iV6YOEaImCMQs&callback=initMap">
</script>

</body>
</html>
