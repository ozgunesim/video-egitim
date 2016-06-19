<?php
if(!isset($_SESSION)){	//oturum degiskenleri tanımlanıyor
    session_start();
}
require("bolumler/baslik.php");	//hazır baslik bilgisi eklentisi require edildi 
?>
<html lang="en">
	<head>	
		<?php
			baslik_bilgisi_ekle("Konu Anlatımı"); //baslik bilgisi eklendi
		?>
		<script type="text/javascript">
			document.createElement('video');document.createElement('audio');document.createElement('track');
			//html5 video player yukleniyor
		</script>

		<link href="css/video-js.css" rel="stylesheet">
		<script src="js/video.js"></script>
		<style>
			.vjs-default-skin .vjs-big-play-button
			{
				top: 50%;
				left: 50%;
				margin: -4em auto auto -6em;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<?php menu_yukle(); ?>
		</div>
		<div class="container" id="ana_container" style="margin-top:70px; padding:0px;">
		<?php require('islemler/oturum_kontrol.php'); ?>
			<div class="col-md-9">
				<?php
					if(!isset($_GET['id'])){				//video idsi kontrol ediliyor
						header("Location:index.php");
					}
					$id=$_GET['id'];
					$video = $db->video_getir($id);			//video idsi ile veritabanında arama yapılıyor.
					$db->video_izlenme_artir($video['ID']);	//video izlenme sayısı artırılıyor


				?>
				<ol class="breadcrumb">
				  <li><span class="label label-default">Konular</span></li>
				  <li><span class="label label-default"><?php echo $video['video_kategori']; ?></span></li>
				</ol>

				<video class="video-js vjs-default-skin vjs-big-play-centered"
					
					controls preload="auto" width="100%" height="65%"
					poster="img/thumbs/<?php echo $video['ID']; ?>-lg.jpg"
					data-setup='{"example_option":true}'>
					<source src="video/<?php echo $video['ID'] ?>.mp4" type='video/mp4' />
					<p class="vjs-no-js">Tarayıcınızda html5 video desteği bulunmamakta.</p>
				</video>


				<div class="col-md-8 col-sm-8">
					<div class="row"><h3><?php echo $video['video_adi']; ?></h3></div>
					<div class="row"><h4>Yükleyen: <?php echo $video['video_yukleyen']['uye_adi']; ?></h4></div>
					<div class="row"><h5><?php echo $video['video_aciklamasi']; ?></h5><br></div>

					<!--VİDEO BİLGİLERİ YAZDIRILIYOR-->


				</div>
				<div class="col-md-4 col-sm-8">
					<div class="row"><h4 style="margin-top:24px;text-align:right;"><strong>İzlenme: <?php echo $video['video_izlenme']+1; ?></strong></h4></div>
					<?php
						if($_SESSION['uye_yetki'] == 3){
							//OZEL DERS ALMA BUTONU YAZDIRILIYOR
						?>
							<div class="row"><a href="rezervasyon.php?video_id=<?php echo $video['ID']; ?>" class="btn btn-warning" style="float:right;"><span class="glyphicon glyphicon-education"></span> Özel Ders Almak İstiyorum </a></div>
						<?php
						}
					?>
				</div>
				
			</div>
			<div class="col-md-3">
				<?php require('bolumler/sag_kutu.php'); ?>
			</div>
		</div>
		
		<?php require('bolumler/footer.php'); ?>


	</body>
</html>