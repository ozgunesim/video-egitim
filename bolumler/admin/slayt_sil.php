<?php
require('../../islemler/db.php');
require('../mesajlar.php');

$db = new DB();

if(isset($_POST['id'])){
	$id = $_POST['id'];
	if($db->slayt_sil($id)){
		$hedef = "../../img/slaytlar/" . $id . ".jpg";
		unlink($hedef);
		uyari_mesaji('Silindi.');
	}
}
?>