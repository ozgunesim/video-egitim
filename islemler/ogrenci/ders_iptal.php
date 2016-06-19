<?php
require('../../islemler/db.php');	//veritabanı sınıfını barındıran php dosyası ekleniyor
$db = new DB();
if(isset($_POST['ID'])){	//ogrencinin iptal edecegi ders idsi aliniyor
	$id = $_POST['ID'];

	$sonuc = $db->ogrenci_rezervasyon_iptal($id);	//ogrencinin iptal ettigi ders veritabanında guncelleniyor
	if($sonuc !== true)
		echo $sonuc;
}

?>
