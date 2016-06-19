<?php
require('../../islemler/db.php');	//veritabanı sınıfını barındıran php dosyası ekleniyor
$db = new DB();

$aralik = 5;

if(isset($_GET['sayfa']))		//listelenecek videoların sayfa numarası alınıyor
	$sayfa = $_GET['sayfa'];
else
	$sayfa = 1;
?>
<div class="table-responsive">
<table class="table table-striped table-hover">
	<tr><th>Video Adı</th><th>Yükleyen</th><th>Açıklama</th><th>İzlenme</th><th>Konu</th><th>İşlem?</th></tr>
	<?php
		$dizi = $db->tum_videolari_listele($sayfa,$aralik);		//veritabanından sayfa aralıgındaki videolar cekiliyor
		$sayac = 0;
		foreach ($dizi as $satir) {
			echo "<tr id='vid_satir_" . $satir['ID']. "'>";										//videolar tablolanıyor
				?>
				<td><?php echo $satir['video_adi']; ?></td>
				<td><?php echo $satir['video_yukleyen']['uye_adi']; ?></td>
				<td><?php echo $satir['video_aciklamasi']; ?></td>
				<td><?php echo $satir['video_izlenme']; ?></td>
				<td><?php echo $satir['video_kategori']; ?></td>
				<td>
					
					
					<a href="izle.php?id=<?php echo $satir['ID']; ?>" class="btn btn-sm btn-info">İzle</a>

					<button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#vid_sil-<?php echo $satir['ID']; ?>">
					 	Sil
					</button>

					<!-- Modal -->
					<div class="modal fade" id="vid_sil-<?php echo $satir['ID']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					  <div class="modal-dialog">
					    <div class="modal-content">
					      <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					        <h4 class="modal-title" id="myModalLabel">Silme İşlemini Onayla</h4>
					      </div>
					      <div class="modal-body">
					        	Bu işlemin geri dönüşü yoktur. Videoyu silmek istediğinize emin misiniz?<hr>
				    			<strong>Silinecek Video: <?php echo $satir['video_adi']; ?><br></strong>
					      </div>
					      <div class="modal-footer">
					      	<button class="btn btn-danger" onclick="ajax_video_sil(<?php echo $satir['ID']; ?>);" >Sil</button>
					        <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
					      </div>
					    </div>
					  </div>
					</div>


				</td>
				<?php
				$sayac++;
			echo "</tr>";				//video silme secenegi ekleniyor ve satır bitiriliyor
		}
	?>
</table>

<?php
$max_sayfa_araligi = 5;
$video_sayisi = $db->video_sayi_getir();
$sayfa_sayisi = ceil($video_sayisi / $aralik);			//toplam sayfa sayısı alınıyor ve sayfa numaraları yazdırılıyor
echo "sayfa sayisi:" . $sayfa_sayisi;
?>

<nav>
  <ul class="pagination">

    
    <?php

	  	for($i=0; $i<$sayfa_sayisi; $i++){
	  		if($i == 0){
	  			$ek = "";
	  			$onclick = "";
	  			if($sayfa == 1){
	  				$ek = $ek = "class='disabled'";
	  			}else{
	  				$onclick = "onclick='ajax_video_getir(" . ($sayfa-1) . ")'";
	  			}
	    		?>
	    		<li <?php echo $ek; ?>><a href="#" aria-label="Previous" <?php echo $onclick; ?>><span aria-hidden="true">&laquo;</span></a></li>
	    		<?php
	    	}

	  		if($i == $sayfa-1){
	  			?>
	  			<li class="active"><a href="#"><?php echo ($i+1); ?> <span class="sr-only">(current)</span></a></li>
	  			<?php
	  		}else{
	  			//for($k = 0; $k<200; $k++){
		  			?>
		  			<li><a href="#" onclick="ajax_video_getir(<?php echo $i+1; ?>)"><?php echo ($i+1); ?> </a></li>
		  			<?php
		  		
	  			//}
	  		}

	  		if($i == $sayfa_sayisi-1){
	  			$ek = "";
	  			$onclick = "";
	  			if($sayfa-1 == $i){
	  				$ek = "class='disabled'";
	  			}else{
	  				$onclick = "onclick='ajax_video_getir(" . ($sayfa+1) . ")'";
	  			}
	  			?>
	  			<li <?php echo $ek; ?>><a href="#" aria-label="Next" <?php echo $onclick; ?>><span aria-hidden="true">&raquo;</span></a></li>
	  			<?php
	  		}
	  	}
	?>
    
  </ul>
</nav>

</div>
