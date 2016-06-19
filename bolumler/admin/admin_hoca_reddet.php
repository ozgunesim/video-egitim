<?php
require('../../islemler/db.php');	//veritabanı sınıfını barındıran php dosyası ekleniyor
$db = new DB();

$sonuc = $db->hoca_kayit_reddet($_GET['basvuru_sil']);		//hoca basvurusunun reddedildigi bilgisi verirabanına gonderiliyor

?>