<?php
if(!isset($_SESSION)){	//oturum degiskenleri tanımlanmadıysa tanımlanıyor
    session_start();
}
require("bolumler/baslik.php");	//hazır baslik bilgisi eklentisi require edildi 
?>
<html lang="en">
	<head>	
		<?php
			baslik_bilgisi_ekle("Kayıt Ol"); //baslik bilgisi eklendi
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

			if(isset($_POST['email'])){		//kayıt islemi yakalanırsa kayıt icin gerekli olan bilgiler alınıyor
				$email = $_POST['email'];
				$kullanici = $_POST['kullanici_adi'];
				$sifre = $_POST['sifre'];
				$sifre_tekrar = $_POST['sifre_tekrar'];
				if(isset($_POST['tur']) && $_POST['tur'] == 2){		//egitmen kaydıysa akademik bilgiler alınıyor
					$bilgi = $_POST['bilgi'];
				}
				$skype = $_POST['skype'];

				if(
					trim($email) != "" && 
					trim($sifre) != "" && 
					trim($sifre_tekrar) != "" && 
					trim($kullanici) != "" 			//alanlar bos degilse
				){
					$email = trim($email);
					$sifre = trim($sifre);
					$sifre_tekrar = trim($sifre_tekrar);
					$kullanici = trim($kullanici);
					$skype = trim($skype);

					if($sifre == $sifre_tekrar){
						if(isset($bilgi) && trim($bilgi) != ""){
							if($bilgi != ""){																//veritabanına egitmen kaydı yollanıyor
								if($db->hoca_kaydet($email,$sifre,$kullanici,$bilgi,$skype) === true){
									basari_mesaji("Kaydınız işleme alınmıştır. Yöneticiler tarafından onaylandığında giriş yapabilirsiniz.");
									exit();
								}else{
									hata_mesaji('Kayıt yapılamadı. Bu hesap zaten alınmış olabilir.');
								}
							}
							else{
								hata_mesaji('Akademik bilgilerinizi boş bıraktınız.');
							}
						}else{
							if(!isset($bilgi)){	
								if($skype != ""){
									if($db->ogrenci_kaydet($email,$sifre,$kullanici,$skype) === true){
										basari_mesaji("Kaydınız tamamlandı. Şimdi giriş yapabilirsiniz.");
										exit();
									}else{
										hata_mesaji('Kayıt yapılamadı. Bu hesap zaten alınmış olabilir.');
									}
								}else{
									hata_mesaji('Skype adresiniz boş!');
								}
							}else{
								hata_mesaji('Akademik bilgi boş');
							}
							
						}
					}else{
						hata_mesaji('Şifreler uyuşmuyor.');
					}
				}else{
					hata_mesaji('Eksik alan(lar) var');
				}
			}

			?>
				<div id="signupbox" style="margin-top:50px" class="mainbox col-md-8 col-md-offset-2 col-sm-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="panel-title">Kayıt Ol</div>
                            
                        </div>  
                        <div class="panel-body" >
	                        <div class="alert alert-warning">* işaretli alanlar zorunludur.</div>
                            <form id="signupform" class="form-horizontal" role="form" action="" method="post">
                                
                                <div id="signupalert" style="display:none" class="alert alert-danger">
                                    <p>Error:</p>
                                    <span></span>
                                </div>
                                    
                                <div class="form-group">
                                    <label for="" class="col-md-3 control-label">Kayit Türü</label>
                                    <div class="col-md-9">
                                        <select name="tur" class="form-control" onchange="tur_kontrol();" id="select_tur">
                                        	<option value="1">Öğrenci</option>
                                        	<option value="2" <?php if(isset($bilgi)) echo "selected "; ?>>Eğitmen</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-md-3 control-label">Email<span class="text-danger">*</span></label>
                                    <div class="col-md-9">
                                        <input type="email" class="form-control" name="email" placeholder="Email Adresiniz" <?php if(isset($email)) echo "value='" . $email . "'"; ?>>
                                    </div>
                                </div>
                                    
                                <div class="form-group">
                                    <label for="" class="col-md-3 control-label">Kullanıcı Adı<span class="text-danger">*</span></label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="kullanici_adi" placeholder="Görünür Adınız" <?php if(isset($kullanici)) echo "value='" . $kullanici . "'"; ?>>
                                    </div>
                                </div>
                                <div id="skype_input">
                                <div class="form-group">
                                    <label for="" class="col-md-3 control-label"><img class="img-responsive" style="display:inline;width:25px;" src="img/skype.png" \> Skype Hesabınız<span class="text-danger">*</span></label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="skype" placeholder="Skype adınız veya Skype'a kayıtlı Windows Live Hesabınız" <?php if(isset($skype)) echo "value='" . $skype . "'"; ?>>
                                    </div>
                                </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-md-3 control-label">Şifre<span class="text-danger">*</span></label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" name="sifre" placeholder="Şifre" <?php if(isset($sifre)) echo "value='" . $sifre . "'"; ?>>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-md-3 control-label">Şifre Tekrar<span class="text-danger">*</span></label>
                                    <div class="col-md-9">
                                        <input type="password" class="form-control" name="sifre_tekrar" placeholder="Şifre Tekrar" <?php if(isset($sifre_tekrar)) echo "value='" . $sifre_tekrar . "'"; ?>>
                                    </div>
                                </div>

                                <div class="form-group"  id="bilgi_text" style="display:none;">
                                    <label for="bilgi" class="col-md-3 control-label">Akademik Bilgilerinizi Girin<span class="text-danger">*</span></label>
                                    <div class="col-md-9">
                                        <textarea placeholder="Akademik Bilgileriniz" rows="10" class="form-control" name="bilgi"><?php if(isset($bilgi)) echo $bilgi; ?></textarea>
                                    </div>
                                </div>
                                    

                                <div class="form-group">
                                    <!-- Button -->                                        
                                    <div class="col-md-offset-3 col-md-9">
                                        <input type="submit" id="btn-signup" class="btn btn-info" value="Kayıt Ol" \>
                                        
                                    </div>
                                </div>
                                
                                
                                
                                
                            </form>
                         </div>
                    </div>

               
               
                
         	</div> 
			</div>
		</div>
		
		<?php require('bolumler/footer.php'); ?>


	</body>
</html>