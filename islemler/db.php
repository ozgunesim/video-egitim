<?php
/*

	bu dosya veritabanı islemlerinin yapıldıgı "DB" sınıfını barındırmaktadır.

*/
class DB
{
	private $pdo;

	//VERITABANI PARAMETRELERI
	private $dbHost = "localhost";
	private $dbKullanici = "root";
	private $dbSifre = "";
	private $dbName = "video_egitim";

	//sinifin yapilandiricisinda veritabani baglantisi kuruluyor ve pdo nesnesi olusturuluyor.
	function __construct(){
		try {
	 		$this->pdo = new PDO("mysql:host=" . $this->dbHost . ";dbname=" . $this->dbName . ";charset=utf8", $this->dbKullanici, $this->dbSifre);
		} catch ( PDOException $e ){	//hata var ise
		    print $e->getMessage();	//hata mesajini yazdir
		}
	}

	//uyelik girisi kontrolu
	public function uye_kontrol($mail,$sifre){

		//uye kaydini sinamak icin mysql sorgusu yazılıyor.
		$sorgu = $this->pdo->query("SELECT * FROM uyeler WHERE kul_mail = '{$mail}' AND kul_sifre = '{$sifre}'")->fetch(PDO::FETCH_ASSOC);
		if ( $sorgu ){	//sorgu deger dondurduyse

			//session(oturum) dizisine sorgudan donen uye bilgileri atiliyor.
		    $_SESSION['uye_adi'] = $sorgu['kul_nick'];
		    $_SESSION['uye_mail'] = $sorgu['kul_mail'];
		    $_SESSION['uye_yetki'] = $sorgu['kul_yetki'];
		    $_SESSION['uye_ID'] = $sorgu['ID'];
		    $_SESSION['oturum'] = true;
		    return true;
		}else{	//sorgudan deger donmediyse
			return false;
		}
	}

	//sifre degistirme isleminde gonderilen eski sifrenin gecerli sifre olup olmadıgı sorgulanıyor
	public function sifre_karsilastir($sifre){
		if($this->uye_kontrol($_SESSION['uye_mail'],$sifre))
			return true;
		else
			return false;
	}

	//id'si verilen uyenin sifresi degistiriliyor
	public function sifre_guncelle($id,$sifre){
		$sorgu = $this->pdo->query("update uyeler set kul_sifre='" . mysql_escape_string($sifre) . "' where ID=" . mysql_escape_string($id));
		if($sorgu)
			return true;
		else
			return false;
	}

	//id'si verilen uyenin mail adresi degistiriliyor
	public function email_guncelle($id,$mail){
		$sorgu = $this->pdo->query("update uyeler set kul_mail='" . mysql_escape_string($mail) . "' where ID=" . $id);
		if($sorgu){
			$_SESSION['uye_mail'] = $mail;
			return true;
		}
		else{
			return false;
		}
	}

	//id'si verilen uyenin skype adresi degistiriliyor
	public function skype_guncelle($id,$skype){
		$sorgu = $this->pdo->query("update uyeler set skype='" . mysql_escape_string($skype) . "' where ID=" . $id);
		if($sorgu){
			return true;
		}
		else{
			return false;
		}
	}

	//idsi alınan uye veritabanından siliniyor
	public function uye_sil($id){
		$sorgu = $this->pdo->query("DELETE FROM uyeler where ID = '{$id}'", PDO::FETCH_ASSOC);	
		if($sorgu)
			return true;
		else
			return false;
	}



	//kayıtlı uyeler kullanici adına gore sıralanarak getiriliyor
	public function uye_listesi_getir(){
		$sorgu = $this->pdo->query("SELECT * FROM uyeler ORDER BY kul_nick ASC", PDO::FETCH_ASSOC);	
		if ( $sorgu->rowCount() ){
			$sayac = 0;
			foreach( $sorgu as $satir ){
				$ara_dizi[$sayac] = $satir;
				$sayac++;
			}
			return $ara_dizi;
		}else{
			return false;
		}
	}
		


		//istenen aralıkta onay bekleyen hoca dizisini dondurur
		public function bekleyen_hoca_listesi_getir($sira,$limit){		
		$sorgu = $this->pdo->query("SELECT * FROM hoca_basvuru ORDER BY ID ASC LIMIT " . (($sira-1)*$limit) . "," . $limit, PDO::FETCH_ASSOC);
		if ( $sorgu->rowCount() ){
			$sayac = 0;
			foreach( $sorgu as $satir ){
				$ara_dizi[$sayac] = $satir;
				$sayac++;
			}
			return $ara_dizi;
		}else{
			return false;
		}
	}



	//verilen ders idsine gore ders konularını dondurur
	public function alt_kategori_getir($ust_id){
		$sorgu = $this->pdo->query("SELECT * FROM kategoriler WHERE ust_kategori_id = " . $ust_id . " ORDER BY kategori_adi ASC", PDO::FETCH_ASSOC);
		if ( $sorgu->rowCount() ){
			$sayac = 0;
			foreach( $sorgu as $satir ){
				$ara_dizi[$sayac]['ID'] = $satir['ID'];
				$ara_dizi[$sayac]['adi'] = $satir['kategori_adi'];
				$sayac++;
			}
			return $ara_dizi;
		}else{
			return false;
		}
	}


