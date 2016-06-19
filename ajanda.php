<?php
if(!isset($_SESSION)){	//oturum degiskenleri tanımlanmadıysa tanımlanıyor
    session_start();
}
require("bolumler/baslik.php");	//hazır baslik bilgisi eklentisi require edildi 
?>
<html lang="en">
	<head>	
		<?php
			baslik_bilgisi_ekle("Ajanda"); //baslik bilgisi eklendi
		?>
		<script type="text/javascript">
		$(document).ready(function(){
			

		});

		function hoca_onay(id){		//egitmenin ders onayladıgı ajax fonksiyonu
		    
		    $.ajax({
		        type:'POST',
		        url:'islemler/egitmen/ders_onay.php',
		        data:'ID='+id,
		        success: function(msg){
		            $('#hoca_onay_' + id).text("İptal Et").removeClass().addClass('btn btn-sm btn-danger');
		            $('#hoca_onay_' + id).attr("onclick","hoca_iptal(" + id + ")");
		            $('#hoca_onay_' + id).attr("id","hoca_iptal_" + id);
		            $('#hoca_red_' + id).remove();

		        }
		    });
		}

		function hoca_iptal(id){	//egitmenin dersi iptal ettigi ajax fonksiyonu
		    
		    $.ajax({
		        type:'POST',
		        url:'islemler/egitmen/ders_iptal.php',
		        data:'ID='+id,
		        success: function(msg){
		        	if(msg == "Süre Geçmiş"){
		            	//window.alert("sure gecmis");
		            	$('#sure_hoca_iptal_' + id).modal('show');
		            }else{
		            	$('#hoca_iptal_' + id).text("İptal Edildi").removeClass().addClass('btn btn-sm btn-warning').attr('disabled','disabled');
		            	$('#hoca_iptal_' + id).removeAttr("onclick");
		            }
		            
		            
		        }
		    });
		}

		function ogrenci_iptal(id){		//ogrencinin dersi iptal ettigi fonksiyon
		    
		    $.ajax({
		        type:'POST',
		        url:'islemler/ogrenci/ders_iptal.php',
		        data:'ID='+id,
		        success: function(msg){
		        	//window.alert(msg);
		        	if(msg == "Süre Geçmiş"){
		            	//window.alert("sure gecmis");
		            	$('#ogrenci_sure_hata_' + id).modal('show');
		            }else{
		            	$('#ogrenci_iptal_' + id).text("İptal Edildi").removeClass().addClass('btn btn-sm btn-warning').attr('disabled','disabled');
		            	$('#ogrenci_iptal_' + id).removeAttr("onclick");
		            	if($('#onay_bekliyor_' + id).length>0)
		            		$('#onay_bekliyor_' + id).remove();
		            }
		            
		            
		        }
		    });
		}

		function hoca_red(id){		//hocanin ders istegini reddettigi fonksiyon
		    
		    $.ajax({
		        type:'POST',
		        url:'islemler/egitmen/ders_red.php',
		        data:'ID='+id,
		        success: function(msg){
		            $('#hoca_red_' + id).text("Reddedildi").removeClass().addClass('btn btn-sm btn-warning').attr('disabled','disabled');
		            $('#hoca_red_' + id).removeAttr("onclick");
		            $('#hoca_onay_' + id).remove();
		        }
		    });
		}
		

		</script>

	</head>
	<body>
	<?php
	if(isset($_GET['temizle'])){	//temizle butonuna basıldıysa iptal edilmis,reddedilmis veya suresi gecmiz dersler veritabanından siliniyor
		$db->rezervasyon_temizle($_SESSION['uye_ID']);
	}
	?>
		<div class="container">
			<?php menu_yukle(); ?>
		</div>
		<div class="container" id="ana_container" style="margin-top:70px; padding:0px;">
			<div class="col-md-9 well" style="min-height:88.5vh;">
				<h4>Özel Ders Programınız: <a href="?temizle" style="float:right;margin-top:-16px;" class="btn btn-sm btn-warning"><span class="glyphicon glyphicon-trash"></span> Temizle</a> </h4>
				<div class="table-responsive">
					<table class="table table-striped table-hover">
						<th>Dersi Veren</th><th>Dersi Alan</th><th>Ders Konusu</th><th>Öğrenci Açıklaması</th><?php if($_SESSION['uye_yetki']==2){ ?><th>Öğrenci Açıklaması</th><?php } ?><th>Ders Tarih/Saat</th><th>İşlem?</th>
						<?php
							$id = $_SESSION['uye_ID'];
							$sonuc = $db->rezervasyon_getir($id);									//ders bilgileri tablolanıyor
							$durum = array("","Onaylandı","Reddedildi","Bekliyor","Geçti");
							foreach ($sonuc as $satir) {
								echo "<tr>";

								echo "<td>";
								echo $db->uye_bilgi_getir($satir['hoca_id'])['uye_adi'];
								echo "</td>";

								echo "<td>";
								echo $db->uye_bilgi_getir($satir['ogr_id'])['uye_adi'];
								echo "</td>";

								echo "<td>";
								echo "<p class='text-danger'>" . $db->kategori_adi_getir($satir['konu_id']) . "</p>";
								echo "</td>";

								echo "<td>";
								?>
								<button type="button"  class="btn btn-info btn-sm" data-toggle="modal" data-target="#incele-<?php echo $satir['ID']; ?>">
								 	Öğrenci Açıklaması
								</button>

								<!-- Modal -->
								<div class="modal fade" id="incele-<?php echo $satir['ID']; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
								  <div class="modal-dialog">
								    <div class="modal-content">
								      <div class="modal-header">
								        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								        <h4 class="modal-title">Öğrenci Açıklaması</h4>
								      </div>
								      <div class="modal-body">
								        	<?php
								        		echo $satir['aciklama'];
								        	?>
								      </div>
								      <div class="modal-footer">
								        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
								      </div>
								    </div>
								  </div>
								</div>
								<?php
								
								echo "</td>";
								if($_SESSION['uye_yetki'] == 2){	//eger egitmen girisi varsa ogrencinin skype adresi gosteriliyor
									echo "<td>";

									?>

									<button type="button"  class="btn btn-primary btn-sm" data-toggle="modal" data-target="#skype-<?php echo $satir['ID']; ?>">
									 	Skype Adresi
									</button>

									<!-- Modal -->
									<div class="modal fade" id="skype-<?php echo $satir['ID']; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
									  <div class="modal-dialog">
									    <div class="modal-content">
									      <div class="modal-header">
									        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									        <h4 class="modal-title">Öğrenci Skype Adresi</h4>
									      </div>
									      <div class="modal-body">
									        	<?php
									        		echo $db->uye_bilgi_getir($satir['ogr_id'])['skype'];
									        	?>
									      </div>
									      <div class="modal-footer">
									        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
									      </div>
									    </div>
									  </div>
									</div>

									<?php

									echo "</td>";
								}
								echo "<td>";

								$dersTarihi = new DateTime($satir['tarih']);
								echo $dersTarihi->format("Y-m-d / H:i");
								echo "</td>";


								$ders_durum = $satir['durum'];
								if($_SESSION['uye_yetki'] == 3){
									echo "<td>";
									//dersin iptal edilip edilemeyecegi bildiriliyor
									?>

									<div class="modal fade" id="ogrenci_sure_hata_<?php echo $satir['ID']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
										  <div class="modal-dialog">
										    <div class="modal-content">
										      <div class="modal-header">
										        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										        <h4 class="modal-title">HATA</h4>
										      </div>
										      <div class="modal-body">
										        	<?php
										        		hata_mesaji(" Derse 1 günden az kalmış. Bu dersi iptal edemezsiniz!");
										        	?>
										      </div>
										      <div class="modal-footer">
										        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
										      </div>
										    </div>
										  </div>
										</div>

									<?php
									if($ders_durum == 1){
									?>
										<button id="ogrenci_iptal_<?php echo $satir['ID']; ?>" onclick="ogrenci_iptal(<?php echo $satir['ID']; ?>)" class="btn btn-sm btn-danger">İptal Et</button>

										<!-- Modal -->
										<div class="modal fade" id="ogrenci_sure_hata_<?php echo $satir['ID']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
										  <div class="modal-dialog">
										    <div class="modal-content">
										      <div class="modal-header">
										        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										        <h4 class="modal-title">HATA</h4>
										      </div>
										      <div class="modal-body">
										        	<?php
										        		hata_mesaji(" Derse 1 günden az kalmış. Bu dersi iptal edemezsiniz!");
										        	?>
										      </div>
										      <div class="modal-footer">
										        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
										      </div>
										    </div>
										  </div>
										</div>

									<?php
																		//derslerin o anki durumları yazdırılıyor
									}else if($ders_durum == 2){
									?>
										<button disabled='disabled' class="btn btn-sm btn-danger">Reddedildi</button>
									<?php
									}else if($ders_durum == 3){
									?>
										<button disabled='disabled' id="onay_bekliyor_<?php echo $satir['ID']; ?>" class="btn btn-sm btn-info">Onay Bekliyor</button>
										<button id="ogrenci_iptal_<?php echo $satir['ID']; ?>" onclick="ogrenci_iptal(<?php echo $satir['ID']; ?>)" class="btn btn-sm btn-danger">İptal Et</button>


									<?php
									}else if($ders_durum == 4){
									?>
										<button disabled='disabled' class="btn btn-sm btn-default">Geçti</button>
									<?php
									}else if($ders_durum == 5){
									?>
										<button disabled='disabled' class="btn btn-sm btn-warning">Eğitmen İptal Etti</button>
									<?php
									}else if($ders_durum == 6){
									?>
										<button disabled='disabled' class="btn btn-sm btn-default">Öğrenci İptal Etti</button>
									<?php
									}
									echo "</td>";

									
								}else{
									echo "<td>";

									$tarih1 = new DateTime();
									$tarih1 = $tarih1->add(new DateInterval('P1D'));
									$tarih2 = $dersTarihi;

									$tarih = new DateTime();
									$fark = $tarih2->diff($tarih);

									$sureGecti = false;

									if($tarih1 > $tarih2)	//ders suresinin gecmiste olup olmadıgı kontrol ediliyor
										$sureGecti = true;
									else
										$sureGecti = false;




									if($ders_durum == 1){
										if($tarih1 < $tarih2){
											//dersin iptal edilemeyecegi ve dersin o anki durumu yazdırılıyor
										?>
										<button id="hoca_iptal_<?php echo $satir['ID']; ?>" onclick="hoca_iptal(<?php echo $satir['ID']; ?>);" class="btn btn-sm btn-danger">İptal Et</button>
										<?php
										}else{
										?>
										<button type="button"  class="btn btn-danger btn-sm" data-toggle="modal" data-target="#sure_hata_<?php echo $satir['ID']; ?>">
								 			İptal Et
										</button>

										<!-- Modal -->
										<div class="modal fade" id="sure_hata_<?php echo $satir['ID']; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
										  <div class="modal-dialog">
										    <div class="modal-content">
										      <div class="modal-header">
										        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										        <h4 class="modal-title">HATA</h4>
										      </div>
										      <div class="modal-body">
										        	<?php
										        		hata_mesaji(" Derse 1 günden az kalmış. Bu dersi iptal edemezsiniz!");
										        	?>
										      </div>
										      <div class="modal-footer">
										        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
										      </div>
										    </div>
										  </div>
										</div>

										<?php
										}
									}else if($ders_durum == 2){
									?>
										<button disabled='disabled' class="btn btn-sm btn-warning">İptal Edildi</button>
									<?php
									}else if($ders_durum == 3){
									?>	
										<button id="hoca_red_<?php echo $satir['ID']; ?>" onclick="hoca_red(<?php echo $satir['ID']; ?>);" class="btn btn-sm btn-danger">Reddet</button>
										<button id="hoca_onay_<?php echo $satir['ID']; ?>" onclick="hoca_onay(<?php echo $satir['ID']; ?>);" class="btn btn-sm btn-success">Onayla</button>

										<div class="modal fade" id="sure_hoca_iptal_<?php echo $satir['ID']; ?>" tabindex="-1" role="dialog"  aria-hidden="true">
										  <div class="modal-dialog">
										    <div class="modal-content">
										      <div class="modal-header">
										        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										        <h4 class="modal-title">Öğrenci Açıklaması</h4>
										      </div>
										      <div class="modal-body">
										        	<?php
										        		hata_mesaji('Derse 1 günden az kalmış. İptal edemezsiniz!');
										        	?>
										      </div>
										      <div class="modal-footer">
										        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
										      </div>
										    </div>
										  </div>
										</div>
									<?php
									}else if($ders_durum == 4){
									?>
										<button disabled='disabled' class="btn btn-sm btn-default">Geçti</button>
									<?php
									}else if($ders_durum == 5){
									?>
										<button disabled='disabled' class="btn btn-sm btn-warning">Eğitmen İptal Etti</button>
									<?php
									}else if($ders_durum == 6){
									?>
										<button disabled='disabled' class="btn btn-sm btn-warning">Öğrenci İptal Etti</button>
									<?php
									}
									echo "</td>";
								}
								


								echo "</tr>";

							}


						?>
					</table>
				</div>
			</div>
			<div class="col-md-3">
					<?php require('bolumler/sag_kutu.php'); ?>
			</div>
		</div>
		
		<?php require('bolumler/footer.php'); ?>

	</body>
</html>