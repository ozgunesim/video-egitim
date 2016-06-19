<?php
if(!isset($_SESSION)){		//oturum degiskenleri tanımlanıyor
    session_start();
}
require("bolumler/baslik.php");	//hazır baslik bilgisi eklentisi require edildi 
?>
<html lang="en">
	<head>	
		<?php
			baslik_bilgisi_ekle("Anasayfa"); //baslik bilgisi eklendi
		?>

		<script src="js/newsTicker.js"></script>
		<script type="text/javascript">

		$(document).ready(function(){			//duyuru cubugunun ayarları yapılıyor
			$('.duyuru_cubuk').newsTicker({
		   	row_height: 50,
		   	speed: 800
		});

		    //selectbox değişince çalıştır
		    $("#ders").change(function(){		//ders konularının alındıgı fonksiyon select kontrolunun onchange olayına baglanıyor
		        konulari_al();
		    });
		});
		 
		function konulari_al(){					//ders konuları alınıyor
		    //dersin alınması
		    ders_id=$("#ders").val();
		    //seçilen dersin gönderilmesi
		    $.ajax({
		        type:'POST',
		        url:'bolumler/alt_kategori_getir.php',
		        data:'kat_id='+ders_id,
		        success: function(msg){
		            //dönen konuları gösterme
		            $('#konu').html(msg);
		        }
		    });
		}
		</script>



	</head>
	<body>
		<div class="container">
			<?php menu_yukle(); ?>
		</div>


		<div class="container" style="padding-left:0;margin-top:70px;">
			<?php include('bolumler/duyuru_cubuk.php'); ?>		
		</div>


		<div class="container" id="ana_container" style="padding:0px;">
			<div class="col-md-9 well">
				<?php slide_yukle(); ?>
				<hr>


				<h4>Listelemek istediğiniz derslerin konusunu seçin:</h4>
				<div class="row">
					<div class="col-md-12">
						<?php include('bolumler/ders_getir.php'); ?>
						<input type="submit" id="btn_ders_getir" value="Listele" class="btn btn-default" onclick="$( '#ders_form' ).submit();" style="float:right;" \>
					</div>
				</div>
				<hr>




				<h5><strong>Son Yüklenen Dersler</strong></h5><br>
				<div class="row">
						<?php include('bolumler/son_yuklenen.php'); ?>
				</div>
				<hr>
				<h5><strong>En Çok İzlenen Dersler</strong></h5>
				<div class="row">
					<?php include('bolumler/en_cok_izlenen.php'); ?>
				</div>

			</div>
			<div class="col-md-3">
					<?php require('bolumler/sag_kutu.php'); ?>
			</div>
		</div>
		
		<?php require('bolumler/footer.php'); ?>

	</body>
</html>