	//tum dersleri ve bu derslerin konularını dondurur
	public function kategori_getir(){
		$sorgu = $this->pdo->query("SELECT * FROM kategoriler WHERE ust_kategori_id=0 ORDER BY kategori_adi ASC", PDO::FETCH_ASSOC);
		$sayac = 0;
		if ( $sorgu->rowCount() ){
			foreach( $sorgu as $satir ){
				$kategori_dizi[$sayac]['ID'] = $satir['ID'];
				$kategori_dizi[$sayac]['adi'] = $satir['kategori_adi'];

				$alt_kategoriler = $this->alt_kategori_getir($satir['ID']);
				if($alt_kategoriler){
					$kategori_dizi[$sayac]['alt'] = $alt_kategoriler;
				}
				
				$sayac++;
			}
			return $kategori_dizi;
		}
		else{
			return false;
		}
	}

	
	//idsi verilen dersi siler
	public function kategori_sil($id){
		$sorgu = $this->pdo->query("DELETE FROM kategoriler where ID = '{$id}'", PDO::FETCH_ASSOC);
		//echo "DELETE FROM kategoriler where ID = '{$id}'";
		if($sorgu)
			return true;
		else
			return false;
	}


	//yeni ders veya konu ekler
	public function yeni_kategori_ekle($adi,$ust_id = 0){
		$sorgu = $this->pdo->prepare("INSERT INTO kategoriler SET
			kategori_adi = ?,
			ust_kategori_id = ?"
		);

		$sonuc = $sorgu->execute(array(
		    $adi, $ust_id
		));

		if($sonuc)
			return true;
		else
			return false;

	}


	//adı verilen dersin idsini dondurur
	public function kategori_id_getir($kategori_adi){
		$sorgu = $this->pdo->query("SELECT ID FROM kategoriler WHERE kategori_adi = '{$kategori_adi}'")->fetch(PDO::FETCH_ASSOC);
		if( $sorgu ){
			$video_kategori_id = $sorgu['ID'];
			return $video_kategori_id;
		}else{
			return false;
		}
	}


	//idsi verilen dersin adını dondurur
	public function kategori_adi_getir($kategori_id){
		$sorgu = $this->pdo->query("SELECT kategori_adi FROM kategoriler WHERE ID = '{$kategori_id}'")->fetch(PDO::FETCH_ASSOC);
		if( $sorgu ){
			$video_kategori_adi = $sorgu['kategori_adi'];
			return $video_kategori_adi;
		}else{
			return false;
		}
	}


	//isdi verilen uyenin bilgilerini dondurur
	public function uye_bilgi_getir($uye_id){
		$sorgu = $this->pdo->query("SELECT * FROM uyeler WHERE ID = '{$uye_id}'")->fetch(PDO::FETCH_ASSOC);
		if( $sorgu ){
			$uye_dizi['ID'] = $uye_id;
			$uye_dizi['uye_adi'] = $sorgu['kul_nick'];
			$uye_dizi['uye_mail'] = $sorgu['kul_mail'];
			$uye_dizi['uye_yetki'] = $sorgu['kul_yetki'];
			$uye_dizi['skype'] = $sorgu['skype'];
			return $uye_dizi;
		}else{
			return false;
		}
	}


	//toplam uye sayısını dondurur
	public function uye_sayi_getir(){
		$sorgu = $this->pdo->query("SELECT COUNT(*) FROM uyeler")->fetch(PDO::FETCH_ASSOC);
		if( $sorgu ){
			return $sorgu["COUNT(*)"];
		}else{
			return false;
		}
	}

	//tum hocaların listesini dondurur
	public function hoca_listesi_getir(){
		$sorgu = $this->pdo->query("SELECT * FROM uyeler WHERE kul_yetki = 2", PDO::FETCH_ASSOC);
		$sayac = 0;
		if ( $sorgu ){
			foreach( $sorgu as $satir ){
				$uye_dizi[$sayac]['adi'] = $satir['kul_nick'];
				$uye_dizi[$sayac]['ID'] = $satir['ID'];
				$sayac++;
			}
			return $uye_dizi;
		}
		else{
			return false;
		}
	}


	//idsi verilen hoca kaydını siler
	public function hoca_kayit_reddet($id){
		$sorgu = $this->pdo->query("DELETE FROM hoca_basvuru where ID = '{$id}'", PDO::FETCH_ASSOC);
		if($sorgu)
			return true;
		else
			return false;
	}


	//idsi verilen hocanın bilgisini dondurur
	public function hoca_bilgi_getir($id){
		$sorgu = $this->pdo->query("SELECT * FROM hoca_bilgi WHERE uye_id = $id")->fetch(PDO::FETCH_ASSOC);

		if( $sorgu ){
			$uye_dizi['bilgi'] = $sorgu['bilgi'];
			return $uye_dizi;
		}else{
			return false;
		}
	}


