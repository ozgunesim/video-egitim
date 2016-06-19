<?php
require('../../islemler/db.php');
$db = new DB();

$sonuc = $db->hoca_kayit_onayla($_GET['basvuru_onay']);

?>