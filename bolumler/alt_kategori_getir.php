<?php
//veri.php olarak kaydedin
//header("Content-Type: text/html; charset=iso-8859-9");

require('../islemler/db.php');	//veritabanı sınıfını barındıran php dosyası ekleniyor
$db = new DB();

?>
<br>
<strong>Konu</strong>
<select id='konular' title='Konuyu Seçin' class='form-control' name='video_konu'>";
    <option disabled selected>Konu Seçin</option>
<?php
    //$ust_kategori_id = $db->kategori_id_getir($_POST['kat_id']);
    $dizi = $db->alt_kategori_getir($_POST['kat_id']);				//secilen dersin konuları veri tabanından cekiliyor

    foreach ($dizi as $satir) {
        ?>
            <option value="<?php echo $satir['ID']; ?>" > <?php echo $satir['adi']; ?> </option>
        <?php  		//cekilen konular listelendi ve select kontrolune atıldı
    }
echo "</select>";
?>