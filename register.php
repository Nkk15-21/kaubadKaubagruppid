<?php
require('conf.php');
session_start();
global $yhendus;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = htmlspecialchars(trim($_POST["login"] ?? ""));
    $pass1 = $_POST["pass1"] ?? "";
    $pass2 = $_POST["pass2"] ?? "";

    if (empty($login) || empty($pass1) || empty($pass2)) {
        $error = "Kõik väljad peavad olema täidetud!";
    } elseif ($pass1 !== $pass2) {
        $error = "Paroolid ei kattu!";
    } else {
        // Kontrollime, kas kasutajanimi on juba olemas
        $stmt = $yhendus->prepare("SELECT id FROM kasutajad WHERE kasutaja=?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Kasutajanimi on juba võetud!";
        } else {
            // Salvestame uue kasutaja
            $sool = 'cool';
            $krypt = crypt($pass1, $sool);
            $stmt = $yhendus->prepare("INSERT INTO kasutajad (kasutaja, parool, onadmin) VALUES (?, ?, 0)");
            $stmt->bind_param("ss", $login, $krypt);
            $stmt->execute();

            // Automaatselt sisse logimine
            $_SESSION["kasutaja"] = $login;
            $_SESSION["admin"] = false;

            header("Location: kaubaHaldus.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Registreerimine</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Registreerimine</h1>

<?php if (!empty($error)): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" action="register.php">
    <label for="login">Kasutajanimi:</label><br>
    <input type="text" id="login" name="login" required><br><br>

    <label for="pass1">Parool:</label><br>
    <input type="password" id="pass1" name="pass1" required><br><br>

    <label for="pass2">Korda parooli:</label><br>
    <input type="password" id="pass2" name="pass2" required><br><br>

    <input type="submit" value="Registreeri">
</form>

<p>Kui sul on konto, <a href="login2.php">logi sisse</a>.</p>
</body>
</html>
