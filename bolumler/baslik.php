<?php
	require('islemler/db.php');
	$db = new DB();

	include('mesajlar.php');

	function baslik_bilgisi_ekle($baslik){	//temel head ogelerinin yuklendigi fonksiyon. parametre olarak sayfa başlık adını alır.
		?>
		<meta charset='utf-8'>
			<title><?php echo $baslik ?></title>
			<script src='js/jquery.min.js'></script>
			<link href='css/light_bootstrap.min.css' rel='stylesheet'>
			<link href='css/main.css' rel='stylesheet'>
			<script src='js/bootstrap.min.js'></script>
			<script src='js/newsbox.js'></script>
			<meta name='viewport' content='width=device-width, initial-scale=1 maximum-scale=1.0, user-scalable=no'>
			<link rel="Shortcut Icon"  href="img/favicon.png"  type="image/x-icon">
			<script src="js/zabuto_calendar.js"></script>
			<link rel="stylesheet" type="text/css" href="css/zabuto_calendar.css">

			<?php
				if(isset($_SESSION['oturum']) && $_SESSION['oturum'] === true){		//oturum acilmissa
					?>
					<script type="application/javascript">
					    $(document).ready(function () {
					    	


					    	var bildirim_loop = setTimeout(ajax_cagir, 10000);	//bildirimleri kontrol edecek fonksiyon 10 saniyelik aralıklarla cagırılacak


							function ajax_cagir() {
								bildirim_getir();
								bildirim_loop = setTimeout(ajax_cagir, 10000);
							}

							
							ajax_cagir();
							<?php

							if($_SESSION['uye_yetki'] != 1)
							{
							?>
					    	var eventData = [
							    <?php
							    	//takvimdeki renklendirmelerin nasıl yapılacagı ile ilgili parametreler db sınıfından ilgili fonksiyondan cekiliyor

									$db = new DB();
									$db->gecmis_ders_guncelle();	//ayrıca derslerin yapilmis olup olmadıgı suanki tarih baz alinarak veritabanında guncelleniyor
								    $dizi = $db->takvim_getir($_SESSION['uye_ID']);
								    if(is_array($dizi)){
								    	foreach ($dizi as $satir) {
										    echo $satir;	//takvim bilgileri cekiliyor
										}
								    }
								    

							    ?>

							];



					        $("#takvim").zabuto_calendar({		//takvimin ayarları ve renklendirmeleri belirleniyor
					        	language: "tr",
					        	today: true,
					        	data: eventData,
					        	legend: [
							        {type: "block", label: "Onay", classname: "takvim_onay"},
							        //{type: "block", label: "Bekleyen", classname: "takvim_bekleyen"},
							        {type: "block", label: "Geçmiş", classname: "takvim_gecmis"},
							        <?php if($_SESSION['uye_yetki']==3){ ?>
							        {type: "block", label: "Red", classname: "takvim_red"}, <?php } ?>
							        {type: "block", label: "İptal", classname: "takvim_iptal"}
      							],
					        });

					        <?php
					    	}
					    	?>
					        
					        


					    });


						function bildirim_temizle(){	//bildirimleri görüldü yapma fonksiyonu
						    $.ajax({
						        type:'POST',
						        url:'islemler/bildirim_temizle.php',
						        data:'ID=' + <?php echo $_SESSION['uye_ID']; ?>,
						        success: function(msg){
						        	//window.alert(msg);
						        }
						    });
						}

						function bildirim_getir(){		//yeni bildirimleri alan fonksiyon
						    $.ajax({
						        type:'POST',
						        url:'islemler/bildirim_getir.php',
						        data:'ID=' + <?php echo $_SESSION['uye_ID']; ?>,
						        success: function(msg){
						        	$('.bildirim_liste').html(msg);
						        }
						    });

						    $.ajax({			//yeni gelen bildirim sayısını alan fonksiyon
						        type:'POST',
						        url:'islemler/bildirim_sayi_getir.php',
						        data:'ID=' + <?php echo $_SESSION['uye_ID']; ?>,
						        success: function(msg){
						        	$('#bildirim_sayi').html(msg);
						        }
						    });
						}


					</script>

					<?php
				}
				
			?>

			<?php
		//baslik bilgileri eklendi
	}

	function menu_yukle(){
		?>
			<nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Yazılım Mühendisliği</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">

        	

          <ul class="nav navbar-nav navbar-right">
            <li><a href="index.php"> Anasayfa </a></li>

            <?php
	            if(!isset($_SESSION)){	//oturum bilgisi yoksa Giriş Yap/Kayıt Ol menusunu ekle
	            	?>

	            	<li class="dropdown" id="menuLogin">
			            <a class="dropdown-toggle" href="#" data-toggle="dropdown" id="navLogin">Giriş Yap / Kayıt Ol</a>
			            <div class="dropdown-menu" style="padding:17px;border-radius: 0;padding-bottom: 2px;width:232px;">
			              <form action="islemler/giris.php" method="post" class="form" id="formLogin"> 
			                <input class="form-control" name="kul_mail" id="username" type="email" placeholder="E-Mail Adresi"> 
			                <input class="form-control" name="kul_sifre" id="password" type="password" placeholder="Şifre"><br>
			                <button type="submit" id="btnLogin" name="giris_yap" class="btn btn-block btn-warning">Giriş</button><center>ya da</center>
			                <a href="kayit.php" style="text-decoration:none;"><button type="button" id="btnLogin" name="giris_yap" class="btn btn-block btn-info">Kayıt Ol</button></a>
			              </form>
			            </div>
			          </li>

	            	<?php

	            }else{

	            	if(isset($_SESSION['oturum'])){
	            		$oturum_acik = $_SESSION['oturum'];	//oturum acik mi bilgisi degiskene atandi
            			$yetki = $_SESSION['uye_yetki'];	//yetki bilgisi degiskene atandi

            			if($oturum_acik == false){	//oturum acik degilse

	            			?>

		            		<li class="dropdown" id="menuLogin">
					            <a class="dropdown-toggle" href="#" data-toggle="dropdown" id="navLogin">Giriş Yap / Kayıt Ol</a>
					            <div class="dropdown-menu" style="padding:17px;border-radius: 0;padding-bottom: 2px;width:232px;">
					              <form action="islemler/giris.php" method="post" class="form" id="formLogin"> 
					                <input class="form-control" name="kul_mail" id="username" type="email" placeholder="E-Mail Adresi"> 
					                <input class="form-control" name="kul_sifre" id="password" type="password" placeholder="Şifre"><br>
					                <button type="submit" id="btnLogin" name="giris_yap" class="btn btn-block btn-warning">Giriş</button><center>ya da</center>
					                <a href="kayit.php" style="text-decoration:none;"><button type="button" id="btnLogin" name="giris_yap" class="btn btn-block btn-info">Kayıt Ol</button></a>
					              </form>
					            </div>
					          </li>

	            			<?php
	            		
						}else{
						//oturum aciksa

							$oturum_renkleri = ['#9954bb','#2780e3','#ff7518'];	//yetkiye gore renkler belirlendi
							if($yetki == 1){	//admin girisi varsa panel linkini ekle
								echo "<li><a href='admin.php'> Admin Paneli </a></li>";
							}else{	// admin girisi yoksa ajanda linkini ekle
								echo "<li><a href='ajanda.php'> Ajanda </a></li>";
							}
							if($yetki==2){
								echo "<li><a href='yukle.php'> Video Yükle </a></li>";	//hica girisi varsa video yukleme linkini ekle
								echo "<li><a href='hoca_panel.php'> Yönetim </a></li>";	//hica girisi varsa yönetim linkini ekle
							}

							
							//diger menu elemanlari ekleniyor
							?>
								<li><a href='hesap.php' style='color: <?php echo $oturum_renkleri[$yetki-1];  ?>'> <?php echo $_SESSION['uye_adi']; ?> </a></li>
								<li class="dropdown">
						          <a href="#" class="dropdown-toggle" data-target="#"  onclick="bildirim_temizle();" id="bildirim_handle" data-toggle="dropdown" role="button" aria-expanded="false">
							         <span id="bildirim_sayi" class="label label-warning" style="color:black;"></span>
							          <span style="top:5px;" class="glyphicon glyphicon-bell"></span> 
							          <span class="caret"></span>
						          </a>
						          <ul class="dropdown-menu list-group bildirim_liste" role="menu">
						          
						          </ul>
						        </li>
								<li><a href='islemler/cikis.php'> Çıkış </a></li>
							<?php
						}

	            	}else{
	            		?>
	            			<li class="dropdown" id="menuLogin">
					            <a class="dropdown-toggle" href="#" data-toggle="dropdown" id="navLogin">Giriş Yap / Kayıt Ol</a>
					            <div class="dropdown-menu" style="padding:17px;border-radius: 0;padding-bottom: 2px;width:232px;">
					              <form action="islemler/giris.php" method="post" class="form" id="formLogin"> 
					                <input class="form-control" name="kul_mail" id="username" type="email" placeholder="E-Mail Adresi"> 
					                <input class="form-control" name="kul_sifre" id="password" type="password" placeholder="Şifre"><br>
					                <button type="submit" id="btnLogin" name="giris_yap" class="btn btn-block btn-warning">Giriş</button><center>ya da</center>
					                <a href="kayit.php" style="text-decoration:none;"><button type="button" id="btnLogin" name="giris_yap" class="btn btn-block btn-info">Kayıt Ol</button></a>
					              </form>
					            </div>
					          </li>

	            		<?php
	            	}
            
	            }

            	?>

          </ul>
          <form class="navbar-form navbar-right" role="search" action="sonuc.php" method="get">
		        <div class="form-group">
		          	<input type="text" class="form-control"  name="kelime" id="arama_kutusu" placeholder="Ders Konusu Ara...">
		        </div>
		        <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span> ARA</button>
	      	</form>
        </div>
      </div>
    </nav>

		<?php

		//menu eklendi

	}



	function slide_yukle(){		//slider in yuklendigi fonksiyon
	?>

		<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
		  <!-- Indicators -->
		  <ol class="carousel-indicators">
		  <?php
		  	$db = new DB();
		  	$slaytlar = $db->slayt_getir();
		  	if(is_array($slaytlar)){
		  		$ilk = true;
		  		$aktif = "";
		  		foreach ($slaytlar as $slayt) {
		  			break;
		  			if($ilk===true)
		  				$aktif = "class='active'";
		  			?>
		  				<li data-target="#carousel-example-generic" data-slide-to="0" <?php echo $aktif; ?>></li>
		  			<?php
		  			$ilk = false;
		  		}
		  	}
		  	?>
		  </ol>

		  <!-- Wrapper for slides -->
		  <div class="carousel-inner" role="listbox">
		  <?php
		  
		  	$slaytlar = $db->slayt_getir();
		  	$ilk = true;
		  	if($slaytlar && is_array($slaytlar)){
		  		foreach ($slaytlar as $slayt) {
		  			?>

					<div class="item <?php if($ilk) echo "active"; ?>">
				      <img src="img/slaytlar/<?php echo $slayt['ID']; ?>.jpg">
				      <div class="carousel-caption golge-arka">
				        <h3 class="golge" ><?php echo $slayt['baslik']; ?></h3>
				        <p class="golge"><?php echo $slayt['aciklama']; ?></p>
				      </div>
				    </div>

			  		<?php
			  		$ilk=false;
		  		}
		  	}
		  ?>
		    
		  </div>
		  <!-- Kontroller -->
		  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
		    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		    <span class="sr-only">Önceki</span>
		  </a>
		  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
		    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		    <span class="sr-only">Sonraki</span>
		  </a>
		</div>

	<?php
	}
?>