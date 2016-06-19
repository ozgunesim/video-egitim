<?php

class thumbs{
	public function thumbnailKaydet($video_id,$kaynak){
		$ffmpeg = "C:\\ffmpeg\\bin\\ffmpeg";
		$kareSaniye = 5;
		$komut = "$ffmpeg -i $kaynak -an -ss $kareSaniye -s 320x240 img/thumbs/$video_id-lg.jpg";
		if(!shell_exec($komut))
			return true;
		else
			return false;
	}
}

?>