<?php
require('../../islemler/db.php');
require('../mesajlar.php');

$db = new DB();

if(isset($_POST)){
	$baslik = $_POST['baslik'];
	$metin = $_POST['metin'];
	$tur = $_FILES['file']['type'];
	if($tur == "image/jpeg"){
		$id = $db->slayt_kaydet(mysql_escape_string($baslik),mysql_escape_string($metin));
		if($id){
			$hedef = "../../img/slaytlar/" . $id . ".jpg";
			if (move_uploaded_file($_FILES["file"]["tmp_name"], $hedef)) {
				basari_mesaji("Slayt Kaydedildi.<span id='id_al' lastid='" . $id . "'></span>");
			}
		}
	}else{
		hata_mesaji('Bu bir resim değil!');
	}


}

?>