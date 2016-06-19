<?php
if(!isset($_SESSION)){	//oturum degiskenleri tanimlanmadiysa tanimlanıyor
    session_start();
}
require('islemler/oturum_kontrol.php');
require("bolumler/baslik.php");	//hazır baslik bilgisi eklentisi require edildi 
?>
<html lang="en">
	<head>	
		<?php
			baslik_bilgisi_ekle("Admin Paneli"); //baslik bilgisi eklendi
		?>
		<link href="css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
		<script src="js/fileinput.min.js" type="text/javascript"></script>
		<script>
			//$("#slide_input_1").fileinput({'showUpload':true, 'showPreview':true});	//file input aracı yuklendi
			//$("#slide_input_2").fileinput({'showUpload':true, 'showPreview':true});	//file input aracı yuklendi
			//$("#slide_input_3").fileinput({'showUpload':true, 'showPreview':true});	//file input aracı yuklendi
		</script>

		<script src="js/newsTicker.js"></script>
		<script type="text/javascript">


		$(document).ready(function(){

		    //selectbox değişince çalıştır
		    $("#ders").change(function(){
		        konulari_al();
		    });


			$("#slayt_resim").change(function(){
			    readURL(this,this.id);
			});



		    //admin paneli sayfası yuklendiginde ajax fonksiyonları varsayılan degerleriyle cagırılıyor

		    ajax_video_getir(1);
		    ajax_onay_listesi_getir(1);
		    ajax_uye_listele(1);
		    //secilen_slayt(1);
		    slayt_listele();

		});

		var secilenID;
		var secilenYetki;

		function yetki_sec(yetki){
			secilenYetki = yetki;
		}

		function yetkilendir(id){
			$.ajax({
		        type:'POST',
		        url:'bolumler/admin/admin_yetki_sec.php',
		        data:{ID:id,yetki:secilenYetki},
		        success: function(msg){
		        	$('#mdl_uye_yetki_' + id).on('hidden.bs.modal', function () {
					    ajax_uye_listele(1);
					});
		        	$('#mdl_uye_yetki_' + id).modal('hide');
		            
		        }
		    });

		}

		function slayt_ekle(){
			$('#slayt_baslik').val("");
			$('#slayt_metin').text("");
			$('#slayt_onizleme').attr('src','');
			$('#slayt_buton').attr("onclick","slayt_kaydet();").text('Kaydet');
			$('#slayt_buton_sil').remove();

		}

		function secilen_slayt(id){
			secilenID = id;
			$.ajax({
		        type:'POST',
		        url:'bolumler/admin/slayt_getir.php',
		        data:'id='+id,
		        success: function(msg){
		            $('#slayt_form').html(msg);
		            $("#slayt_resim").change(function(){
					    readURL(this,this.id);
					});
		        }
		    });
		}

		function slayt_sil(id){
			$.ajax({
		        type:'POST',
		        url:'bolumler/admin/slayt_sil.php',
		        data:'id='+id,
		        success: function(msg){
		            $('#slayt_bildirim').html(msg);
		            //secilen_slayt(id-1);
		            slayt_ekle();
		            slayt_listele();
		        }
		    });
		}

		function slayt_listele(){
			$.ajax({
		        type:'POST',
		        url:'bolumler/admin/slayt_listele.php',
		        success: function(msg){
		            $('#slayt_liste').html(msg);
		        }
		    });
		}

		function slayt_kaydet(){
				if($('#slayt_resim').prop('files')[0]){
					var dosya = $('#slayt_resim').prop('files')[0];
					var form_veri = new FormData();
					var baslik = $('#slayt_baslik').val();
					var metin = $('#slayt_metin').val();
					if(baslik == ""){
						window.alert('Başlık Boş!');
						return;
					}
					if(metin == ""){
						window.alert('Açıklama Boş!');
						return;
					}
    				form_veri.append('file', dosya);
    				form_veri.append('baslik',baslik);
    				form_veri.append('metin',metin);
    				$.ajax({
    					dataType: 'text',  // what to expect back from the PHP script, if anything
		                cache: false,
		                contentType: false,
		                processData: false,
				        type:'POST',
				        url:'bolumler/admin/slayt_kaydet.php',
				        data:form_veri,
				        success: function(msg){
				            //window.alert(msg);
				            $('#slayt_bildirim').html(msg);
				            secilen_slayt($('#id_al').attr('lastid'));
				            slayt_listele();
				        }
				    });

				}else{
					window.alert('Resim Seçmediniz!');
				}
		}

		function slayt_guncelle(id){
			var form_veri = new FormData();
			if($('#slayt_resim').prop('files')[0]){
				var dosya = $('#slayt_resim').prop('files')[0];
				form_veri.append('file', dosya);
			}
				
				var baslik = $('#slayt_baslik').val();
				var metin = $('#slayt_metin').val();

				if(baslik == ""){
					window.alert('Başlık Boş!');
					return;
				}
				if(metin == ""){
					window.alert('Açıklama Boş!');
					return;
				}
				
				form_veri.append('baslik',baslik);
				form_veri.append('metin',metin);
				form_veri.append('ID',id);

				$.ajax({
					dataType: 'text',  // what to expect back from the PHP script, if anything
	                cache: false,
	                contentType: false,
	                processData: false,
			        type:'POST',
			        url:'bolumler/admin/slayt_guncelle.php',
			        data:form_veri,
			        success: function(msg){
			            //window.alert(msg);
			            $('#slayt_bildirim').html(msg);
			        }
			    });

			
		}

		function readURL(input,id) {

		    if (input.files && input.files[0]) {
		        var reader = new FileReader();

		        reader.onload = function (e) {
		            $('#slayt_onizleme').attr('src', e.target.result);
		        }

		        reader.readAsDataURL(input.files[0]);
		    }
		}

		





		 
		function konulari_al(){			//ders konularinin getirildigi fonksiyon
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
		            $('#konu_araclar').show();
		        }
		    });
		}


		function ajax_video_getir(id){			//video listesi sayfalara ayrılmıs oldugundan her sayfaya tıklandıgında sayfa bilgisi bu fonksiyona gonderiliyor. fonksiyon ilgili sayfayı getiriyor
		    
		    $.ajax({
		        type:'GET',
		        url:'bolumler/admin/admin_video_listele.php',
		        data:'sayfa='+id,
		        success: function(msg){
		            $('#video_tablo').html(msg);
		        }
		    });
		}

		function ajax_onay_listesi_getir(id){		//onay bekleyen egitmen listesi getiriliyor
		    
		    $.ajax({
		        type:'GET',
		        url:'bolumler/admin/admin_hoca_listele.php',
		        data:'sayfa='+id,
		        success: function(msg){
		            $('#onay_listesi').html(msg);
		        }
		    });
		}

		function ajax_hoca_onayla(id){		//egitmen uyeliginin onaylandigi ajax fonksiyonu
		    
		    $.ajax({
		        type:'GET',
		        url:'bolumler/admin/admin_hoca_onayla.php',
		        data:'basvuru_onay='+id,
		        success: function(msg){
		            $('#hucre_hoca_onay_' + id).html("<button class='btn btn-sm btn-success' disabled='disabled'>Onaylandı</button>");
		        }
		    });
		}

		function ajax_uye_sil(id){		//uyelik silinmesi islevini goren ajax fonksiyonu
		    
		    $.ajax({
		        type:'GET',
		        url:'bolumler/admin/admin_uye_sil.php',
		        data:'uye_sil='+id,
		        success: function(msg){
		            
		            $('#mdl_uye_sil_' + id).on('hidden.bs.modal', function () {
					    $('#btn_uye_sil_' + id).text("Silindi").attr('disabled','disabled');
					});
		            $('#mdl_uye_sil_' + id).modal('hide');

		        }
		    });
		}

		function ajax_uye_listele(id){		//siteye kayıtlı kullanıcıların listelendigi ajax fonksiyonu
		    
		    $.ajax({
		        type:'GET',
		        url:'bolumler/admin/admin_uye_listele.php',
		        data:'sayfa='+id,
		        success: function(msg){
		            $('#uye_tab').html(msg);
		        }
		    });
		}

		function ajax_hoca_reddet(id){		//hoca kayıt basvurusunun reddedildigi ajax fonksiyonu
		    
		    $.ajax({
		        type:'GET',
		        url:'bolumler/admin/admin_hoca_reddet.php',
		        data:'basvuru_sil='+id,
		        success: function(msg){
		            $('#hucre_hoca_onay_' + id).html("<button class='btn btn-sm btn-danger' disabled='disabled'>Reddedildi</button>");
		        }
		    });
		}

		function ajax_video_sil(id){		//video silme islevini goren ajax fonksiyonu
		    
		    $.ajax({
		        type:'GET',
		        url:'bolumler/admin/admin_video_sil.php',
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


		<div class="container" id="ana_container" style="padding:0px;margin-top:70px;">
			<div class="col-md-9 well">
				<?php
					if($_SESSION['uye_yetki'] != 1){		//uye yetkisi sorgulanıyor. admin degilse erisim engelleniyor
						hata_mesaji("Bu sayfaya erişim yetkiniz yok!");
						exit();
					}

					//cok fazla tekrar etmesi olası olmayan islemler dogrudan bu sayfaya post ediliyor. ajax kullanimi gerek gorulmedi

	    			if(isset($_POST['konu_sil']) && isset($_POST['video_konu'])){	//ders konusu silme islevi varsa ilgili fonksiyon aracılıgı ile siliniyor
	    				$silinecek = $_POST['video_konu'];
	    				$db->kategori_sil($silinecek);
	    			}

	    			if(isset($_POST['ders_sil']) && isset($_POST['video_kategori'])){	//ders basligi silme islevi varsa ilgili fonksiyon aracılıgı ile siliniyor
	    				$silinecek = $_POST['video_kategori'];
	    				$db->kategori_sil($silinecek);
	    			}

	    			if(isset($_GET['duyuru_sil'])){
	    				$id = $_GET['duyuru_sil'];
	    				$db->duyuru_sil($id);
	    			}

	    			if(isset($_GET['haber_sil'])){
	    				$id = $_GET['haber_sil'];
	    				$db->haber_sil($id);
	    			}
	    			
	    			if(isset($_POST['ders_ekle']) && trim($_POST['yeni_ders_adi']) != ""){	//ders basligi ekleme islevi varsa ilgili fonksiyon aracılıgı ile siliniyor
						$db->yeni_kategori_ekle($_POST['yeni_ders_adi'],0);
	    			}

	    			if(isset($_POST['konu_ekle']) && trim($_POST['yeni_konu_adi']) != ""){	//konu basligi ekleme islevi varsa ilgili fonksiyon aracılıgı ile siliniyor
						$db->yeni_kategori_ekle($_POST['yeni_konu_adi'],$_POST['video_kategori']);
	    			}

					if(isset($_POST['haber_metni']) && trim($_POST['haber_metni']) != ""){	//sag bolmedeki haberler aracina yeni haber ekleme islemi
						$db->haber_ekle(mysql_escape_string($_POST['haber_metni']));
	    			}
	    			if(isset($_POST['duyuru_metni']) && trim($_POST['duyuru_metni']) != ""){	//anasayfanın ust bolmesindeki duyuru cubuguna yeni duyuru ekleme islemi
						$db->duyuru_ekle(mysql_escape_string($_POST['duyuru_metni']));
	    			}



				?>

				<ul id="admin_tabs" class="nav nav-tabs">
				   <li class="active"><a href="#bilgi" data-toggle="tab">Üyelikler</a></li>
				   <li><a href="#egitmen" data-toggle="tab">Eğitmen Kaydı</a></li>
				   <li><a href="#alanlar" data-toggle="tab">Alanları Düzenle</a></li>
				   <li><a href="#dersler" data-toggle="tab">Dersler ve Konular</a></li>
				   <li><a href="#videolar" data-toggle="tab">Videolar</a></li>
				</ul>
				<div id="tab_kutu" class="tab-content">
				   <div class="tab-pane fade in active" id="bilgi">
				    	<div class="row" style="padding:15px;">
				    		<div class="well" id="uye_tab">


				    		</div>
				    	</div>
				   </div>
				   <div class="tab-pane fade" id="egitmen">
					    <div class="row" style="padding:15px;">
					    <div class="well" id="onay_listesi">
			    		
			    		</div>
				    	</div>
				   </div>
				   <div class="tab-pane fade" id="alanlar">
					    <div class="row" style="padding:15px;">
						    <div class="well">
							    <form class="form"  action="" method="post" >
							    	<h4>Duyuru Ekle</h4>
							    	<input class="form-control" type="text" required="" name="duyuru_metni" placeholder="Duyuru Girin..." \><br>
							    	<input type="submit" class="btn btn-default" value="Ekle" \>
							    </form><hr>
							    <div class="well">
							    <?php
							    	$duyurular = $db->duyuru_getir(999999);
							    	foreach ($duyurular as $satir) {
							    		echo "<div style='padding-right:60px;'>" . $satir['metin'] . "</div>";
							    		echo "<br><div style='text-align:right;margin-top:-45px;'><a href='?duyuru_sil=" . $satir['ID'] . "' class='btn btn-sm btn-danger'>Sil</a></div><hr>";
							    	}
							    ?>
							    </div>
							    <form  action="" method="post" >
							    	<h4>Haber Ekle</h4>
							    	<textarea class="form-control" rows="3" name="haber_metni"></textarea><br>
							    	<input type="submit" class="btn btn-default" value="Ekle" \>
							    </form><hr>
								<div class="well">
									<?php
										$haberler = $db->haber_getir(999999);
								    	foreach ($haberler as $satir) {
								    		echo "<div style='padding-right:60px;'>" . $satir['metin'] . "</div>";
								    		echo "<br><div style='text-align:right;margin-top:-45px;'><a href='?haber_sil=" . $satir['ID'] . "' class='btn btn-sm btn-danger'>Sil</a></div><hr>";
								    	}
									?>
								</div>
								<h4>Slaytları Düzenle</h4>
							    <div class="row">
								    <div class="col-md-4 col-sm-12">
									    <div id="slayt_liste">
									    	
									    </div>
								    	<button type="button" onclick="slayt_ekle();" class="btn btn-sm btn-block btn-default">Slayt Ekle</button>
								    </div>
								    <div class="col-md-8 col-sm-12">
									    <div id="slayt_form">
									    	
									    </div>
								    </div>

								</div>

					    	</div>
				    	</div>
				   </div>
				   <div class="tab-pane fade" id="dersler">
					    <div class="row" style="padding:15px;">
					    	<div class="well">
						    	<form class="form" action="" method="post" id="ders_form">
									<div class="form-group">
										<strong>Dersler</strong>
										<select class="form-control" name="video_kategori" id="ders">
										<option disabled selected>Ders Seçin</option>
											<?php
												$dizi = $db->kategori_getir();	//ders basliklari select kontrolune aktariliyor
												foreach ($dizi as $satir) {
													echo "<option value='"  . $satir['ID']  . "'>" . $satir['adi'] . "</option>";
												}
											?>
										</select>
										
										

										  <br>
										  <button type="button" class="btn btn-success" data-toggle="modal" data-target=".ders_modal">Ders Ekle</button>

											<div class="modal fade ders_modal" tabindex="-1" role="dialog" aria-hidden="true">
											  <div class="modal-dialog modal-sm">
											    <div class="modal-content" style="padding:15px;">
											    	<input type="text" name="yeni_ders_adi" class="form-control" placeholder="Ders Adını Girin..." \>
											      	<input type="submit" name="ders_ekle" value="Ders Ekle" type="button" class="btn btn-block btn-success">
											    </div>
											  </div>
											</div>

										  <input type="submit" name="ders_sil" value="Ders Sil" type="button" class="btn btn-danger">
										  <hr>

										<div id="konu">

										</div>
										<div id="konu_araclar" style="display:none" style="margin-top:10px;"><br>
											
											  
												<button type="button" class="btn btn-success" data-toggle="modal" data-target=".konu_modal">Konu Ekle</button>

												<div class="modal fade konu_modal" tabindex="-1" role="dialog" aria-hidden="true">
												  <div class="modal-dialog modal-sm">
												    <div class="modal-content" style="padding:15px;">
												    	<input type="text" name="yeni_konu_adi" class="form-control" placeholder="Konu Adını Girin..."\>
												      	<input type="submit" name="konu_ekle" value="Konu Ekle" class="btn btn-block btn-success">
												    </div>
												  </div>
												</div>

											  	<input type="submit" name="konu_sil" value="Konu Sil" type="button" class="btn btn-danger">
											
										</div>
									</div>
								</form>
							</div>
				    	</div>
				   </div>
				   <div class="tab-pane fade" id="videolar">
					    <div class="row" style="padding:15px;">
					    <div class="well">
					    	<div class="table-responsive" id="video_tablo">
			    				



			    			</div>

			    			</div>
				    	</div>
				   </div>
				</div>

				<script>
				   $(function () {	//son yapilan isleme gore tab kontrolundeki sayfa degistiriliyor
				   	<?php
				   		$sayfa = 0;
				   		if(isset($_GET['basvuru_sil']) || isset($_GET['basvuru_onay'])){
				   			$sayfa = 1;
				   		}
				   		if(isset($_GET['duyuru_sil']) || isset($_GET['haber_sil']) || isset($_POST['haber_metni']) || isset($_POST['duyuru_metni'])){
				   			$sayfa = 2;
				   		}
				   	?>
				      $('#admin_tabs li:eq(<?php echo $sayfa; ?>) a').tab('show');
				   });
				</script>



			</div>
			<div class="col-md-3">
					<?php require('bolumler/sag_kutu.php'); ?>
			</div>
		</div>
		
		<?php require('bolumler/footer.php'); ?>

	</body>
</html>