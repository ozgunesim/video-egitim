<?php
	if(isset($_POST['video_konu']) && !isset($_POST['video_aciklamasi'])){				//eger ders konusu secildiyse bu dersin videolarını listele
		$link = "sonuc.php?kelime=" . $db->kategori_adi_getir($_POST['video_konu']);
		?>
			 <meta http-equiv="Refresh" content="0; url=<?php echo $link; ?>">
		<?php
	}
?>
<form action="" method="post" enctype="multipart/form-data" id="ders_form">
	<div class="form-group">
		<strong>Ders</strong>
		<select class="form-control" name="video_kategori" id="ders">
		<option disabled selected>Ders Seçin</option>
			<?php
				$dizi = $db->kategori_getir();		//ders baslıgı secilmisse cagırılan bu php dosyasında secilen baslıgın ders konuları listeleniyor
				foreach ($dizi as $satir) {
					echo "<option value='"  . $satir['ID']  . "'>" . $satir['adi'] . "</option>";
				}
			?>
		</select>
		<div id="konu">

		</div>
	</div>
</form>