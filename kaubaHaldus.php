<?php
require("conf.php");
session_start();
if (!isset($_SESSION['admin'])) {
    $_SESSION['admin'] = false;
}
if (!isset($_SESSION['kasutaja'])) {
    header("Location: login2.php");
    exit();
}

require("abifunktsioonid.php");

function isAdmin() {
    return isset($_SESSION['admin']) && $_SESSION['admin'];
}

// Lisamine ja kustutamine ainult adminile
if (isset($_REQUEST["grupilisamine"]) && isAdmin() && !empty(trim($_REQUEST["uuegrupinimi"]))) {
    if (grupinimiKontroll(trim($_REQUEST["uuegrupinimi"])) == 0) {
        lisaGrupp($_REQUEST["uuegrupinimi"]);
        header("Location: kaubaHaldus.php");
        exit();
    }
}

if (isset($_REQUEST["kaubalisamine"]) && isAdmin() && !empty(trim($_REQUEST["nimetus"]))) {
    lisaKaup($_REQUEST["nimetus"], $_REQUEST["kaubagrupi_id"], $_REQUEST["hind"]);
    header("Location: kaubaHaldus.php");
    exit();
}

if (isset($_REQUEST["kustutusid"]) && isAdmin()) {
    kustutaKaup($_REQUEST["kustutusid"]);
}

if (isset($_REQUEST["muutmine"]) && isAdmin()) {
    muudaKaup($_REQUEST["muudetudid"], $_REQUEST["nimetus"], $_REQUEST["kaubagrupi_id"], $_REQUEST["hind"]);
}

$kaubad = kysiKaupadeAndmed();
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Kaupade leht</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<p>Tere, <?= htmlspecialchars($_SESSION["kasutaja"]) ?>!</p>
<form action="logout.php" method="post">
    <input type="submit" value="Logi välja" name="logout">
</form>

<h1>Kaubad | Kaubagrupid</h1>

<?php if (isAdmin()): ?>
    <form action="kaubaHaldus.php">
        <h2>Kauba lisamine</h2>
        <dl>
            <dt>Nimetus:</dt>
            <dd><input type="text" name="nimetus" required /></dd>
            <dt>Kaubagrupp:</dt>
            <dd>
                <?= looRippMenyy("SELECT id, grupinimi FROM kaubagrupid", "kaubagrupi_id"); ?>
            </dd>
            <dt>Hind:</dt>
            <dd><input type="text" name="hind" required /></dd>
        </dl>
        <input type="submit" name="kaubalisamine" value="Lisa kaup" />

        <h2>Grupi lisamine</h2>
        <input type="text" name="uuegrupinimi" required />
        <input type="submit" name="grupilisamine" value="Lisa grupp" />
        <?php
        if (isset($_REQUEST["uuegrupinimi"]) && grupinimiKontroll(trim($_REQUEST["uuegrupinimi"])) > 0) {
            echo "<p style='color:red;'>Sisestatud grupinimi on juba olemas!</p>";
        }
        ?>
    </form>
<?php endif; ?>

<form action="kaubaHaldus.php">
    <h2>Kaupade loetelu</h2>
    <table>
        <tr>
            <th>Haldus</th>
            <th>Nimetus</th>
            <th>Kaubagrupp</th>
            <th>Hind</th>
        </tr>
        <?php foreach ($kaubad as $kaup): ?>
            <tr>
                <?php if (isset($_REQUEST["muutmisid"]) && intval($_REQUEST["muutmisid"]) == $kaup->id): ?>
                    <?php if (isAdmin()): ?>
                        <td>
                            <input type="submit" name="muutmine" value="Muuda" />
                            <input type="submit" name="katkestus" value="Katkesta" />
                            <input type="hidden" name="muudetudid" value="<?= $kaup->id ?>" />
                        </td>
                        <td><input type="text" name="nimetus" value="<?= htmlspecialchars($kaup->nimetus) ?>" /></td>
                        <td>
                            <?= looRippMenyy("SELECT id, grupinimi FROM kaubagrupid", "kaubagrupi_id", $kaup->kaubagrupi_id); ?>
                        </td>
                        <td><input type="text" name="hind" value="<?= $kaup->hind ?>" /></td>
                    <?php else: ?>
                        <td colspan="4">Muuda lubatud ainult adminile.</td>
                    <?php endif; ?>
                <?php else: ?>
                    <td>
                        <?php if (isAdmin()): ?>
                            <a href="kaubaHaldus.php?kustutusid=<?= $kaup->id ?>"
                               onclick="return confirm('Kas ikka soovid kustutada?')">x</a>
                            <a href="kaubaHaldus.php?muutmisid=<?= $kaup->id ?>">m</a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($kaup->nimetus) ?></td>
                    <td><?= htmlspecialchars($kaup->grupinimi) ?></td>
                    <td><?= $kaup->hind ?></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </table>
</form>

</body>
</html>