	//idsi verilen hocanın akademik bilgilerini gunceller
	public function hoca_bilgi_guncelle($id,$bilgi){
		$sorgu = $this->pdo->query("UPDATE hoca_bilgi set bilgi = '" . $bilgi . "' WHERE uye_id=$id", PDO::FETCH_ASSOC);
		if( $sorgu ){
			return true;
		}else{
			return false;
		}
	}


	//idsi verilen hoca kaydını onaylar
	//hoca bilgilerini ve uyelik bilgilerini ilgili tablolara taşır
	public function hoca_kayit_onayla($id){
		$sorgu = $this->pdo->query("SELECT * FROM hoca_basvuru WHERE ID = '{$id}'")->fetch(PDO::FETCH_ASSOC);
		if( $sorgu ){
			$bekleyen_id = $sorgu['ID'];
			$mail = $sorgu['kul_mail'];
			$sifre = $sorgu['kul_sifre'];
			$nick = $sorgu['kul_nick'];
			$bilgi = $sorgu['bilgi'];
		}else{
			return false;
		}


		$sorgu = $this->pdo->prepare("INSERT INTO uyeler SET
			kul_mail = ?,
			kul_sifre = ?,
			kul_nick = ?,
			kul_yetki = ?"
		);

		$sonuc = $sorgu->execute(array(
		    $mail, $sifre, $nick, 2
		));

		if( $sonuc ){
			$son_eklenen = $this->pdo->lastInsertId();

			$sorgu = $this->pdo->prepare("INSERT INTO hoca_bilgi SET
				uye_id = ?, bilgi = ?"
			);


			echo $sonuc = $sorgu->execute(array(
				$son_eklenen, $bilgi
			));

			$this->hoca_kayit_reddet($bekleyen_id);

			return true;

		}else{
			return false;
		}
	}


	//yeni hoca basvurusu olusturur
	public function hoca_kaydet($mail,$sifre,$kullanici,$bilgi,$skype){
		$sorgu = $this->pdo->prepare("INSERT INTO hoca_basvuru SET
			kul_mail = ?,
			kul_sifre = ?,
			kul_nick = ?,
			bilgi = ?,
			skype = ?"
		);

		$sonuc = $sorgu->execute(array(
		    $mail, $sifre, $kullanici, $bilgi,$skype
		));

		if($sorgu)
			return true;
		else
			return false;
	}


	//yeni ogrenci uyeligi olusturur
	public function ogrenci_kaydet($mail,$sifre,$kullanici,$skype){
		$sorgu = $this->pdo->prepare("INSERT INTO uyeler SET
			kul_mail = ?,
			kul_sifre = ?,
			kul_nick = ?,
			kul_yetki = ?,
			skype = ?"
		);
		try{
			$sonuc = $sorgu->execute(array(
			    $mail, $sifre, $kullanici, 3, $skype
			));
			return true;
		}catch(PDOException $e){
			return print_r($e->errorInfo());
		}
	}


	//toplam video sayısını dondurur
	public function video_sayi_getir(){
		$sorgu = $this->pdo->query("SELECT COUNT(*) FROM videolar")->fetch(PDO::FETCH_ASSOC);
		if( $sorgu ){
			return $sorgu["COUNT(*)"];
		}else{
			return false;
		}
	}

	//idsi verilmis bir hocanın toplam kac video yukledigini dondurur
	public function hoca_video_sayi_getir($id){
		$sorgu = $this->pdo->query("SELECT COUNT(*) FROM videolar WHERE yukleyen_id = $id")->fetch(PDO::FETCH_ASSOC);
		if( $sorgu ){
			return $sorgu["COUNT(*)"];
		}else{
			return false;
		}
	}


	//yeni video bilgilerini veritabanına kaydeder
	//eger video daha once yuklenmise hata dondurur
	//video basarıyla yuklenirse yuklenen videonun idsini dondurur
	public function video_kaydet($video_adi,$video_aciklamasi,$video_kategori){
		
		$sorgu = $this->pdo->query("SELECT * FROM videolar WHERE video_adi = '{$video_adi}' AND video_kategori = '{$video_kategori}'");

		$donen_sayi = $sorgu->rowCount();
		if($donen_sayi>0){
			return false;
		}

		//$video_kategori_id = $this->kategori_id_getir($video_kategori);

		//if(!$video_kategori_id)
		//	return false;

		$sorgu = $this->pdo->prepare("INSERT INTO videolar SET
			video_adi = ?,
			yukleyen_id = ?,
			video_aciklama = ?,
			video_kategori = ?"
		);

		$sonuc = $sorgu->execute(array(
		    $video_adi, $_SESSION['uye_ID'], $video_aciklamasi, $video_kategori
		));

		if( $sonuc ){
			$son_eklenen = $this->pdo->lastInsertId();
			return $son_eklenen;
		}else{
			return false;
		}
	}


	//idsi verilen videonun izlenme sayisini artırır
	public function video_izlenme_artir($id){
		$sorgu = $this->pdo->query("UPDATE videolar SET video_izlenme=video_izlenme+1 WHERE ID = '{$id}'", PDO::FETCH_ASSOC);
		if($sorgu){
			return true;
		}else{
			return false;
		}
	}

