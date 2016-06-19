<?php
require('../../islemler/db.php');	//veritabanı sınıfını barındıran php dosyası ekleniyor
$db = new DB();

if(isset($_GET['id'])){
	$id = $_GET['id'];									//silinecek videonun idsi alınıyor
	$db->video_sil($id);								//video veritabanından siliniyor
	unlink("../../video/" . $id . ".mp4");						//videonun dosyası siliniyor
	if(file_exists("../../img/thumbs/" . $id . "-lg.jpg"))		//videonun onizlemesi siliniyor
		unlink("../../img/thumbs/" . $id . "-lg.jpg");
}

?>