<?php
require('../../islemler/db.php');
$db = new DB();

if(isset($_GET['uye_sil']) && trim($_GET['uye_sil']) != ""){
	$sonuc = $db->uye_sil($_GET['uye_sil']);
}

?>