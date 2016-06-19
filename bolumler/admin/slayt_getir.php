<?php
require('../../islemler/db.php');
require('../mesajlar.php');

$db = new DB();

if(isset($_POST['id'])){
	$id = $_POST['id'];
	$sonuc = $db->slayt_getir(' ID=' . $id);
	if($sonuc){
		?>

			<div class="form-group">
			<div id="slayt_bildirim"></div>
		    	<img class="form-control img-responsive" id="slayt_onizleme" src="img/slaytlar/<?php echo $sonuc['ID']; ?>.jpg" style="height:auto;"\>
		    	<span class="btn btn-primary btn-file btn-block">
				    Resmi Seç <input id="slayt_resim" type="file">
				</span>
		    	<input class="form-control" type="text" id="slayt_baslik" placeholder="Slayt başlığını girin..." value="<?php echo $sonuc['baslik']; ?>"\>
		    	<textarea class="form-control" id="slayt_metin" rows=3 placeholder="Slayt metnini girin..."><?php echo $sonuc['aciklama']; ?></textarea>
		    	
		    		<button id="slayt_buton" value="slayt_guncelle" onclick="slayt_guncelle(<?php echo $sonuc['ID']; ?>);" class="btn btn-success">Güncelle</button>
		    		<button id="slayt_buton_sil" value="slayt_sil(<?php echo $id; ?>)" onclick="slayt_sil(<?php echo $id; ?>);" class="btn btn-danger">Sil</button>
		    		<br>
	    			
	    		
	    	</div>

		<?php
	}else{
		hata_mesaji(' Bir hata ile karşılaşıldı.');
	}
}

?>