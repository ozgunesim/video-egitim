<?php

if(!isset($_SESSION)){	//tanimli session yoksa
    session_start(); 	//oturum baslat
}


require('db.php');
$db = new DB();			//siniftan turetme yapildi


if(isset($_POST['giris_yap'])){	//giris yapma islemi varsa
	$mail = $_POST['kul_mail'];
	$sifre = $_POST['kul_sifre'];

	if($db->uye_kontrol($mail,$sifre)){		// uye bulunursa
		header("Location: ../index.php?mesaj1");		//hatasiz anasayfaya don
	}else{
		//print_r($_POST);
		header("Location: ../index.php?hata1");	//hata ile anasayfaya don
	}
	
}


?>