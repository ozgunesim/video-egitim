

<div class="panel panel-default">
	<div class="panel-heading"> <span class="glyphicon glyphicon-list-alt"></span><b>Haberler</b></div>
	<div class="panel-body">
		<div class="row">
			<div class="col-xs-12">
				<ul class="sag_kutu_haber" style="overflow-y: hidden; height: 211px;"  id="haber_panel">
					<?php

					$dizi = $db->haber_getir();		//site haberleri veritabanından cekiliyor
					foreach ($dizi as $satir) {
						?>
						<li style="" class="news-item">
							<?php echo $satir['metin']; ?>
						</li>
						<?php 		//haberler listelendi
					}
					?>

				</ul>
			</div>
		</div>
	</div>
</div>


<script>
	//haberleri gosteren aracın ayarları yukleniyor
	$(".sag_kutu_haber").bootstrapNews({
        newsPerPage: 3,
        autoplay: true,
		pauseOnHover:true,
        direction: 'down',
        newsTickerInterval: 4000,
        onToDo: function () {
            //console.log(this);
        }
    });

</script>
