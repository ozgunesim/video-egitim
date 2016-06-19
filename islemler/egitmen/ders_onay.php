<?php
require('../../islemler/db.php');	//veritabanı sınıfını barındıran php dosyası ekleniyor
$db = new DB();
if(isset($_POST['ID'])){	//egitmenin onaylayacagı ders idsi alınıyor
	$id = $_POST['ID'];

	$db->hoca_rezervasyon_onayla($id);	//egitmenin onayladıgı ders veritabanında guncelleniyor
}

?>