	//idsi verilen videonun bilgilerini dondurur
	public function video_getir($id){
		$sorgu = $this->pdo->query("SELECT * FROM videolar WHERE ID = '{$id}'", PDO::FETCH_ASSOC);
		$sayac = 0;
		if ( $sorgu ){
			foreach( $sorgu as $satir ){
				$video['ID'] = $satir['ID'];
				$video['video_adi'] = $satir['video_adi'];
				$video['video_aciklamasi'] = $satir['video_aciklama'];
				$video['video_yukleyen'] = $this->uye_bilgi_getir($satir['yukleyen_id']);
				$video['video_izlenme'] = $satir['video_izlenme'];
				$video['video_kategori'] = $this->kategori_adi_getir($satir['video_kategori']);
				$sayac++;
			}
			return $video;
		}
		else{
			return false;
		}
	}

	//yuklenen son 10 videoyu dondurur
	public function son_10_video_getir(){
		$sorgu = $this->pdo->query("SELECT * FROM videolar ORDER BY ID DESC LIMIT 0,10", PDO::FETCH_ASSOC);
		$sayac = 0;
		if ( $sorgu->rowCount() ){
			foreach( $sorgu as $satir ){
				$video_dizi[$sayac]['ID'] = $satir['ID'];
				$video_dizi[$sayac]['video_adi'] = $satir['video_adi'];
				$video_dizi[$sayac]['video_aciklamasi'] = $satir['video_aciklama'];
				$video_dizi[$sayac]['video_yukleyen'] = $this->uye_bilgi_getir($satir['yukleyen_id']);
				$video_dizi[$sayac]['video_izlenme'] = $satir['video_izlenme'];
				$video_dizi[$sayac]['video_kategori'] = $this->kategori_adi_getir($satir['video_kategori']);
				$sayac++;
			}
			return $video_dizi;
		}
		else{
			return false;
		}
	}


	//en cok izlenen 10 videoyu dondurur
	public function top_10_video_getir(){
		$sorgu = $this->pdo->query("SELECT * FROM videolar ORDER BY video_izlenme DESC LIMIT 0,10", PDO::FETCH_ASSOC);
		$sayac = 0;
		if ( $sorgu->rowCount() ){
			foreach( $sorgu as $satir ){
				$video_dizi[$sayac] = $this->video_bilgi_yukle($satir);
				$sayac++;
			}
			return $video_dizi;
		}
		else{
			return false;
		}
	}


	//verilen video bilgisi dizisini duzenleyerek dondurur
	private function video_bilgi_yukle($satir){
		$video_dizi['ID'] = $satir['ID'];
		$video_dizi['video_adi'] = $satir['video_adi'];
		$video_dizi['video_aciklamasi'] = $satir['video_aciklama'];
		$video_dizi['video_yukleyen'] = $this->uye_bilgi_getir($satir['yukleyen_id']);
		$video_dizi['video_izlenme'] = $satir['video_izlenme'];
		$video_dizi['video_kategori'] = $this->kategori_adi_getir($satir['video_kategori']);
		return $video_dizi;
	}


	//konusu ve yukleyen hocası belirtilen videoları dondurur
	public function video_listele($ust_id,$hoca_id){
		$ek = "";
		if($hoca_id != ""){
			$ek = " AND yukleyen_id=" . $hoca_id;
		}
		$sorgu = $this->pdo->query("SELECT * FROM videolar where video_kategori = $ust_id" . $ek, PDO::FETCH_ASSOC);
		$sayac = 0;
		if( $sorgu->rowCount() ){
			foreach ($sorgu as $satir) {
				$video_dizi[$sayac] = $this->video_bilgi_yukle($satir);
				$sayac++;
			}
			return $video_dizi;
		}else{
			return false;
		}
	}


	//belirtilen aralıktaki tum videoları dondurur
	public function tum_videolari_listele($sira, $limit){
		$sorgu = $this->pdo->query("SELECT * FROM videolar ORDER BY ID DESC LIMIT " . (($sira-1)*$limit) . "," . $limit, PDO::FETCH_ASSOC);
		$sayac = 0;
		if( $sorgu->rowCount() ){
			foreach ($sorgu as $satir) {
				$video_dizi[$sayac] = $this->video_bilgi_yukle($satir);
				$sayac++;
			}
			return $video_dizi;
		}else{
			return false;
		}
	}


	//idsi verilen hocanın yukledigi videolardan belirli bir aralık dondurur
	public function hoca_videolari_listele($sira, $limit, $hoca_id){
		$sorgu = $this->pdo->query("SELECT * FROM videolar WHERE yukleyen_id = $hoca_id ORDER BY ID DESC LIMIT " . (($sira-1)*$limit) . "," . $limit, PDO::FETCH_ASSOC);
		$sayac = 0;
		if( $sorgu->rowCount() ){
			foreach ($sorgu as $satir) {
				$video_dizi[$sayac] = $this->video_bilgi_yukle($satir);
				$sayac++;
			}
			return $video_dizi;
		}else{
			return false;
		}
	}


