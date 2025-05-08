<?php
require ('conf.php');
function kysiKaupadeAndmed($sorttulp="nimetus", $otsisona=""){
    global $yhendus;
    $kask=$yhendus->prepare("
        SELECT kaubad.id, nimetus, grupinimi, hind
        FROM kaubad, kaubagrupid
        WHERE kaubad.kaubagrupi_id=kaubagrupid.id
        AND (nimetus LIKE '%$otsisona%' OR grupinimi LIKE '%$otsisona%')
        ORDER BY $sorttulp;
    ");
    //echo $yhendus->error;

    $otsisona=addslashes(stripslashes($otsisona));
    $kask->bind_result($id, $nimetus, $grupinimi, $hind);
    $kask->execute();
    $hoidla=array();
    while($kask->fetch()){
        $kaup=new stdClass();
        $kaup->id=$id;
        $kaup->nimetus=htmlspecialchars($nimetus);
        $kaup->grupinimi=htmlspecialchars($grupinimi);
        $kaup->hind=$hind;
        array_push($hoidla, $kaup);
    }
    return $hoidla;
}

function looRippMenyy($sqllause, $valikunimi, $valitudid=""){
    global $yhendus;
    $kask=$yhendus->prepare($sqllause);
    $kask->bind_result($id, $sisu);
    $kask->execute();
    $tulemus="<select name='$valikunimi'>";
    while($kask->fetch()){
        $lisand="";
        if($id==$valitudid){$lisand=" selected='selected'";}
        $tulemus.="<option value='$id' $lisand >$sisu</option>";
    }
    $tulemus.="</select>";
    return $tulemus;
}

function lisaGrupp($grupinimi){
global $yhendus;
$kask=$yhendus->prepare("INSERT INTO kaubagrupid (grupinimi)
VALUES (?)");
$kask->bind_param("s", $grupinimi);
$kask->execute();
}
function lisaKaup($nimetus, $kaubagrupi_id, $hind){
global $yhendus;
$kask=$yhendus->prepare("INSERT INTO
kaubad (nimetus, kaubagrupi_id, hind)
VALUES (?, ?, ?)");
$kask->bind_param("sid", $nimetus, $kaubagrupi_id, $hind);
$kask->execute();
}

function kustutaKaup($kauba_id){
global $yhendus;
$kask=$yhendus->prepare("DELETE FROM kaubad WHERE id=?");
$kask->bind_param("i", $kauba_id);
$kask->execute();
}

function muudaKaup($kauba_id, $nimetus, $kaubagrupi_id, $hind){
    global $yhendus;
    $kask=$yhendus->prepare("UPDATE kaubad SET nimetus=?, kaubagrupi_id=?, hind=?
WHERE id=?");
    $kask->bind_param("sidi", $nimetus, $kaubagrupi_id, $hind, $kauba_id);
    $kask->execute();
}
?>
<!--väljastab kõik andmetabelid andmed massivina
<pre>
<?php
print_r(kysiKaupadeAndmed());
?>
</pre>
-->