<?php session_start(); ?>
<html lang="en">
	<head>	
		<?php
			require("eklentiler/eklenti_yukle.php");	//hazır baslik bilgisi eklentisi require edildi
			baslik_bilgisi_ekle("Anasayfa"); //baslik bilgisi eklendi
		?>
	</head>
	<body>
		<div class="container-fluid">
			<?php menu_yukle(); ?>
			
		</div>
	</body>
</html>