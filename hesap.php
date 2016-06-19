<?php
if(!isset($_SESSION)){	//oturum degiskenleri tanımlanmadıysa tanımlanıyor
    session_start();
}
require("bolumler/baslik.php");	//hazır baslik bilgisi eklentisi require edildi 
?>
<html lang="en">
	<head>	
		<?php
			baslik_bilgisi_ekle("Hesabı Güncelle"); //baslik bilgisi eklendi
			require('islemler/oturum_kontrol.php');	//giris kontrol edildi
		?>
		
		<script>

		$(document).ready(function(){	//egitmen kaydı mı yoksa ogrenci kaydı mı yapıldıgı sorgulanıyor
			tur_kontrol();
		});

		function tur_kontrol(){
			if($('#select_tur').val() == 2) {	//eger egitmen kaydı yapılıyorsa akademik bilgi girisi alaninin acilmasi saglanıyor
				$('#bilgi_text').show();
				$('#skype_input').hide();
			}else{
				$('#bilgi_text').hide();
				$('#skype_input').show();
			}

		}
		</script>

	</head>
	<body>
		<div class="container">
			<?php menu_yukle(); ?>
		</div>
		<div class="container" id="ana_container" style="margin-top:70px; padding:0px;">
		
			<div class="col-md-12">
				<?php
				if(isset($_POST['eski_sifre'])){							//sifre degistirme islemi var mı kontrol ediliyor
					$eski_sifre = trim($_POST['eski_sifre']);
					$yeni_sifre = trim($_POST['yeni_sifre']);
					$yeni_sifre_t = trim($_POST['yeni_sifre_tekrar']);		//verilen post degiskeninden alınıyor
					$mesaj = "";
					if($eski_sifre == "")
						$mesaj = "Eski Şifre Alanı Boş!<br>";				//bos veriler kontrol ediliyor
					if($yeni_sifre == "")
						$mesaj .= "Yeni Şifre Alanı Boş!<br>";
					if($yeni_sifre_t == "")
						$mesaj .= "Yeni Şifre Tekrar Alanı Boş!";

					$hataVar = false;
					if($mesaj != ""){
						hata_mesaji($mesaj);								//hata varsa yazdırılıyor
						$hataVar = true;
					}

					$dogrulama = false;
					$dogrulama = $db->sifre_karsilastir($eski_sifre);

					if(!$hataVar){
						if($dogrulama === true){				//eski sifrenin dogrulugu kontrol ediliyor
							if($yeni_sifre == $yeni_sifre_t){
								if(strlen($yeni_sifre) > 5){	//sifre en az 6 karakter mi kontrol ediliyor
									if($db->sifre_guncelle($_SESSION['uye_ID'],$yeni_sifre)){	//hata yoksa sifre guncelleniyor
										basari_mesaji('Şifreniz Güncellendi!');
									}else{
										hata_mesaji('Beklenmeyen hata!');						//hatalar yazdırılıyor
									}
								}else{
									hata_mesaji('Yeni Şifre En Az 6 Karakterden Oluşmalı!');
								}
							}else{
								hata_mesaji('Yeni Şifre Alanları Uyuşmuyor!');
							}
						}else{
							hata_mesaji('Eski Şifre Yanlış!');
						}
					}
					
				}

				if(isset($_POST['email'])){							//email degistirme islemi var mı kontrol ediliyor
					$email = trim($_POST['email']);
					if($email != ""){				//ilgili veriler bos mu kontrol ediliyor
						if($db->email_guncelle($_SESSION['uye_ID'],$email)){		//hata yoksa e mail degistiriliyor
							basari_mesaji('E Mail Güncellendi.');
						}else{														//hatalar yazdırılıyor
							hata_mesaji('Beklenmeyen Hata!');
						}
					}else{
						hata_mesaji('E Mail Boş!');
					}
				}

				if(isset($_POST['skype'])){								//skype adresi degistirme islemi var mı kontrol ediliyor
					$skype = trim($_POST['skype']);
					if($skype != ""){				//ilgili veriler bos mu kontrol ediliyor
						if($db->skype_guncelle($_SESSION['uye_ID'],$skype)){		//hata yoksa skype adresi degistiriliyor
							basari_mesaji('Skype Adresi Güncellendi.');
						}else{														//hatalar yazdırılıyor
							hata_mesaji('Beklenmeyen Hata!');
						}
					}else{
						hata_mesaji('Skype Adresi Boş!');
					}
				}

				?>
				<div id="signupbox" style="margin-top:50px" class="mainbox col-md-8 col-md-offset-2 col-sm-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="panel-title"><span class="glyphicon glyphicon-user"></span> Hesap Bilgilerini Düzenle</div>
                            
                        </div>  
                        <div class="panel-body" >
	                        <div class="well">
		                        <h4><span class="glyphicon glyphicon-asterisk"></span> Şifre Değiştir</h4>
		                        <form class="form-group" action="" method="post">
		                       		<label>Eski Şifre</label>
		                       		<input class="form-control" type="password" maxlength="10" value="<?php if(isset($_POST['eski_sifre']))echo $_POST['eski_sifre']; ?>" name="eski_sifre">
		                       		<label>Yeni Şifre</label>
		                       		<input class="form-control" type="password" maxlength="10" value="<?php if(isset($_POST['yeni_sifre']))echo $_POST['yeni_sifre']; ?>" name="yeni_sifre">
		                       		<label>Yeni Şifre Tekrar</label>
		                       		<input class="form-control" type="password" maxlength="10" value="<?php if(isset($_POST['yeni_sifre_tekrar']))echo $_POST['yeni_sifre_tekrar']; ?>" name="yeni_sifre_tekrar"><br>
		                       		<input type="submit" value="Tamam" class="btn btn-default">
		                        </form>
	                        </div>
	                        <div class="well">
		                        <h4><span class="glyphicon glyphicon-envelope"></span> E Mail Değiştir <small>(<?php echo $_SESSION['uye_mail']; ?>)</small></h4>
		                        <form class="form-group" action="" method="post">
		                       		<label>E-Mail</label>
		                       		<input class="form-control" type="email" name="email"><br>
		                       		<input type="submit" value="Tamam" class="btn btn-default">
		                        </form>
	                        </div>
	                        <?php
	                        if($_SESSION['uye_yetki'] == 3){
	                        ?>
	                        <div class="well">
		                        <h4><span class="glyphicon glyphicon-phone-alt"></span> Skype Değiştir <small>(<?php $sonuc = $db->uye_bilgi_getir($_SESSION['uye_ID']); echo $sonuc['skype']; ?>)</small></h4>
		                        <form class="form-group" action="" method="post">
		                       		<label>Skype Adresi</label>
		                       		<input class="form-control" type="text" name="skype"><br>
		                       		<input type="submit" value="Tamam" class="btn btn-default">
		                        </form>
	                        </div>
	                        <?php
	                    	}
	                    	?>
                        </div>
                    </div>

               
               
                
         	</div> 
			</div>
		</div>
	
		<?php require('bolumler/footer.php'); ?>


	</body>
</html>