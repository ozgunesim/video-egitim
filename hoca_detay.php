<?php
/*

	bu sayfa egitmen bilgisinin verildigi sayfadır

*/
if(!isset($_SESSION)){	//oturum degiskenleri olusturulmadıysa olusturuluyor
    session_start();
}
require("bolumler/baslik.php");	//hazır baslik bilgisi eklentisi require edildi 
?>
<html lang="en">
	<head>	
		<?php
			baslik_bilgisi_ekle("Eğitmen Bilgileri"); //baslik bilgisi eklendi
		?>
	</head>
	<body>
		<div class="container">
			<?php menu_yukle(); ?>
		</div>
		<div class="container" id="ana_container" style="margin-top:70px; padding:0px;">
			<div class="col-md-9 well" style="min-height:88.5vh;">
				<?php
				if(isset($_GET['id'])){		//egitmen id'si aliniyor
					$id = $_GET['id'];
					$uye_bilgi = $db->uye_bilgi_getir($id);		//egitmenin uyelik bilgileri veritabanından cekiliyor

					echo "<h4>" . $uye_bilgi['uye_adi'] . "</h4>";
					echo "<h4 class='small'>" . $uye_bilgi['uye_mail'] . "</h4>";
					echo "<div class='well'>";

					$hakkinda = $db->hoca_bilgi_getir($id);		//egitmenin akademik bilgileri veritabanından cekiliyor
					echo $hakkinda['bilgi'];


					echo "</div>";

					echo "<h4 class='small'>Eğitmenin Verdiği Dersler</h4>";
					echo "<div class='well'>";

					$dizi = $db->hoca_videolari_listele(1,1000,$id);	//hocanın yukledigi video listesi veritabanından alınıyor

					function icinde($dizi,$eleman){				//yuklenen videoların konusu yazdırılacak ancak aynı konuyu 1'den fazla kez yazdırmamak icin kontrol yapılıyor
						foreach ($dizi as $satir) {
							if($eleman == $satir['konu']){
								return true;
								break;
							}
						}
						return false;
					}


					$yazilanlar = array();	//yazdirilmis konuların tutuldugu dizi

					if(!empty($dizi)){
						foreach ($dizi as $satir) {
							if(!icinde($yazilanlar,$satir['video_kategori'])){	//tekrar yazdırmamak icin kontrol ediliyor
								//eger daha once yazdırılmadıysa diziye aliniyor
								$yazilanlar[count($yazilanlar)]['konu'] = $satir['video_kategori'];
								$yazilanlar[count($yazilanlar)-1]['adet'] = 1;
							}else{
								//daha once yazdırıldıysa dizideki adet degeri 1 artırılıyor
								for($i=0; $i<count($yazilanlar); $i++){
									if($yazilanlar[$i]['konu'] == $satir['video_kategori']){
										$yazilanlar[$i]['adet']++;
										break;
									}
								}
							}
						}
					}else{
						echo "<strong>Bu eğitmen henüz ders vermemiş.</strong>";	//ders yuklememisse yazdırılıyor
					}

					//verilmis dersler listeleniyor
					?>
						<ul class="list-group">
						<?php
						if(isset($yazilanlar) && !empty($yazilanlar)){
							foreach ($yazilanlar as $konu) {
							?>
							  <a href="sonuc.php?kelime=<?php echo $konu['konu']; ?>&egitmen=<?php echo $id; ?>" class="list-group-item">
							    <span class="badge"><?php echo $konu['adet'] . " ders videosu"; ?></span>
							    Konu: <?php echo $konu['konu']; ?>
							  </a>
							  <?php }} ?>
						</ul>

					<?php
					
					

					echo "</div>";

				}
				?>
			</div>
			<div class="col-md-3">
					<?php require('bolumler/sag_kutu.php'); ?>
			</div>
		</div>
		
		<?php require('bolumler/footer.php'); ?>

	</body>
</html>