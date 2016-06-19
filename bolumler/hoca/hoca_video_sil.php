<?php
require('../../islemler/db.php');	//veritabanı sınıfını barındıran php dosyası ekleniyor
$db = new DB();

if(isset($_GET['id'])){		//egitmenin silecegi videonun idsi alınıyor
	$id = $_GET['id'];
	$db->video_sil($id);	//video veritabanından siliniyor
	unlink("../../video/" . $id . ".mp4");	//video dosyası siliniyor
	if(file_exists("../../img/thumbs/" . $id . "-lg.jpg"))		//video onizlemesi siliniyor
		unlink("../../img/thumbs/" . $id . "-lg.jpg");
}

?>