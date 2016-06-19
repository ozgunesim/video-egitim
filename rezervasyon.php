<?php
if(!isset($_SESSION)){		//oturum degiskenleri tanımlı degilse tanımlanıyor
    session_start();
}
require("bolumler/baslik.php");	//hazır baslik bilgisi eklentisi require edildi 
?>
<html lang="en">
	<head>	
		<?php
			baslik_bilgisi_ekle("Ders Al"); //baslik bilgisi eklendi
		?>


		<script type="text/javascript" src="js/DateTimePicker.js"></script>
		<link rel="stylesheet" type="text/css" href="css/DateTimePicker.css" />

		<?php

		$video_id = $_GET['video_id'];				//DERS VE HOCA BILGILERI ALINIYOR
		$video_dizi = $db->video_getir($video_id);

		$uye_id = $_SESSION['uye_ID'];
		$uye_adi = $_SESSION['uye_adi'];
		$uye_mail = $_SESSION['uye_mail'];

		$hoca_id = $video_dizi['video_yukleyen']['ID'];
		$hoca_adi = $video_dizi['video_yukleyen']['uye_adi'];
		$hoca_mail = $video_dizi['video_yukleyen']['uye_mail'];
		?>


		<script type="text/javascript">


		function renk_ver(id,elemId){											//tablonun renklendirilmesi yapılıyor
			var colors = ['#FF7518','#ff0039','#9954bb','#3fb618','#2780e3'];
			$('#'+elemId).css('background',colors[id]).css('color','white');
		}

		function rezerve_et(tarih){

			var btn_id = tarih.replace(' ','_');
			$('#' + btn_id).text('onaylandı');
			$.ajax({
		        type:'POST',
		        url:'islemler/ders_al.php',
		        data: {hoca_id:<?php echo $hoca_id; ?>,tarih:tarih,konu_id:<?php echo $db->kategori_id_getir($video_dizi['video_kategori']); ?>,aciklama:$('#aciklama').val()},
		        success: function(msg){
		        	$('#tarih_modal').modal('toggle');
		        	$('#bilgi_modal').modal('toggle');
		            $('#bilgi_modal_body').html(msg);
		        }
		    });
		}


		$(document).ready(function(){
		    //selectbox değişince çalıştır
		    $("#ders").change(function(){	//ders konularınin alindigi ajax fonksiyonu
		        konulari_al();
		    });

		    
		    if($('#takvim_hoca').length){
	        	$("#takvim_hoca").zabuto_calendar({		//takvimin ayarları ve beslemeleri yapılıyor
		        	language: "tr",
		        	today: true,
		        	//ajax:{
		        		//url: "bolumler/egitmen_takvim_getir.php?id=<?php echo $hoca_id; ?>",
		        		//modal: true
		        	//},
					action: function tiklandi (){	//tarih seciciden tarih bilgisi alınıyor ve saat listesi listeleniyor
						//window.alert(this.id);
						var tarih = $('#' + this.id).data("date");
						$('#tarih_modal').modal('toggle');
						$('#tarih_modal_body').text(tarih + "|" + <?php echo $hoca_id; ?>);
						$.ajax({
					        type:'POST',
					        url:'bolumler/saat_getir.php',
					        data: {hoca_id:<?php echo $hoca_id; ?>,tarih:tarih,konu_id:<?php echo $db->kategori_id_getir($video_dizi['video_kategori']); ?>},
					        success: function(msg){
					            $('#tarih_modal_body').html(msg);
					            $('#tarih_modal_baslik').html('Seçtiğiniz Tarih: <strong>' + tarih + '</strong>');
					        }
					    });
						
		        	}
		        });
			}


		});
		 
		function konulari_al(){		//ders konularının alındıgı ajax fonksiyonu
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


		<div class="modal fade" id="tarih_modal">
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="tarih_modal_baslik">Tarih Seçici</h4>
		      </div>
		      <div class="modal-body" id="tarih_modal_body">
		        <p>YÜKLENİYOR...</p>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
		      </div>
		    </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</div><!-- /.modal -->



		<div class="modal fade" id="bilgi_modal">
		  <div class="modal-dialog modal-lg">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title" id="bilgi_modal_baslik">Özel Ders Al</h4>
		      </div>
		      <div class="modal-body" id="bilgi_modal_body">
		        <p>YÜKLENİYOR...</p>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
		      </div>
		    </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</div><!-- /.modal -->




		<div class="container" id="ana_container" style="margin-top:70px; padding:0px;">
			<div class="col-md-12">
				<div class="col-md-12">
					<div class="row">
						<div class="panel panel-default">
							<div class="panel-heading"><h4><strong><span class="glyphicon glyphicon-education"></span> Özel Ders Al</strong></h3></div>
							<div class="panel-body">

								<?php
									
									
									if($video_dizi['video_yukleyen']['uye_yetki'] == 1)			//dersi yukleyen admin ise erişim engelleniyor
										exit(hata_mesaji('Bu kişi yöneticidir. Ders alamazsınız!'));
									
								?>


									<div class="well" style="text-align:center;">
									<?php 											//ders ve egitmen bilgileri yazdırılıyor
										echo "<div class='col-md-6 col sm-12'><h4>";
										echo "Konu: <strong>" . $video_dizi['video_kategori'] . "</strong>";
										echo "</h4></div>";

										echo "<div class='col-md-6 col-sm-12' style='border-left:1px solid gray;'><h4>";
										echo "Dersi Veren Eğitmen: <strong>" . $hoca_adi . "</strong>";
										echo "</h4></div><hr>";
									?>
									</div>

									<div class="well">
										<h4><strong>Anlamadığınız yerler hakkında biraz bilgi verin:</strong></h4>
										<textarea name="aciklama" id="aciklama" class="form-control" rows=8></textarea>
										


									</div>

									<div class="well">
										<h4><strong>Aşağıdaki takvimden ders tarihini seçin:</strong></h4>
										<div id="takvim_hoca"></div>
									</div>
									
									<?php 			//bilgi mesaji veriliyor
										bilgi_mesaji("En erken 1 gün sonrasına tarih belirleyebilirsiniz.<br>
											TARİH SEÇMEDEN ÖNCE EĞİTMENİN 
											
											<button type='button' class='btn btn-default btn-sm' data-toggle='modal' data-target='#program'>
											  <strong>DERS PLANINI</strong>
											</button>
											
											 KONTROL EDİN.");
									?>


									<!-- Modal -->
									<div class="modal fade" id="program" tabindex="-1" role="dialog" aria-hidden="true">
									  <div class="modal-dialog modal-lg">
									    <div class="modal-content">
									      <div class="modal-header">
									        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									        <h4 class="modal-title">Eğitmenin Ders Planı</h4>
									      </div>
									      <div class="modal-body">
									      	

									      		

												<div class="table-responsive">
													<table class="table table-striped table-bordered table-hover table-condensed">
														<tr style="text-align:center;background:gray;color:white;">
															<th>Pazartesi</th><th>Salı</th><th>Çarşamba</th><th>Perşembe</th><th>Cuma</th><th>Cumartesi</th><th>Pazar</th>
														</tr>
														<?php
															$takvim = $db->hoca_takvim_getir($hoca_id);		//egitmenin ders takvimi veritabanından alınıp yazdırılıyor
															//print_r($takvim);
															$hoca_dersler = $db->hoca_konu_getir($hoca_id);

															for($i = 0; $i< 12 ; $i++){
																echo "<tr style='text-align:center;'>";

																for($j = 0; $j<7 ; $j++){
																	echo "<td id='td_" . $i . $j . "'>";
																	$uygun = false;
																		?>
																		
																		  
																		    <?php echo "<strong>" . ($i+12) . ":00</strong>" ; ?>
																			  
																			  
																			  <?php
																			  //print_r($hoca_dersler);
																			  	foreach ($hoca_dersler as $satir) {
																			  		$selected = "";
																			  		$renkler = array('#9954bb','#ff0039','#FF7518','#3fb618','#2780e3');

																			  		
																			  		for($k=0; $k< count($takvim); $k++){
																			  			if(isset($takvim[$k]) && $takvim[$k]['saat'] == $i && $takvim[$k]['gun'] == $j && $satir['ID'] == $takvim[$k]['ders_id']){
																			  				echo "<br>";
																			  				echo "<small>Ders:</small> <STRONG>" . $satir['adi'] . "</STRONG>";
																			  				$uygun = true;
																			  				?>
																			  				<script>
																						  		renk_ver(<?php echo ($satir['ID']%5); ?> , <?php echo "'td_" . $i . $j . "'" ; ?>);
																						  	</script>
																			  				<?php

																			  				//uygun ders saati yazdırıldı renklendirildi ve hangi dersin verildigi yazdırıldı

																			  			}
																			  		}
																			  		
																			  	}
																			  ?>
																			  
																			
																		<?php
																		if(!$uygun){
																			echo "<h5>Ders Yok</h5>";	//ders saati uygun degilse yazdırılıyor
																		}
																	echo "</td>"; 
																}

																echo "</tr>";
															}

														?>
													</table>
												




									      </div>
									      <div class="modal-footer">
									        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
									      </div>
									    </div>
									  </div>
									</div>

									</div>
								


							</div>

						</div>
					</div>
				</div>
			</div>
			<!--
			<div class="col-md-3">
					<?php //require('bolumler/sag_kutu.php'); //sag kutu yuklendi ?>
			</div>
			-->
		</div>
		
		<?php require('bolumler/footer.php'); //footer yuklendi ?>

	</body>
</html>