<?php
require('../islemler/db.php');		//veritabanı islemlerinin yapıldıgı sınıf ekleniyor
$db = new DB();
//print_r($db->hoca_takvim_getir($_POST['hoca_id']));

$tarih = $_POST['tarih'];								//secilen tarih verisi isleniyor ve zaman araligi kontrolu yapiliyor
$tarih_secilen = new DateTime($tarih);
$tarih_ileri = new DateTime();
$tarih_ileri = $tarih_ileri->add(new DateInterval('P30D'));
$bugun = new DateTime();
//$bugun = $bugun->add(new DateInterval('P1D'));

include('mesajlar.php');

if($tarih_secilen <= $bugun){
	hata_mesaji('EN ERKEN 1 GÜN SONRASINI SEÇEBİLİRSİNİZ!');
	exit();
}

if($tarih_secilen > $tarih_ileri){
	hata_mesaji('EN GEÇ 30 GÜN SONRASINI SEÇEBİLİRSİNİZ!');
	exit();
}


$gun = date('w', strtotime($tarih));		//baslangic gunu pazartesine cekiliyor
if($gun == 0){
	$gun = 6;
}else{
	$gun--;
}

echo "<br><h4>Şimdi aşağıda listelenmiş saatlerden uygun olanı seçin:</h4>";

$hoca_dersler = $db->hoca_konu_getir($_POST['hoca_id']);		//egitmen ders yuklemis mi kontrol ediliyor
if(!is_array($hoca_dersler))
	exit(hata_mesaji('Bu eğitmen henüz ders vermiyor'));

?>
<br>
<table class="table table-condensed">
<thead>
	<th><span class="glyphicon glyphicon-time"></span> Saat</th>
	<th><span class="glyphicon glyphicon-book"></span> Dersin Konusu</th>
	<th><span class="glyphicon glyphicon-check"></span> Durum</th>
	<th style="text-align:right;"><span class="glyphicon glyphicon-asterisk"></span> İşlem</th>
</thead>
<tbody>
<?php
//echo "<pre>"; print_r($db->hoca_takvim_getir($_POST['hoca_id'])); echo "</pre>";

$dizi = $db->hoca_takvim_getir($_POST['hoca_id']);
$sayac = 0;

if(!is_array($dizi))
	exit(hata_mesaji('Bu eğitmen henüz ders vermiyor'));		//egitmenin takviminde verdigi ders var mı kontrol ediliyor

foreach ($dizi as $satir) {			//secilen gundeki dersler aliniyor
	if($satir['gun'] == $gun){
		$gunDizi[$sayac] = $satir;
		$sayac++;
	}
}

if(!isset($gunDizi)){
	exit(uyari_mesaji("Eğitmen bugün ders vermiyor."));
}

for($i =0; $i<12; $i++){
	$gunTablo[$i] = array('saat' => '' , 'gun' => '', 'ders_id' => '');		//secilen gune gore saat - konu tablosu olusturuluyor
	foreach ($gunDizi as $satir) {
		if($satir['saat'] == ($i)){
			$gunTablo[$i] = $satir; 
		}
	}
}

//echo "<pre>"; print_r($gunTablo); echo "</pre>";
//print_r($gunDizi);

for($i=0; $i<12; $i++){

	$dolu = false;

	if($gunTablo[$i]['saat'] != ""){

		if($db->hoca_ders_uygun($tarih . " " . ($i+12) . ":00" ,$_POST['hoca_id'])){	//egitmenin o saatte dolu olup olmadıgı kontrol ediliyor
			//echo "<tr>";
		}else{
			echo "<tr class='danger'>";
			$dolu = true;
		}

		$konu_uygun = false;
		if($_POST['konu_id'] == $gunTablo[$i]['ders_id']){		//ogretmenin o saatte dersini verdigi konunun istenilen konuya esit olup olm adıgı kontrol ediliyor
			$konu_uygun = true;
		}else{
			$konu_uygun = false;
		}

		if($konu_uygun && !$dolu)								//tablolama yapılıyor
			echo "<tr>";
		else
			echo "<tr class='danger'>";
		
	}else{
		echo "<tr class='danger'>";
	}

			echo "<td>";
				if($gunTablo[$i]['saat'] != ""){
					echo "<span style='font-weight:bold;'>" . ($i+12) . ":00" . "</span>";
				}else{
					echo "<span style='text-decoration:line-through;'>" . ($i+12) . ":00" . "</span>";
				}
			echo "</td>";

			echo "<td>";
				if($gunTablo[$i]['saat'] != ""){
					foreach ($hoca_dersler as $ders) {
						if($ders['ID'] == $gunTablo[$i]['ders_id']){
							echo $ders['adi'];
							break;
						}
					}
				}else{
					echo "Eğitmen bu saatte müsait değil.";
				}
			echo "</td>";

			echo "<td>";
				if($gunTablo[$i]['saat'] != "" && $konu_uygun && !$dolu){
					echo "<p>Uygun</p>";
				}else{
					echo "<p>Alınamaz</p>";
				}
			echo "</td>";

			echo "<td style='text-align:right;'>";
				if($gunTablo[$i]['saat'] != ""){		//butonlar yerlestiriliyor. eger dersi almakta bir sorun yoksa buton onclick olayında dersi rezerve eden bir ajax fonksiyonuna gidiyor.
					if($dolu===true){
						echo "<button style='width:135px;' disabled='disabled' class='btn btn-default btn-sm' type='button'>Alınmış</button>";
					}else{
						if($konu_uygun){
							echo "<button id='btn_" . $tarih . "_" . ($i+12) . ":00" . "' style='width:135px;' onclick=\"rezerve_et('" . $tarih . " " . ($i+12) . ":00" .  "');\" class='btn btn-success btn-sm' type='button'>Dersi Al</button>";
						}else{
							echo "<button style='width:135px;' disabled='disabled' class='btn btn-info btn-sm' type='button'>Konu Uygun Değil</button>";
						}
					}
				}else{
					echo "<button style='width:135px;' disabled='disabled' class='btn btn-default btn-sm' type='button'>Eğitmen Uygun Değil</button>";
				}
				
			echo "</td>";

		echo "</tr>";	
}


?>
</tbody>
</table>