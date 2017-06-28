<?php 
if (isset ( $_GET ['cid'] ) and is_numeric($_GET ['cid'])){
	$ArchivePath = Zend_Registry::get ( 'config' )->archive->path;
	$imgId = $_GET ['cid'];
	$person_id = $_SESSION['person']['selected'];
	
	$archive = new Table_ArchiveImages();
	$imgPath = $archive->getImagePath($imgId, $person_id);
	if(!is_null($imgPath)){
		$image = $ArchivePath.str_replace("\\", "/", $imgPath);
		
		//echo $image;
		ob_clean();
		header('Content-type: image/jpeg');
		//header('Content-Length: '.filesize($image));
		readfile($image);
	}
}
?>