<?php
if(isset($_POST['metin'])){
require('db.php');	//veritabanı sınıfını barındıran php dosyası ekleniyor
$db = new DB();
$metin = $_POST['metin'];
$kime = $_POST['kime'];
$db->bildirim_yolla($metin,$kime);	//veritabanına bildirim ekleniyor
}

?>