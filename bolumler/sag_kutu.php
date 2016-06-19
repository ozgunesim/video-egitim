<?php
/*
	
	bu dosya sayfanın sag bolmesindeki aracları icerir

*/

if(isset($_GET['hata1'])){		//kullanıcı girişi hatası varsa bildir
	//giriş yapılamadı hatası ver
	hata_mesaji("Kullanici Girişi Yapılamadı.");
}elseif(isset($_GET['mesaj1'])){	//kullanıcı girişi başarılıysa bildir
	bilgi_mesaji("Giriş Başarılı. Hoşgeldin <strong>" . $_SESSION['uye_adi'] . "</strong>");
}
?>
  

<div class="panel panel-default">
  <div class="panel-heading"><h4><span class="glyphicon glyphicon-list-alt"></span> Dersler</h4></div>
  <div class="panel-body" id="sag_kutu">

  	<div id="menu">
  	<div class="panel list-group" style="margin-bottom:0;">
	  <?php
	  	$kategoriler = $db->kategori_getir();				
	  	foreach ($kategoriler as $satir) {		//veritabanindan cekilen dersler ve konular listeleniyor
	  		if(isset($satir['alt']) && count($satir['alt'])>0){
	  		?>
	  			<a href="#" class="list-group-item" data-parent="menu" data-toggle="collapse" data-target="#<?php echo "div-" . $satir['ID']; ?>"><span class="glyphicon glyphicon-chevron-right"></span> <?php echo $satir['adi'] ?></a>
	  			<div id="<?php echo "div-" . $satir['ID']; ?>" class="sublinks collapse" >
	  			<?php

		  			foreach ($satir['alt'] as $alt_kategori) {
		  				?>
		  				<a href="sonuc.php?kelime=<?php echo $alt_kategori['adi']; ?>" class="list-group-item small"><span class="glyphicon glyphicon-chevron-right" style="margin-left:15px;"></span> <?php echo $alt_kategori['adi']; ?></a>
		  				<?php
		  			}

	  			?>
				</div>

	  			<?php


	  		}else{
	  			?>
	  			<a href="#" class="list-group-item"><span class="glyphicon glyphicon-chevron-right"></span> <?php echo $satir['adi'] ?></a>
	  			<?php
	  		}
	  	}
	  			//ders ve konular listelendi
	  ?>
  	</div>
  	</div>
  </div>
</div>

<?php
if(isset($_SESSION['oturum']) && $_SESSION['oturum'] === true && $_SESSION['uye_yetki'] != 1){	//eger ogrenci veya egitmen girisi varsa takvim ve ajanda kısayolu gosteriliyor
?>
<div class="panel panel-default">
	<div class="panel-heading"> <span class="glyphicon glyphicon-list-alt"></span><b>Özel Ders Takvimi</b></div>
	<div class="panel-body">
		<div id="takvim"></div>
		<a href="ajanda.php" class="btn btn-block btn-info">Ajandaya Git</a>
	</div>
</div>
<?php
}
?>
<?php
include('bolumler/haber.php');	//haberler ekleniyor
?>

<div class="panel panel-default">
	<div class="panel-heading"> <span class="glyphicon glyphicon-list-alt"></span><b>Eğitmen Listesi</b></div>
	<div class="panel-body">
	<div class="panel list-group" id="hoca_kutu">
	<?php
		$dizi = $db->hoca_listesi_getir();	//siteye kayıtli egitmenler listeleniyor
		foreach ($dizi as $satir) {
			?>
			<a href="hoca_detay.php?id=<?php echo $satir['ID']; ?>" class="list-group-item small"><?php echo $satir['adi']; ?></a>

			<?php
		}
	?>
	</div>
	</div>
</div>