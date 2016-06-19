<?php
require('../../islemler/db.php');
require('../mesajlar.php');

$db = new DB();

if(isset($_POST)){
	$id=$_POST['ID'];
	$baslik = $_POST['baslik'];
	$metin = $_POST['metin'];
	if(isset($_FILES['file']))
		$tur = $_FILES['file']['type'];

	if($db->slayt_guncelle(mysql_escape_string($id),mysql_escape_string($baslik),mysql_escape_string($metin))){
		if(isset($tur)){
			if($tur == "image/jpeg"){
				$hedef = "../../img/slaytlar/" . $id . ".jpg";
				if (move_uploaded_file($_FILES["file"]["tmp_name"], $hedef)) {
					basari_mesaji('Slayt Güncellendi.');
				}else{
					hata_mesaji('Beklenmeyen Hata!');
				}
			}else{
				hata_mesaji('Bu bir resim değil!');
			}
		}else{
			basari_mesaji('Slayt Güncellendi.');
		}
	}else{
		hata_mesaji('Beklenmeyen Hata!');
	}
}

?>