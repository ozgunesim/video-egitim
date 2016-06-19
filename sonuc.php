<?php
if(!isset($_SESSION)){	//oturum degiskenleri tanımlanıyor
    session_start();
}
require("bolumler/baslik.php");	//hazır baslik bilgisi eklentisi require edildi 
?>
<html lang="en">
	<head>	
		<?php
			baslik_bilgisi_ekle("Arama"); //baslik bilgisi eklendi
		?>
	</head>
	<body>
		<div class="container">
			<?php menu_yukle(); ?>
		</div>
		<div class="container" id="ana_container" style="margin-top:70px; padding:0px;">
			<div class="col-md-9 well" style="min-height:90vh;">
			<?php
				if(isset($_GET['kelime']) && trim($_GET['kelime']) != ""){		//arama yapılacak kelime aliniyor
					$kelime = $_GET['kelime'];
					if(isset($_GET['ust'])){
						$ust_kategori = true;
					}
					?>
					<h4>Arama sonucları görüntüleniyor:  <strong><?php echo $kelime; ?></strong></h4><hr>


					<?php
					$ek = "";
					if(isset($_GET['egitmen'])){							//eger belirli bir egitmenin paylastıgı derslerde arama yapılacaksa egitmen idsi alınıyor
						$ek = mysql_escape_string($_GET['egitmen']);
					}
					$dizi = $db->video_ara(mysql_escape_string($kelime),$ek);	//veritabanı arama fonksiyonu cagırılıyor


					if(isset($dizi) && count($dizi[0])>0){
						//print_r($dizi);
						$sayac = 0;
						foreach ($dizi[$sayac] as $satir){		//donen degerlerin sayısı 0'dan buyukse listeleniyor
							$video = $satir;
							if(count($video)==0) break;
						?>

							<div class="col-md-6 col-sm-12" id="index_alt_kutu">
								<div class="row" style="margin-left:0;margin-right:0;">
									<div class="col-md-3 thumbnail">
										<?php
								     	if(file_exists("img/thumbs/" . $video['ID'] . "-lg.jpg")){
								    	?>
								      		<img src="img/thumbs/<?php echo $video['ID']; ?>-lg.jpg">
										<?php
										}else{
										?>
											<img src="img/movie.png">
										<?php
										}
										?>
									</div>
									<div class="col-md-9">
										<div class="col-md-12">
											<a href="izle.php?id=<?php echo $video['ID']; ?>"  id="index_baslik" ><h4><?php echo $sayac+1 .". " . $video['video_adi']; ?></h4></a>
									        Gönderen: <?php echo $video['video_yukleyen']['uye_adi']; ?><br>
									        Konu: 
									        <?php

									        	$kategori = $video['video_kategori']; 
									        	$kategori = str_replace(strtolower($kelime),"<strong>" . strtolower($kelime) . "</strong>",strtolower($kategori));
									        	echo $kategori;				//video bilgileri yazdırılıyor
									        ?>

										</div>

									</div>
								</div>
								<div class="row" style="margin-left:0;margin-right:0;">
									<?php echo $video['video_aciklamasi']; ?>
								</div>
							</div>
							

							<?php


							$sayac++;
						}

					}

					?>

					<?php
				}else{
				
						 hata_mesaji("Boş arama yapılamaz!");	//bos arama hatası veriliyor

					
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