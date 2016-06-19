<?php
require('../../islemler/db.php');	//veritabanı sınıfını barındıran php dosyası ekleniyor
$db = new DB();

if(isset($_POST['ID'])){
	$id = $_POST['ID'];
	$yetki = $_POST['yetki'];

	if($db->uye_yetkilendir($id,$yetki)){
		return true;
	}else{
		return false;
	}
}

?>