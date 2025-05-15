<?php include('conf.php'); ?>
<?php
session_start();
/*if (isset($_SESSION['tuvastamine'])) {
    header('Location: kaubaHaldus.php');
    exit();
}*/
global $yhendus;
//kontrollime kas väljad on täidetud
if (!empty($_POST['login']) && !empty($_POST['pass'])) {
    //eemaldame kasutaja sisestusest kahtlase pahna
    $login = htmlspecialchars(trim($_POST['login']));
    $pass = htmlspecialchars(trim($_POST['pass']));
    //SIIA UUS KONTROLL
    $sool = 'cool';
    $krypt = crypt($pass, $sool);
    //kontrollime kas andmebaasis on selline kasutaja ja parool
    $paring = $yhendus->prepare("SELECT kasutaja, parool, onadmin FROM kasutajad 
                                 WHERE kasutaja=? AND parool=?");
    $paring->bind_param('ss', $login, $krypt);
    $paring->bind_result($kasutaja, $parool, $onadmin);
    $paring->execute();
    //$valjund = mysqli_query($yhendus, $paring);
    //kui on, siis loome sessiooni ja suuname
    /*if (mysqli_num_rows($valjund)==1) {
        $_SESSION['tuvastamine'] = 'misiganes';
        header('Location: kaubaHaldus.php');
    } else {
        echo "kasutaja või parool on vale";
    }*/
    if($paring->fetch() && $parool == $krypt) {

        $_SESSION['kasutaja'] = $login;
        if($onadmin==1) {
            $_SESSION['admin'] = true;
        }
        header('location:kaubaHaldus.php');
        $yhendus->close();
    }   else {
        echo "kasutaja või parool on vale";
        $yhendus->close();
    }
}
?>
<h1>Login</h1>
<link rel="stylesheet" href="style.css">
<p><a href="register.php">Registreeru uue kasutajana</a></p>

<form action="" method="post">
    <table>
        <tr>
            <td>
                <label for="login">Login:</label>
            </td>
            <td>
                <input type="text"  id="login" name="login">
            </td>
        </tr>
        <tr>
            <td>
                <label for="login">Password:</label>
            </td>
            <td>
                <input type="password" id="login" name="pass">
            </td>
        </tr>
        <tr>
            <td></td>
            <td>    <input type="submit" value="Logi sisse"></td>
        </tr>
    </table>
</form>