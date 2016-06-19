<?php
if(isset($_POST['ID'])){
	require('db.php');
	$db = new DB();
	$bildirimler = $db->bildirim_sayi_getir($_POST['ID']);	//veritabanında bildirim sayısını getiren fonksiyon cagiriliyor

	if($bildirimler && $bildirimler > 0){
		echo $bildirimler;
	}
}
	
?>