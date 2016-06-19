<?php
/*
	Bu sayfa egitmenler icin video yukleme sayfasıdır.

*/
if(!isset($_SESSION)){	//oturum degiskenleri yuklenmediyse yükle
    session_start();
}
require("bolumler/baslik.php");	//hazır baslik bilgisi eklentisi require edildi 
?>
<html lang="en">
	<head>	
		<?php
			baslik_bilgisi_ekle("İçerik Oluştur"); //baslik bilgisi eklendi
		?>
		<link href="css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
		<script src="js/fileinput.min.js" type="text/javascript"></script>
		<script>
			$("#input-id").fileinput({'showUpload':true, 'showPreview':false});	//file input aracı yuklendi
		</script>

		<script type="text/javascript">
		$(document).ready(function(){
		    //selectbox değişince çalıştır
		    $("#ders").change(function(){
		        konulari_al();
		    });
		});
		 
		function konulari_al(){				//ilgili basligin ders konularini getiren ajax fonksiyonu
		    //dersin alınması
		    ders_id=$("#ders").val();
		    //seçilen dersin gönderilmesi
		    $.ajax({
		        type:'POST',
		        url:'bolumler/alt_kategori_getir.php',
		        data:'kat_id='+ders_id,
		        success: function(msg){
		            //dönen konuları göster
		            $('#konu').html(msg);
		        }
		    });
		}
		</script>
		
	</head>
	<body>

		

		<div class="container">
			<?php menu_yukle(); //menu yuklendi ?>
		</div>
		<div class="container" id="ana_container" style="margin-top:70px; padding:0px;">
			<div class="col-md-9">
				<div class="col-md-12">
					<div class="row">
					<?php

					if($_SESSION['uye_yetki'] != 1 && $_SESSION['uye_yetki'] != 2){
						hata_mesaji("Bu sayfaya giriş yetkiniz yok!");
						exit();
						// eger gelen kullanıcı admin veya egitmen degilse erişim engelle
					}

					?>
						<div class="panel panel-default">
						  <div class="panel-heading"><h4><strong><span class="glyphicon glyphicon-upload"></span> Ders İçeriği Yükle</strong></h3></div>
						  <div class="panel-body">

						  	<?php
						  		$err = false;	//hata degiskenleri olusturuldu
						  		$errMesg = "";

								if(isset($_FILES['video_dosyasi'])){	//eger video yukleme istegi varsa

									

									if($_POST['video_adi'] == ""){					//bos alanlar kontrol ediliyor
										$err = true;
										$errMesg = "<br>Video Başlığı Boş!<br>";
									}
									if($_POST['video_adi'] == ""){
										$err = true;
										$errMesg = "<br>Video Başlığı Boş!<br>";
									}
									if($_POST['video_aciklamasi'] == ""){
										$err = true;
										$errMesg .= "Video Açıklaması Boş!<br>";
									}

									if(!isset($_POST['video_konu']) || $_POST['video_konu'] == ""){
										$err = true;
										$errMesg .= "Video Konusu Boş!<br>";
									}	
									if($_FILES['video_dosyasi']['name'] == ""){
										$err = true;
										$errMesg .= "Video Dosyası Seçilmemiş!";
									}
									if($_FILES['video_dosyasi']['size'] > (1024 * 1024) * 100 ){	//dosya boyutu kontrol ediliyor (max 100 mb)
										$err = true;
										$errMesg .= "Video Dosyası 100mb'dan Büyük!";
									}
									//$ara_dizi = explode(".", $_FILES['video_dosyasi']['name']);
									//$uzanti = $ara_dizi[count($ara_dizi)-1];
									if($_FILES['video_dosyasi']['type'] != "video/mp4"){	//dosya turu kontrol ediliyor (yalniz mp4)
										$err = true;
										$errMesg .= "Geçerli Bir .mp4 Dosyası Değil!";
									}

									if(!$err){	//hata yoksa yuklemeye basla

										$video_adi = $_POST['video_adi'];				//post bilgileri aliniyor
										$video_aciklamasi = $_POST['video_aciklamasi'];
										$video_kategori = $_POST['video_konu'];

										//veritabanina video bilgileri kaydediliyor. kaydedilen videonun id'si degiskene atiliyor.
										//donen video id videolar klasorune yuklenen mp4 dosyasinin adi olacak!.
										$video_id = $db->video_kaydet($video_adi,$video_aciklamasi,$video_kategori);

										if($video_id){	//video_id degiskenine sonuc donduyse

											//video önizlemesi olusturulacak

											$hedef = "video/" . $video_id .".mp4";
											if (move_uploaded_file($_FILES["video_dosyasi"]["tmp_name"], $hedef)) {
												$mesaj = "Ders başarıyla yüklendi.";

												//thumbnail olusturulacak
												//thumbnail 320x240 boyutunda;
												include('islemler/thumbs.php');
												$thumbnail = new thumbs();
												if(!$thumbnail->thumbnailKaydet($video_id,$hedef)){
													$mesaj .= "<br><strong>ANCAK THUMBNAIL OLUŞTURULAMADI.</strong>";

												}
												basari_mesaji($mesaj);
										        
										    } else {
										        $mesaj = "Üzgünüz. Bir hata ile karşılaşıldı.";
										        hata_mesaji($mesaj);
										    }
										}else{
											hata_mesaji("Bu isimde bir video zaten paylaşılmış!");
										}

									}else{		//hata varsa hata mesaji goster
										hata_mesaji($errMesg);
									}
								}


							?>

						    <form action="" method="post" enctype="multipart/form-data">
						    	<div class="form-group">
						    		<strong>Video Başlığı</strong>
						    		<input type="text" class="form-control" name="video_adi" placeholder="Başlığı Girin."><br>
						    		<strong>Video Açıklaması</strong>
						    		<textarea class="form-control" rows="3" maxlength="400" name="video_aciklamasi" placeholder="Açıklamayı Girin.(Max:400 karakter)" ></textarea><br>
						    		

									<?php include('bolumler/ders_getir.php'); ?>


									<br>
									<strong>Video Dosyasını Seçin</strong> <span class="label label-danger" style="margin-left:10px;">Yalnızca .mp4 / Max. 100mb</span>
									<script src="js/fileinput_locale_tr.js"></script>
									<input type="file" class="file" name="video_dosyasi" data-show-preview="false" multiple="false">
						    	</div>
						    </form>
						    
						  </div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
					<?php require('bolumler/sag_kutu.php'); //sag kutu yuklendi ?>
			</div>
		</div>
		
		<?php require('bolumler/footer.php'); //footer yuklendi ?>

	</body>
</html>