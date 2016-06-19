<?php
require('../../islemler/db.php');
$db = new DB();
if(isset($_POST['ID'])){	//iptal edilecek ders idsi aliniyor
	$id = $_POST['ID'];

	$sonuc = $db->hoca_rezervasyon_iptal($id);	//hocanın dersi iptal ettigi bilgisi veritabanında guncelleniyor
	if($sonuc !== true)
		echo $sonuc;
}

?>
