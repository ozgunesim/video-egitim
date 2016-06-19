<?php
	$dizi = $db->son_10_video_getir();
	if($dizi != null){
		$sayac = 0;
		foreach ($dizi as $video){
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
							<a href="izle.php?id=<?php echo $video['ID']; ?>"  id="index_baslik" ><h4><?php echo $video['video_adi']; ?></h4></a>
					        Gönderen: <?php echo $video['video_yukleyen']['uye_adi']; ?>
						</div>

					</div>
				</div>
				<div class="row" style="margin-left:0;margin-right:0;">
					<p><?php echo $video['video_aciklamasi']; ?></p>
				</div>
			</div>
			

			<?php


			$sayac++;
		}

	}
	


?>