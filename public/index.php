<?php
require '../vendor/autoload.php';
require_once '../app/bootstrap.php';
use Uppu3\Helper\FormatHelper as FormatHelper;





$app = new \Slim\Slim(array(
	'view' => new \Slim\Views\Twig(),
	'templates.path' => '../app/templates'
	));

$app->container->singleton('em', function() use ($entityManager) {

	return  $entityManager;
});

$app->get('/hello/:world/', function($world) use ($app) {
	echo "Hello, $world";
});

$app->get('/', function() use ($app) {
	$page = 'index';
	$flash = '';
	$app->render('file_load.html', array('page' => $page,
										 'flash' => $flash));

});

$app->get('/uri', function() use ($app) {
    $resize = new \Uppu3\Helper\Resize;

});
$app->post('/', function() use ($app) {
	if (file_exists($_FILES['load']['tmp_name'])) {
		$pictures = array('image/jpeg','image/gif','image/png');
		$file = new \Uppu3\Resource\FileResource();
		$file->saveFile($_FILES['load']);
		$app->em->persist($file);
		$app->em->flush();
		$id = $file->getId();
		$tmpFile = $_FILES['load']['tmp_name'];
		$newFile = FormatHelper::formatUploadLink($id, $_FILES['load']['name']);
		$result = move_uploaded_file($tmpFile, $newFile);
		if (in_array($file->getExtension(), $pictures)) {
			$path = FormatHelper::formatUploadResizeLink($id, $_FILES['load']['name']);
			$resize = new \Uppu3\Helper\Resize;
			$resize->resizeFile($newFile, $path);	
		}
		if ($result) {
			$message = 'File was successfully uploaded';
		} else {
			$message = 'File failed to upload';
		}
		$app->redirect("/view/$id"); 
	} else {
		$flash = "You didn't select any file";
		$app->render('file_load.html', array('flash' => $flash));
	}
});

$app->get('/view/:id/', function($id) use ($app) {
	$file = $app->em->find('Uppu3\Resource\FileResource', $id);
	if (!$file) {
		$app->notFound();
	}
	$helper = new FormatHelper();
	$info = $file->getMediainfo();
	$pictures = array('image/jpeg','image/gif','image/png');
	$video = array('video/webm', 'video/mp4');
	$app->render('view.html', array('file' => $file,
									'info' => $info,
									'helper' => $helper));
// 	if (in_array($file->getExtension(), $pictures)){
// 	$app->render('view_image.html', array('file' => $file,
// 												'info' => $info,
// 											   'helper' => $helper));

// } elseif (in_array($file->getExtension(), $video)) {
// 	$app->render('view_video.html', array('file' => $file,
// 											   'info' => $info,
// 											   'helper' => $helper));
// } else {
// 	$app->render('view.html', array('file' => $file,
// 										 'helper' => $helper));
// }
});

$app->get('/download/:id', function($id) use ($app) {
	$file = $app->em->find('Uppu3\Resource\FileResource', $id);
	$name = FormatHelper::formatDownloadFile($id, $file->getName());
	if (file_exists($name)) {
		header("X-Sendfile:".realpath(dirname(__FILE__)).'/'.$name);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=".$file->getName());
		exit;
	} else {
		$app->notFound();
	}
});

$app->get('/list', function() use ($app) {
	$helper= new FormatHelper();
	$page = 'list';
	// $files = $app->em->getRepository('Uppu3\Resource\FileResource')->findAll();
	$files = $app->em
	->createQuery('SELECT g FROM Uppu3\Resource\FileResource g ORDER BY g.uploaded DESC')
	->getResult();
	$app->render('list.html', array('files' => $files,
										 'page' => $page,
										 'helper' => $helper));
});

$app->run();