<?php
if(!isset($_SESSION))
	session_start();

if(isset($_POST['ID'])){
	require('db.php');
	$db = new DB();
	echo $db->bildirim_okundu_yap($_POST['ID']);		//bildirimler okunduysa okundu olarak veritabanında isaretleniyor
}

?>	