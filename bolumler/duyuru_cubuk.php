<div class="col-md-12 well" style="padding:0;">
	<div class="bg-info" id="duyuru_baslik">Güncelleme ve Duyurular</div>
	<ul class="duyuru_cubuk">
		<?php
		$dizi = $db->duyuru_getir();	//veritabanından site duyuruları cekiliyor
		foreach ($dizi as $satir) {
			?>
			<li>
				<?php echo $satir['metin']; ?>
			</li>
			<?php 	//duyurular listelendi
		}
		
		?>
	</ul>
</div>