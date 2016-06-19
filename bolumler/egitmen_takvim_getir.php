<?php
require('../islemler/db.php');
$db = new DB();
if(isset($_GET['id'])){
	$id = $_GET['id'];
	$takvimDizi = $db->hoca_takvim_getir($id);
	$sayac = 0;
	foreach ($takvimDizi as $satir) {

		for($k = 0; $k<7; $k++){

		}
		
	}
	

		// $ajaxDizi[0] = array(
		//     "date"=>"2015-06-15",
		//     //"badge"=>true,
		//     "title"=>"Tonight",
		//     "body"=>"<p class=\"lead\">Hoca ID:" . $id . "</p>",
		//     "footer"=>"At Paisley Park",
		//     "classname"=>"takvim_onay"
  		// );

	echo json_encode($ajaxDizi);
}

?>