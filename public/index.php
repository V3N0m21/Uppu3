<?php
require '../vendor/autoload.php';
require_once '../app/bootstrap.php';
use Uppu3\Helper\FormatHelper as FormatHelper;
use Uppu3\Resource\Comments as Comments;

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
		$file = \Uppu3\Resource\File::fileSave($_FILES, $app->em);
		$id = $file->getId();
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
	$comments = $app->em->getRepository('Uppu3\Resource\Comments')
	->findBy(array('fileId' => $id), array('posted' => 'DESC'));
	$info = $file->getMediainfo();
	$app->render('view.html', array('file' => $file,
		'info' => $info,
		'helper' => $helper,
		'comments' => $comments));
});
$app->get('/comment/:id/', function($id) use ($app) {
	$comment = $app->em->find('Uppu3\Resource\Comments', $id);
	echo $comment->getComment();
});


$app->get('/download/:id/:name', function($id, $name) use ($app) {
	$file = $app->em->find('Uppu3\Resource\FileResource', $id);
	$name = FormatHelper::formatDownloadFile($id, $file->getName());

	if (file_exists($name)) {
		header("X-Sendfile:".realpath(dirname(__FILE__)).'/'.$name);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment");
		exit;
	} else {
		
		$app->notFound();
	}
});

$app->get('/list', function() use ($app) {
	$helper= new FormatHelper();
	$page = 'list';
	$files = $app->em->getRepository('Uppu3\Resource\FileResource')
	->findBy([],['uploaded' => 'DESC'],50,0);
	$app->render('list.html', array('files' => $files,
		'page' => $page,
		'helper' => $helper));
});

$app->get('/test', function() use ($app) {

	$comment = new Comments();
	$comment->setUser('Food');
	$comment->setComment('test');
	$comment->setPosted();
	$app->em->persist($comment);
	$app->em->flush();
	echo $comment->getId();
});

$app->post('/send/:id', function($id) use ($app) {
	// var_dump($_POST);die();
	isset($_POST['parent']) ? $parentID = $_POST['parent'] : $parentID = false;

	// var_dump($parentID);die();
	// $parent = $app->em->find('Uppu3\Resource\Comments', $parentID);
	$parent = $app->em->find('Uppu3\Resource\Comments', $parentID);
	// $parent = null;
	Comments::saveComment($_POST, $app->em, $parent);
	$app->redirect("/view/$id");
});

$app->run();