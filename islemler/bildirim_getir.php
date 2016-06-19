<?php
if(isset($_POST['ID']))
	require 'db.php';
$db = new DB();
	$bildirimler = $db->bildirim_getir($_POST['ID']);	//veritabanında bildirimleri getiren fonksiyon cagırılıyor
	if($bildirimler && $bildirimler > 0){
		$renkler = ["","active"];
		foreach ($bildirimler as $satir) {		//okunmamis bildirimler renklendiriliyor
		?>
			<a href="ajanda.php" class="list-group-item <?php echo $renkler[$satir['durum']] ?> bildirim"><?php echo $satir['metin']; ?></a>
		<?php
		}
	}
?>