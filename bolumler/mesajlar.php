<?php
	function hata_mesaji($mesaj){
		?>
		<div class="alert alert-danger " role="alert">
			</button>
			<strong>HATA!</strong> <?php echo $mesaj; ?>
		</div>
	<?php
	}

	function bilgi_mesaji($mesaj){
		?>
		<div class="alert alert-info " role="alert">
			</button>
			<strong>Bilgi:</strong> <?php echo $mesaj; ?>
		</div>
	<?php
	}

	function basari_mesaji($mesaj){
		?>
		<div class="alert alert-success " role="alert">
			</button>
			<strong>Tamam!</strong> <?php echo $mesaj; ?>
		</div>
	<?php
	}


	function uyari_mesaji($mesaj){
		?>
		<div class="alert alert-warning " role="alert">
			</button>
			<strong>Uyarı! </strong> <?php echo $mesaj; ?>
		</div>
	<?php
	}
?>