	//aranılan kelimeyi iceren konulardaki videoları dondurur
	//istege gore bu videolardan belli bir hocanın yuklediklerini dondurur
	public function video_ara($kelime,$hoca_id = ""){
		$sorgu = $this->pdo->query("SELECT * FROM kategoriler where kategori_adi LIKE '%". $kelime . "%' AND ust_kategori_id != 0", PDO::FETCH_ASSOC);
		$sayac = 0;
		if ( $sorgu->rowCount() ){
			foreach( $sorgu as $satir ){
				if($hoca_id == ""){
					$video_dizi[$sayac] = $this->video_listele($satir['ID'],"");
				}else{
					$video_dizi[$sayac] = $this->video_listele($satir['ID'],$hoca_id);
				}
				$sayac++;
			}
			return $video_dizi;
		}
		else{
			return false;
		}
	}


	//idsi verilen videoyu veritabanından siler
	public function video_sil($id){
		$sorgu = $this->pdo->query("DELETE FROM videolar where ID = '{$id}'", PDO::FETCH_ASSOC);
		if($sorgu)
			return true;
		else
			return false;
	}


	//son haberleri dondurur
	public function haber_getir($limit = 5){
		$sorgu = $this->pdo->query("SELECT * FROM haberler ORDER BY ID DESC LIMIT 0,$limit", PDO::FETCH_ASSOC);
		$sayac = 0;
		if ( $sorgu ){
			foreach( $sorgu as $satir ){
				$haber_dizi[$sayac] = $satir;
				$sayac++;
			}
			return $haber_dizi;
		}
		else{
			return false;
		}
	}


	//id'si verilen haberi veritabanından siler
	public function haber_sil($id){
		$sorgu = $this->pdo->query('delete from haberler where ID=' . $id);
		if($sorgu){
			return true;
		}else{
			return false;
		}
	}



	//veritabanına yeni haber ekler
	public function haber_ekle($metin){
		$sorgu = $this->pdo->prepare("INSERT INTO haberler SET
			metin = ?"
		);

		$sonuc = $sorgu->execute(array(
		    $metin
		));

		if($sonuc)
			return true;
		else
			return false;

	}

	//son duyuruları dondurur
	public function duyuru_getir($limit = 5){
		$sorgu = $this->pdo->query("SELECT * FROM duyuru ORDER BY ID DESC LIMIT 0,$limit", PDO::FETCH_ASSOC);
		$sayac = 0;
		if ( $sorgu ){
			foreach( $sorgu as $satir ){
				$duyuru_dizi[$sayac] = $satir;
				$sayac++;
			}
			return $duyuru_dizi;
		}
		else{
			return false;
		}
	}

	//id'si verilen duyuruyu veritabanından siler
	public function duyuru_sil($id){
		$sorgu = $this->pdo->query('delete from duyuru where ID=' . $id);
		if($sorgu){
			return true;
		}else{
			return false;
		}
	}


	//veritabanına yeni duyuru ekler
	public function duyuru_ekle($metin){
		$sorgu = $this->pdo->prepare("INSERT INTO duyuru SET
			metin = ?"
		);

		$sonuc = $sorgu->execute(array(
		    $metin
		));

		if($sonuc)
			return true;
		else
			return false;

	}


