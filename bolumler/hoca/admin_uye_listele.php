<?php

require('../../islemler/db.php');
$db = new DB();

$aralik = 5;

if(isset($_GET['sayfa']))
	$sayfa = $_GET['sayfa'];
else
	$sayfa = 1;

?>

<h4><strong>Üyeler</strong> (Toplam Üye Sayısı:<?php $dizi_uye = $db->uye_listesi_getir(); echo count($dizi_uye); ?>)</h4>

<div class="table-responsive">
	<table class="table table-striped table-hover">
	<tr><th>Kullanıcı Adı</th><th>E-Mail</th><th>Yetki</th><th>Sil?</th></tr>
	<?php
	
	foreach ($dizi_uye as $satir) {
		$yetkiler[0] = "yok";
		$yetkiler[1] = "<span class='text-danger'>Admin</span>";
		$yetkiler[2] = "<span class='text-info'>Eğitmen</span>";
		$yetkiler[3] = "<span class='text-primary'>Öğrenci</span>";
		echo "<tr>";
			
			echo "<td>" . $satir['kul_nick'] . "</td>";
			echo "<td>" . $satir['kul_mail'] . "</td>";
			echo "<td>" . $yetkiler[$satir['kul_yetki']] . "</td>";
			$disabled = "";
			if($satir['kul_yetki'] == 1)
				$disabled = "disabled='disabled'";
			echo "<td>";
			?>
			<button type="button" <?php echo $disabled; ?> id="btn_uye_sil_<?php echo $satir['ID']; ?>" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#mdl_uye_sil_<?php echo $satir['ID']; ?>">
			 	Sil
			</button>
			<!-- Modal -->
			<div class="modal fade" id="mdl_uye_sil_<?php echo $satir['ID']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
			  <div class="modal-dialog">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title">Silme İşlemini Onayla</h4>
			      </div>
			      <div class="modal-body">
			        	Bu işlemin geri dönüşü yoktur. Emin misiniz?<br>
			        	<strong>Silinecek Üye:<?php echo $satir['kul_nick']; ?></strong>
			      </div>
			      <div class="modal-footer">
			      	<button id='mdl_btn_uye_sil_" . $satir['ID'] . "' onclick="ajax_uye_sil(<?php echo $satir['ID']; ?>)" $disabled class='btn btn-danger'> Üyeliği Kaldır</button>
			        <button type="button" class="btn btn-default" data-dismiss="modal">Vazgeç</button>
			      </div>
			    </div>
			  </div>
			</div>

			<?php
			echo "</td>";
		echo "</tr>";
	}

	?>
	</table>
</div>