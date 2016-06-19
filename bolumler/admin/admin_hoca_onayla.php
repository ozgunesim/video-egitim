<?php
require('../../islemler/db.php');	//veritabanı sınıfını barındıran php dosyası ekleniyor
$db = new DB();

$sonuc = $db->hoca_kayit_onayla($_GET['basvuru_onay']);		//egitmenin basvurusunun onaylandıgı bilgisi veritabanına gonderiliyor

?>