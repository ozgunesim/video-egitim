<?php
require('../../islemler/db.php');	//veritabanı sınıfını barındıran php dosyası ekleniyor
$db = new DB();

if(isset($_GET['uye_sil']) && trim($_GET['uye_sil']) != ""){	//silinece üye idsi ile birlikte veritabanı fonksiyonuna yollanıyor
	$sonuc = $db->uye_sil($_GET['uye_sil']);
}

?>