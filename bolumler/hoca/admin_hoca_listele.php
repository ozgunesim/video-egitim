<?php

require('../../islemler/db.php');
$db = new DB();

$aralik = 5;

if(isset($_GET['sayfa']))
	$sayfa = $_GET['sayfa'];
else
	$sayfa = 1;

$bekleyen_egitmen = $db->bekleyen_hoca_listesi_getir($sayfa,$aralik);

?>
<h4><strong>Eğitmen Kayıt Onayı</strong></h4>


<div class="table-responsive">
	<table class="table table-striped table-hover">
	<tr><th>Kullanıcı Adı</th><th>E-Mail</th><th>Eğitmen Bilgisi</th><th>İşlem?</th></tr>
	<?php
	if($bekleyen_egitmen){
		foreach ($bekleyen_egitmen as $satir) {
			echo "<tr>";
				
				echo "<td>" . $satir['kul_nick'] . "</td>";
				echo "<td>" . $satir['kul_mail'] . "</td>";
				?>
				<td>
					<button type="button"  class="btn btn-primary btn-sm" data-toggle="modal" data-target="#incele-<?php echo $satir['ID']; ?>">
					 	İncele
					</button>

					<!-- Modal -->
					<div class="modal fade" id="incele-<?php echo $satir['ID']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					  <div class="modal-dialog">
					    <div class="modal-content">
					      <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					        <h4 class="modal-title">Aday Üyenin Bilgileri</h4>
					      </div>
					      <div class="modal-body">
					        	<?php
					        		echo $satir['bilgi'];
					        	?>
					      </div>
					      <div class="modal-footer">
					        <button type="button" class="btn btn-default" data-dismiss="modal">Kapat</button>
					      </div>
					    </div>
					  </div>
					</div>

					
				</td>




				<?php

				echo "<td id='hucre_hoca_onay_" . $satir['ID'] . "'>" . 
				"<button class='btn btn-sm btn-danger' onclick='ajax_hoca_reddet(" . $satir['ID'] . ");'>Reddet</button>".
				"<button class='btn btn-sm btn-success' onclick='ajax_hoca_onayla(" . $satir['ID'] . ");'>Onayla</button>". 
				"</td>";
			echo "</tr>";
		}
	}
	?>
	</table>
</div>