<?php
require('../../islemler/db.php');	//veritabanı sınıfını barındıran php dosyası ekleniyor
$db = new DB();
if(isset($_POST['ID'])){	//egitmenin reddedecegi dersin idsi aliniyor
	$id = $_POST['ID'];

	$db->hoca_rezervasyon_reddet($id);	//egitmenin reddettigi ders bilgisi veritabanında guncelleniyor

}

?>
