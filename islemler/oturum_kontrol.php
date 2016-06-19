<?php

	$yetki = 0;
	if(!isset($_SESSION['oturum']) || $_SESSION['oturum'] == false){	//oturum acılıp acılmadıgı kontrol ediliyor. acık degilse giris formu gosteriliyor
		?>
		<div class="container">
			<div class="col-md-4"></div>
			<div class="col-md-4">
	      <form class="form-giris" action="islemler/giris.php" method="post">
	        <h4 class="form-giris-baslik"><?php hata_mesaji(" Bu sayfayı görebilmek için sisteme giriş yapmanız gerekmektedir.");?></h4>
	        <label for="mail_kutu" class="sr-only">Email adresi</label>
	        <input type="email" id="mail_kutu" name="kul_mail" class="form-control" placeholder="Email adresi" required="" autofocus="">
	        <label for="sifre_kutu"class="sr-only">Şifre</label>
	        <input type="password" id="sifre_kutu" name="kul_sifre" class="form-control" placeholder="Şifre" required="">
	        <br>
	        <button class="btn btn-primary btn-block" type="submit" name="giris_yap">Giriş Yap</button>
	      </form>
	      <a href="kayit.php" class="btn btn-info btn-block">Kayıt Ol</a>
	      </div>
	      <div class="col-md-4"></div>
	    </div>

    	</div>
		<?php
		include('bolumler/footer.php');
		exit();
	}else{
		$yetki = $_SESSION['uye_yetki'];	//oturum acıksa yetki degiskene alınıyor
	}


?>