<?php

include('../bolumler/mesajlar.php');
require('db.php');
$db = new DB();
if(!isset($_SESSION))
	session_start();

$dizi['hoca_id'] = $_POST['hoca_id'];		//ders rezerve etmek icin gerekli olan bilgiler alınıyor
$dizi['ogr_id'] = $_SESSION['uye_ID'];
$dizi['konu_id'] = $_POST['konu_id'];
$dizi['aciklama'] = $_POST['aciklama'];
$dizi['tarih'] = $_POST['tarih'];

if($dizi['aciklama'] == "")
	exit(hata_mesaji('Açıklama Boş'));


$tarih2 = new DateTime($dizi['tarih']);

$ileriTarih = new DateTime();
//$ileriTarih->add(new DateInterval('P1D'));

$uygunTarih = false;

if($ileriTarih > $tarih2)	//gecerli bir tarih alınıp alınmadıgı sorgulanıyor
	$uygunTarih = false;
else
	$uygunTarih = true;

$dizi['durum'] = 3;
$fark = new DateTime();

$fark = $tarih2->diff($fark);

if($uygunTarih === true){						//uygun bir tarih alınmışsa ders rezervasyonu bilgileri veritabanına yollanıyor
	$sonuc = $db->rezervasyon_olustur($dizi);
	if($sonuc === true){
		basari_mesaji("Özel ders isteğiniz gönderildi. Kalan süre: " . $fark->format("<strong> %a gun , %h saat </strong>"));
		bilgi_mesaji(" OZEL DERS SURESİ 1 SAATTİR. DERSLER SKYPE PROGRAMI ÜZERİNDEN YAPILACAKTIR. BAŞVURUNUZ ONAYLANDIĞI TAKDİRDE EĞİTMEN DERS SAATİ GELMEDEN SKYPE ADRESİNİZDEN SİZE ULAŞACAKTIR.");
		bilgi_mesaji("Özel ders detaylarını incelemek için <a href='ajanda.php' style='color:Red;font-weight:bold;'>ajandanıza</a> bakın.");
	}else{
		hata_mesaji("Özel ders alınamadı! " . $sonuc);	//ozel ders alınamadıysa nedenleri ile birlikte kullaniciya bildiriliyor
		
	}
}else{
	hata_mesaji("Daha ileri bir tarih seçin! En erken 1 gün sonrasına ders alabilirsiniz.");	//kullanıcının gecersiz tarih sectigi bildiriliyor
}

?>