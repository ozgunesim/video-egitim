<?php
require('../../islemler/db.php');
$db = new DB();

$sonuc = $db->hoca_kayit_reddet($_GET['basvuru_sil']);

?>