	//yeni ozel ders rezervasyonu olusturur
	//aynı gun ve saatte baska biri tarafından rezervasyon alınmıssa hata dondurur
	//aynı kisiden aynı dersi bir daha almaya kalkıldıgında hata dondurur
	//reddedilmis bir dersi almaya kalkıldıgında hata dondurur
	//aynı saatte farklı dersler alınmaya kalkıldıgında hata dondurur
	public function rezervasyon_olustur($dizi){
		$sorgu = $this->pdo->query(
			"SELECT * 
			FROM `rezervasyon`
			WHERE
			(hoca_id = " .$dizi['hoca_id'] . " and ogr_id = " . $dizi['ogr_id'] . " and konu_id = " . $dizi['konu_id'] . " and (durum=3 or durum=1))

			OR

			(ogr_id= " . $dizi['ogr_id'] . " and konu_id = " . $dizi['konu_id'] . " and (durum=3 or durum=1))

			OR

			(tarih = '" . date ("Y-m-d H:i",strtotime($dizi['tarih'])) . "' and (durum=3 or durum=1))

			OR 

			(tarih = '" . date ("Y-m-d H:i",strtotime($dizi['tarih'])) . "' and hoca_id = " . $dizi['hoca_id'] . " and ogr_id = " . $dizi['ogr_id'] . "  and (durum=1 or durum = 3))

			OR 

			(tarih = '" . date ("Y-m-d H:i",strtotime($dizi['tarih'])) . "' and ogr_id = " . $dizi['ogr_id'] . " and (durum=1 or durum = 3))"

		)->fetch(PDO::FETCH_ASSOC);


		if( $sorgu ){													//olası hatalar donduruluyor
			return "Bu hatanın sebepleri şunlar olabilir:<br>
			1) Bu eğitmenden aynı konuda zaten onay bekleyen bir dersiniz var.<br>
			2) Bu ders zaten onaylanmış ancak ders tarihi henüz gelmemiş.<br>
			3) Aynı saat ve tarihte başka bir dersiniz var.<br>
			4) Bu eğitmen bu tarihteki rezervasyonunuzda sizi reddetmiş olabilir.";
		}


		$sorgu = $this->pdo->prepare("INSERT INTO rezervasyon (hoca_id,ogr_id,konu_id,aciklama,tarih,durum)
			VALUES (:hoca_id, :ogr_id, :konu_id, :aciklama, :tarih, :durum)"
		);


		$sorgu->bindParam (":hoca_id", $dizi['hoca_id'], PDO::PARAM_INT);
		$sorgu->bindParam (":ogr_id", $dizi['ogr_id'], PDO::PARAM_INT);
		$sorgu->bindParam (":konu_id", $dizi['konu_id'], PDO::PARAM_INT);
		$sorgu->bindParam (":aciklama", $dizi['aciklama'], PDO::PARAM_STR);
		$sorgu->bindParam (":tarih", date ("Y-m-d H:i",strtotime($dizi['tarih'])), PDO::PARAM_STR);
		$sorgu->bindParam (":durum", $dizi['durum'], PDO::PARAM_INT);
		
		$sonuc = $sorgu->execute();

		if($sonuc){
			$tarih = new DateTime($dizi['tarih']);
			$this->bildirim_yolla($tarih->format("Y-m-d H:i") . " tarihli bir ders isteği gönderildi.",$dizi['hoca_id']);
			return true;
		}
		else{
			return false;
		}
	}


	//idsi verilen ozel ders rezervasyonunun bilgisini dondurur
	public function rezervasyon_getir($id){
		$sorgu = $this->pdo->query("SELECT * FROM rezervasyon WHERE hoca_id = " .$id. " OR ogr_id = " .$id . " ORDER BY ID DESC", PDO::FETCH_ASSOC);
		$sayac = 0;
		if ( $sorgu ){
			error_reporting(0);
			foreach( $sorgu as $satir ){
				$dizi[$sayac] = $satir;
				$sayac++;
			}
			error_reporting(1);
			if(isset($dizi))
				return $dizi;
			else
				return false;
		}
		else{
			return false;
		}
	}


	//idsi verilen dersin hoca tarafından reddedilmesini saglar
	public function hoca_rezervasyon_reddet($id){
		$sorgu = $this->pdo->query("UPDATE rezervasyon SET durum=2 WHERE ID = $id", PDO::FETCH_ASSOC);
		if($sorgu){
			$sorgu = $this->pdo->query("SELECT tarih,ogr_id FROM rezervasyon WHERE ID = $id")->fetch(PDO::FETCH_ASSOC);
			if($sorgu){
				$kime = $sorgu['ogr_id'];
				$tarih = new DateTime($sorgu['tarih']);

				$this->bildirim_yolla($tarih->format("Y-m-d H:i") . " tarihli bir dersiniz eğitmen tarafından reddedildi.",$kime);

			}
			return true;
		}else{
			return false;
		}
	}


	//idsi verilen dersin hoca tarafından onaylanmasını saglar
	public function hoca_rezervasyon_onayla($id){
		$sorgu = $this->pdo->query("UPDATE rezervasyon SET durum=1 WHERE ID = $id", PDO::FETCH_ASSOC);
		if($sorgu){
			$sorgu = $this->pdo->query("SELECT tarih,ogr_id FROM rezervasyon WHERE ID = $id")->fetch(PDO::FETCH_ASSOC);
			if($sorgu){
				$kime = $sorgu['ogr_id'];
				$tarih = new DateTime($sorgu['tarih']);

				$this->bildirim_yolla($tarih->format("Y-m-d H:i") . " tarihli bir dersiniz eğitmen tarafından onaylandı.",$kime);
			}
			return true;
		}else{
			return false;
		}
	}

	//buyuk / kucuk tarih karsılastırması yapar
	public function sure_gecti($tarih1,$tarih2){
		if($tarih1 > $tarih2)
			return true;
		else
			return false;
	}


	//idsi verilen hocanın yukledigi ders videolarının hangi ders konularına ait oldugunu dondurur. konuları tekrar etmez. farklı olanları dondurur
	public function hoca_konu_getir($id){
		$sorgu = $this->pdo->query("select distinct video_kategori from videolar where yukleyen_id=$id ORDER BY `videolar`.`video_kategori` ASC", PDO::FETCH_ASSOC);
		if($sorgu){
			$sayac=0;
			foreach ($sorgu as $satir) {
				$ders_dizi[$sayac]['ID'] = $satir['video_kategori'];
				$ders_dizi[$sayac]['adi'] = $this->kategori_adi_getir($satir['video_kategori']);
				$sayac++;
			}
			if(isset($ders_dizi))
				return $ders_dizi;
			else
				return false;
		}else{
			return false;
		}

	}

	//idsi verilen hocanın takvimini parametre olarak aldıgı dizideki degerlere gore gunceller
	public function hoca_takvim_kaydet($dizi,$id){
		$kayit = "";
		for($i=0; $i<count($dizi); $i++){
			$kayit.=$dizi[$i];
			if($i != count($dizi)-1)
				$kayit .= "|";
		}

		if($kayit != ""){
			$sorgu = $this->pdo->query("UPDATE hoca_bilgi SET takvim = '{$kayit}' WHERE uye_id = $id");
			if($sorgu){
				return true;
			}else{
				return $this->pdo->errorInfo();
			}
		}
		
	}

	public function hoca_ders_uygun($tarih,$hoca_id){
		$sorgu = $this->pdo->query("SELECT * FROM rezervasyon where hoca_id = " . $hoca_id . " and tarih = '" . $tarih . "' and (durum = 1 or durum = 3)")->fetch(PDO::FETCH_ASSOC);
		if($sorgu)
			return false;
		else
			return true;
	}


	//idsi verilen hocanıntakvim bilgilerini dondurur
	public function hoca_takvim_getir($id){
		$sorgu = $this->pdo->query("SELECT * FROM hoca_bilgi WHERE uye_id = $id")->fetch(PDO::FETCH_ASSOC);
		if ( $sorgu ){	//sorgu deger dondurduyse
			$kayitlar = explode("|", $sorgu['takvim']);
			$sayac = 0;
			foreach ($kayitlar as $satir) {
				$bilgiler = explode(":",$satir);

				$saatGun = $bilgiler[0];
				if(strlen($saatGun) == 3){
					$saat = substr($saatGun, 0,2);
					$gun = substr($saatGun, 2,1);
				}else if(strlen($saatGun) == 2){
					$saat = substr($saatGun, 0,1);
					$gun = substr($saatGun, 1,1);
				}

				if(!isset($bilgiler) || !is_array($bilgiler))
					return false;
				
				$ders_id = $bilgiler[1];



				$takvim_dizi[$sayac] = array('saat'=>$saat,'gun'=>$gun,'ders_id'=>$ders_id);
				$sayac++;
			}
			if(isset($takvim_dizi)){
				return $takvim_dizi;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	//hocanın idsi verilen ozel dersi iptal etmesini saglar
	public function hoca_rezervasyon_iptal($id){
		$sorgu = $this->pdo->query("SELECT tarih,ogr_id FROM rezervasyon WHERE ID = $id")->fetch(PDO::FETCH_ASSOC);
		if ( $sorgu ){	//sorgu deger dondurduyse

			$tarih1 = new DateTime();
			$tarih1 = $tarih1->add(new DateInterval('P1D'));
			$tarih2 = new DateTime($sorgu['tarih']);
			if($this->sure_gecti($tarih1,$tarih2))
				return "Süre Geçmiş";

			$kime = $sorgu['ogr_id'];
			$tarih = new DateTime($sorgu['tarih']);

		}else{
			return "Sorgu Hatası";
		}

		$sorgu = $this->pdo->query("UPDATE rezervasyon SET durum=5 WHERE ID = $id", PDO::FETCH_ASSOC);
		if($sorgu){
			$this->bildirim_yolla($tarih->format("Y-m-d H:i") . " tarihli bir dersiniz eğitmen tarafından iptal edildi.",$kime);
			return true;
		}else{
			return false;
		}
	}


	//ogrencinin idsi verilen ozel dersi iptal etmesini saglar
	public function ogrenci_rezervasyon_iptal($id){
		$sorgu = $this->pdo->query("SELECT tarih,hoca_id FROM rezervasyon WHERE ID = $id")->fetch(PDO::FETCH_ASSOC);
		if ( $sorgu ){	//sorgu deger dondurduyse

			$tarih1 = new DateTime();
			$tarih1 = $tarih1->add(new DateInterval('P1D'));
			$tarih2 = new DateTime($sorgu['tarih']);
			if($this->sure_gecti($tarih1,$tarih2))
				return "Süre Geçmiş";

			$kime = $sorgu['hoca_id'];
			$tarih = new DateTime($sorgu['tarih']);

		}else{
			return "Sorgu Hatası";
		}
		
		$sorgu = $this->pdo->query("UPDATE rezervasyon SET durum=6 WHERE ID = $id", PDO::FETCH_ASSOC);
		if($sorgu){
			$this->bildirim_yolla($tarih->format("Y-m-d H:i") . " tarihli bir dersiniz öğrenci tarafından iptal edildi.",$kime);
			return true;
		}else{
			return false;
		}
	}


	//$idsi verilen uyenin ozel ders bilgilerinden onay bekleyen ve onaylanmıs olanlar haric hepsinin silinmesini saglar
	public function rezervasyon_temizle($id){
		$sonuc = $this->pdo->query("DELETE FROM rezervasyon WHERE (hoca_id = $id OR ogr_id = $id) AND (durum = 2 OR durum = 4 OR durum = 5 OR durum = 6)", PDO::FETCH_ASSOC);
		if($sonuc)
			return true;
		else
			return false;
	}


	//renklendirilecek takvimin bilgilerini json formatında dondurur
	public function takvim_getir($id){
		//  {"date":"2015-06-01"   ,   "badge":false  ,  "title":"Example 1"}
		$sorgu = $this->pdo->query("SELECT * FROM rezervasyon WHERE (hoca_id = " .$id. " OR ogr_id = " .$id . ") AND (durum != 2 AND durum != 5 AND durum != 6) ORDER BY ID DESC", PDO::FETCH_ASSOC);
		$sayac = 0;
		if ( is_array($sorgu) ){
			$uzn = count($sorgu);
			foreach( $sorgu as $satir ){
				$durum = ["","takvim_onay","takvim_red","takvim_bekleyen","takvim_gecti","takvim_iptal","takvim_iptal"];
				if($_SESSION['uye_yetki'] == 1 || $_SESSION['uye_yetki'] == 2)
					$durum[2] = "";

				$tarihSaat = explode(" ",$satir['tarih']);
				$tarih = $tarihSaat[0];
				// $tarihDizi = explode("-", $tarih);
				// $sontarih = $tarihDizi[2] . "-" . $tarihDizi[1] . "-" . $tarihDizi[0];
				//$virgul = ( $sayac != $uzn+1 ) ? "," : "";
				$virgul = ",";
				$jsonDizi[$sayac] = "{\"date\":\"" . $tarih . "\",\"badge\":false,\"classname\":\"" . $durum[$satir['durum']] . "\",\"title\":\"" . trim($satir['aciklama']) . "\"}" . $virgul . "\n";
				$sayac++;
			}
			return $jsonDizi;
		}
		else{
			return false;
		}
	}

	//gecmis derslerin durumunu gecmis olarak gunceller
	public function gecmis_ders_guncelle(){
		$sorgu = $this->pdo->query("UPDATE rezervasyon SET durum=4 WHERE durum != 4 AND tarih < NOW()", PDO::FETCH_ASSOC);
		if($sorgu){
			return true;	
		}else{
			return false;
		}
	}


	//idsi belirtilmis kisiye yeni bildirim olusturur
	public function bildirim_yolla($metin,$kime){
		$sorgu = $this->pdo->query("insert into bildirim(metin,kime,durum) values('{$metin}',$kime,1)");

		if( $sorgu ){
			return true;
		}else{
			return $this->pdo->errorInfo();
		}

	}

	//idsi belirtilmis kisinin okunmamıs bildirim sayısını dondurur
	public function bildirim_sayi_getir($id){
		$sorgu = $this->pdo->query("SELECT * FROM bildirim WHERE kime = " . $id . " AND durum = 1");
		if($sorgu){
			return $sorgu->rowCount();
		}else{
			return false;
		}
	}

	//istege gore idsi verilmis uyenin okunmus ya da tum bildirimleri dondurur
	public function bildirim_getir($id, $hepsi = true){
		if(!$hepsi)
			$sorgu = $this->pdo->query("SELECT * FROM bildirim WHERE kime = " . $id . " AND durum = 1 ORDER BY ID DESC LIMIT 0,100");
		else
			$sorgu = $this->pdo->query("SELECT * FROM bildirim WHERE kime = " . $id . " ORDER BY ID DESC LIMIT 0,100");

		$sayi = $sorgu->rowCount();
		if($sorgu){
			$sayac = 0;
			foreach ($sorgu as $satir) {
				$dizi[$sayac] = $satir;
				$sayac++;
			}
			return $dizi;
		}else{
			return false;
		}
	}


	//idsi verilmis uyenin tum bildirimlerini okundu olarak gunceller
	public function bildirim_okundu_yap($id){
		$sorgu = $this->pdo->query("UPDATE bildirim SET durum = 0 WHERE kime = " . $id . " AND durum = 1");

		if($sorgu){
			return true;
		}else{
			return "UPDATE bildirim SET durum = 0 WHERE kime = " . $id . " AND durum = 1";
		}
	}

	//slaytın metin bilgileri veri tabanına kaydediliyor eklenen satirin id'si donduruluyor
	public function slayt_kaydet($baslik,$aciklama){
		$sorgu = $this->pdo->query("insert into slaytlar(baslik,aciklama) values('{$baslik}','{$aciklama}')");
		$id = $this->pdo->lastInsertId();
		if($sorgu)
			return $id;
		else
			return false;
	}

	//varolan slaytın metin bilgileri duzenleniyor
	public function slayt_guncelle($id,$baslik,$aciklama){
		$sorgu = $this->pdo->query("update slaytlar set baslik = '{$baslik}' , aciklama = '{$aciklama}' where ID = $id");
		if($sorgu)
			return true;
		else
			return false;
	}

	//id'si verilen slayt siliniyor
	public function slayt_sil($id){
		$sorgu = $this->pdo->query("delete from slaytlar where ID = " . $id);

		if($sorgu)
			return true;
		else
			return false;
	}

	//id'si verilen slayt ya da tum slaytlar donduruluyor
	public function slayt_getir($id = "true"){
		$sorgu = $this->pdo->query("select * from slaytlar where $id");
		if($sorgu){
			$sayac = 0;
			foreach ($sorgu as $satir) {
				if($id=="true")$dizi[$sayac] = $satir;
				else $dizi = $satir;
				$sayac++;
			}
			return $dizi;
		}else{
			return false;
		}
	}

	//id'si verilen uyenin yetkisini aldıgı yetki parametresine gore degistirir
	public function uye_yetkilendir($id,$yetki){
		$sorgu =  $this->pdo->query("Update uyeler set kul_yetki = " . $yetki . " where ID = " . $id);
		if($sorgu){
			return true;
		}else{
			return false;
		}
	}

	
}

?>