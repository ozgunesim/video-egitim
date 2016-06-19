<?php
require('../../islemler/db.php');
require('../mesajlar.php');

$db = new DB();

?>
<ul class="list-group" style="max-height:300px; overflow-y:auto;">
	<?php
		$dizi = $db->slayt_getir();
		if($dizi && is_array($dizi)){
			$sayac = 0;
			$id = 0;
			foreach ($dizi as $satir) {
				$sayac++;
				echo "<li class='list-group-item' style='cursor:pointer;' onclick='secilen_slayt(" . $satir['ID'] . ")'>" .
				"Slayt " . $sayac . "</li>";
				$id = $satir['ID'];
			}
			?>
			<script>
			secilen_slayt(<?php echo $id; ?>);
			</script>
			<?php
		}
	?>

</ul>
