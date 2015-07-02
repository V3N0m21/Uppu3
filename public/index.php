<?php
require '../vendor/autoload.php';
require_once '../app/bootstrap.php';





$app = new \Slim\Slim(array(
	'view' => new \Slim\Views\Twig(),
	'templates.path' => '../app/templates'
	));


$app->get('/hello/:world/', function($world) use ($app) {
	echo "Hello, $world";
});

$app->get('/', function() use ($app) {
	$page = 'index';
	echo $app->render('file_load.html', array('page' => $page));

});

$app->post('/', function() use ($app,$entityManager) {
	if (isset($_POST['load'])) {
		$pictures = array('image/jpeg','image/gif','image/png');
		$file = new \Uppu3\Resource\FileResource();
		$file->setName($_FILES['load']['name']);
		$file->setSize($_FILES['load']['size']);
		$finfo = new \finfo(FILEINFO_MIME_TYPE);
		$file->setExtension($finfo->file($_FILES['load']['tmp_name']));
		#$file->setMediainfo($_FILES['load']['tmp_name']);
		$file->setComment($_POST['comment']);
		$mediainfo = \Uppu3\Resource\MediaInfo::getMediaInfo($_FILES['load']['tmp_name']);
		$mediainfo = json_encode($mediainfo);
		$file->setMediainfo($mediainfo); 
		$file->setUploaded(); 
		$entityManager->persist($file);
		$entityManager->flush();
		$id = $file->getId();
		$tmpFile = $_FILES['load']['tmp_name'];
		$newFile = __DIR__."/upload/".$id."-".$_FILES['load']['name']."-txt";
		$result = move_uploaded_file($tmpFile, $newFile);
		if (in_array($file->getExtension(), $pictures)) {
			$path = __DIR__."/upload/resize/resize-".$_FILES['load']['name'];
			$resize = new \Uppu3\Helper\Resize($newFile, $path);	
		}
		if ($result) {
			$message = 'File was successfully uploaded';
		} else {
			$message = 'File failed to upload';
		}
		$app->redirect("/view/$id"); 
	}
});

$app->get('/view/:id/', function($id) use ($app, $entityManager) {
	$file = $entityManager->find('Uppu3\Resource\FileResource', $id);
	$info = $file->getMediainfo();
	$pictures = array('image/jpeg','image/gif','image/png');
	$video = array('video/webm', 'video/mp4');
	if (in_array($file->getExtension(), $pictures)){
	echo $app->render('view_image.html', array('file' => $file,
												'info' => $info));

} elseif (in_array($file->getExtension(), $video)) {
	echo $app->render('view_video.html', array('file' => $file,
											   'info' => $info));
} else {
	echo $app->render('view.html', array('file' => $file));
}
});

$app->get('/download/:id', function($id) use ($app, $entityManager) {
	$file = $entityManager->find('Uppu3\Resource\FileResource', $id);
	$name = "upload/".$id."-".$file->getName()."-txt";
	if (file_exists($name)) {
		header("X-Sendfile:".realpath(dirname(__FILE__)).'/'.$name);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=".$file->getName());
		exit;
	} else {
		echo "Not found";
	}
});

$app->get('/list', function() use ($app, $entityManager) {
	$page = 'list';
	$files = $entityManager->getRepository('Uppu3\Resource\FileResource')->findAll();
	echo $app->render('list.html', array('files' => $files,
										 'page' => $page));
});

$app->run();