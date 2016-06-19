<?php
/*

	bu sayfa egitmenin yonetim panelidir

*/
if(!isset($_SESSION)){	//oturum degiskenleri tanımlanmadıysa tanımlanıyor
    session_start();
}
require("bolumler/baslik.php");	//hazır baslik bilgisi eklentisi require edildi 
?>
<html lang="en">
	<head>	
		<?php
			baslik_bilgisi_ekle("Anasayfa"); //baslik bilgisi eklendi
		?>
		<script src="js/ckeditor/ckeditor.js"></script>
		<script>
			function ajax_video_getir(id){	//egitmenin yukledigi videolar sayfalanarak listeleniyor. sayfa gecislerinin yapildigi fonksiyon
		    
			    $.ajax({
			        type:'GET',
			        url:'bolumler/hoca/hoca_video_listele.php',
			        data:'sayfa='+id,
			        success: function(msg){
			            $('#video_tablo').html(msg);
			        }
			    });
			}


			function select_kontrol(id){	//egitmenin takvimindeki ders saatlerini degistirmesi sonucunda ilgili alanın renklendirilmesi islevini goren fonksiyon
				var colors = ['#9954bb','#ff0039','#FF7518','#3fb618','#2780e3'];
				console.log((colors[id%5]));
				$('#td_' + id).css('background',colors[$('#select_' + id).val()%5]).css('color','#fff');
				$('#select_' + id).css('color','#000');
				if($('#select_' + id).val() == "bos"){
					$('#td_' + id).css('background','transparent').css('color','#000');

				}
			}

			function ajax_video_sil(id){	//egitmenin yukledigi videoları silmesi islevini goren ajax fonksiyonu
			    
			    $.ajax({
			        type:'GET',
			        url:'bolumler/hoca/hoca_video_sil.php',
			        data:'id='+id,
			        success: function(msg){
			            //$('#video_tablo').html(msg);
			            $('#vid_sil-' + id).on('hidden.bs.modal', function () {
						    $('#vid_satir_' + id).remove();
						});
						$('#vid_sil-' + id).modal('hide');
			            
			        }
			    });
			}



		</script>

	</head>
	<body>
		<div class="container">
			<?php menu_yukle(); ?>
		</div>
		<div class="container" id="ana_container" style="margin-top:70px; padding:0px;">
			<div class="col-md-12 well" style="min-height:88.5vh;">
				

				<?php
			    	if(isset($_POST['kaydet'])){	//takvimdeki degisiklikler aliniyor
			    		//echo "<pre>"; print_r($_POST); echo "</pre>";
			    		$sayac = 0;
			    		for($i = 0; $i< 12 ; $i++){
							for($j = 0; $j<7 ; $j++){
								//echo "ders_" . $i.$j;
								if(isset($_POST['ders_' . $i.$j]) && $_POST['ders_' . $i.$j] != "bos"){
									$secilenler[$sayac] = $i.$j . ":" . $_POST['ders_' . $i.$j];
									$sayac++;
								}
								
							}
						}
						
						if(isset($secilenler)){		//takvimde degisiklik varsa kaydediliyor
							if($db->hoca_takvim_kaydet($secilenler,$_SESSION['uye_ID']))
								basari_mesaji("Takviminiz kaydedildi.");
							else
								hata_mesaji("Takviminiz kaydedilemedi");
						}

			    	}

			    	if(isset($_POST['bilgi_kaydet'])){	//akademik bilgi guncelleme islemi yapılıyor
			    		if(trim($_POST['akademik_bilgi']) != ""){
			    			$bilgi = mysql_escape_string($_POST['akademik_bilgi']);
			    			if($db->hoca_bilgi_guncelle($_SESSION['uye_ID'],$bilgi)){
			    				basari_mesaji('Bilgileriniz başarıyla güncellendi.');
			    			}
			    		}
			    	}
					

		    	?>


				<div role="tabpanel">

				  <!-- Nav tabs -->
				  <ul class="nav nav-tabs" role="tablist">
				     <li role="presentation" class="active"><a href="#video_tab" aria-controls="video_tab" role="tab" data-toggle="tab">Yüklediğiniz Videolar</a></li>
				     <li role="presentation"><a href="#ders_tab" aria-controls="ders_tab" role="tab" data-toggle="tab">Ders Verebileceğiniz Saatler</a></li>
				     <li role="presentation"><a href="#akademik" aria-controls="ders_tab" role="tab" data-toggle="tab">Akademik Bilgileriniz</a></li>
				  </ul>

				  <!-- Tab panes -->
				  <div class="tab-content">
				    <div role="tabpanel" class="tab-pane active" id="video_tab">


				    	<?php
						// require('islemler/db.php');
						// $db = new DB();

						$aralik = 5;

						if(isset($_GET['sayfa']))
							$sayfa = $_GET['sayfa'];
						else
							$sayfa = 1;
						?>
						<div class="table-responsive" id="video_tablo">
						<table class="table table-striped table-hover">
							<tr><th>Video Adı</th><th>Yükleyen</th><th>Açıklama</th><th>İzlenme</th><th>Konu</th><th>İşlem?</th></tr>
							<?php
								$dizi = $db->hoca_videolari_listele($sayfa,$aralik,$_SESSION['uye_ID']);
								$sayac = 0;
								if(is_array($dizi)){
									foreach ($dizi as $satir) {
										echo "<tr id='vid_satir_" . $satir['ID']. "'>";		//videolar listeleniyor
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
										echo "</tr>";
									}
								}
								
							?>
						</table>

						<?php
						$max_sayfa_araligi = 5;
						$video_sayisi = $db->hoca_video_sayi_getir($_SESSION['uye_ID']);
						$sayfa_sayisi = ceil($video_sayisi / $aralik);
						//echo "sayfa sayisi:" . $sayfa_sayisi;
						?>

						<nav>
						  <ul class="pagination">

						    
						    <?php

							  	for($i=0; $i<$sayfa_sayisi; $i++){				//sayfa numaraları yazdırılıyor
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





				    </div>
				    <div role="tabpanel" class="tab-pane" id="ders_tab">

				    	<div class="table-responsive">
				    	<form action="" method="post"> 
							<table class="table table-striped table-bordered table-hover">
								<tr style="text-align:center;background:gray;color:white;">
									<th>Pazartesi</th><th>Salı</th><th>Çarşamba</th><th>Perşembe</th><th>Cuma</th><th>Cumartesi</th><th>Pazar</th>
								</tr>
								<?php
									$takvim = $db->hoca_takvim_getir($_SESSION['uye_ID']);
									//print_r($takvim);
									$hoca_dersler = $db->hoca_konu_getir($_SESSION['uye_ID']);

									for($i = 0; $i< 12 ; $i++){
										echo "<tr style='text-align:center;font-weight:bold !important;'>";

										for($j = 0; $j<7 ; $j++){
											echo "<td id=td_" . $i . $j . ">";
												?>
												
												  
												    <!--input type="checkbox" id="tik_<?php echo $i.$j; ?>" onclick="tik_kontrol('<?php echo $i.$j; ?>')" value="<?php echo "gun:" . $j . "|saat:" . $i . ";"; ?>"-->
												    <?php echo ($i+12) . ":00" ; ?>
													  
													  <select name="ders_<?php echo $i.$j; ?>" id="select_<?php echo $i.$j; ?>" onchange="select_kontrol('<?php echo $i.$j; ?>');" style="text-overflow:elipsis;font-size:12px;">
													  <option value="bos" selected>BOŞ</option>
													  <?php
													  //print_r($hoca_dersler);
													  	foreach ($hoca_dersler as $satir) {
													  		$selected = "";
													  		$renkler = array('#9954bb','#ff0039','#FF7518','#3fb618','#2780e3');


													  		for($k=0; $k< count($takvim); $k++){
													  			if(isset($takvim[$k]) && $takvim[$k]['saat'] == $i && $takvim[$k]['gun'] == $j && $satir['ID'] == $takvim[$k]['ders_id']){
													  				$selected = "selected";
													  			}
													  		}
													  		
													  		echo "<option value='" .$satir['ID'] . "'  $selected>" . $satir['adi'] . "</option>";

													  	}
													  ?>
													  </select>
													<script>
												  			select_kontrol('<?php echo $i.$j; ?>');
												  	</script>
												<?php
											echo "</td>"; 
										}

										echo "</tr>";
									}

								?>
							</table>
							<input type="submit" name="kaydet" value="Kaydet" class="btn btn-primary" \>
							</form>
						</div>


				    </div>

			    	<div role="tabpanel" class="tab-pane" id="akademik">
			    		<form action="" method="post">
			    		<br><h4 class="small">Akademik Bilgilerinizi Girin:</h4>
			    			<div class="form-group">
			    				<textarea class="form-control" rows=17 id="akademik_bilgi" name="akademik_bilgi">
			    				<?php
			    					$hakkinda = $db->hoca_bilgi_getir($_SESSION['uye_ID']);
									//echo str_replace("\n", "\n", $hakkinda['bilgi']);
									echo trim($hakkinda['bilgi']);
			    				?>
			    				</textarea>
			    				<script>
			    					CKEDITOR.replace( 'akademik_bilgi' );
			    				</script>
			    				<br>
			    				<input type="submit" name="bilgi_kaydet" class="btn btn-primary" value="Akademik Bilgi Güncelle" \>
			    			</div>
			    		</form>
			    	</div>


				  </div>


				</div>



			</div>
			<!--div class="col-md-3"-->
					<?php //require('bolumler/sag_kutu.php'); ?>
			<!--/div-->
		</div>
		
		<?php require('bolumler/footer.php'); ?>

	</body>
</html>