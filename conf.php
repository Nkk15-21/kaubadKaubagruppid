<?php
$serverinimi="localhost";
$kasutaja="nikkon";
$parool="12345";
$andmebaas="nikkon1";
$yhendus=new mysqli($serverinimi,$kasutaja,$parool,$andmebaas);
$yhendus->set_charset("utf8");

