<?php
require '../vendor/autoload.php';
require_once '../app/bootstrap.php';
use Uppu3\Helper\FormatHelper;
use Uppu3\Entity\Comment;
use Uppu3\Helper\CommentHelper;

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

$app->get('/test', function() use ($app) {
	$data = array(
		'login' => 'venom',
		'email' => 'rsyu@yandex.ru',
		'password' => '1234567'
		);
	$user = new \Uppu3\Resource\User;
	$user->userSave($data, $app->em);
	var_dump($user);die(); 

});

$app->get('/register', function() use ($app) {
	$app->render('register.html');
});

$app->post('/register', function() use ($app) {
	//$user = \Uppu3\Helper\UserHelper::userData($_POST);
	$validation = new \Uppu3\Helper\ValidationHelper;
	$validation->validateData($_POST);
	if (empty($validation->error)) {
		\Uppu3\Helper\UserHelper::userSave($_POST, $app->em);
		$app->redirect("/view/$id");
	} else {
		$app->render('register.html', array('errors' => $validation->error,
											'data' => $_POST));
	};
	

	//$app->redirect('/');
});

$app->post('/', function() use ($app) {
	if (file_exists($_FILES['load']['tmp_name'])) {
		$file = \Uppu3\Helper\FileHelper::fileSave($_FILES, $app->em);
		$id = $file->getId();
		$app->redirect("/view/$id"); 			
		
	} else {
		$flash = "You didn't select any file";
		$app->render('file_load.html', array('flash' => $flash));
	}
});

$app->get('/view/:id/', function($id) use ($app) {
	$file = $app->em->find('Uppu3\Entity\File', $id);
	if (!$file) {
		$app->notFound();
	}
	$helper = new FormatHelper();
	$comments = $app->em->getRepository('Uppu3\Entity\Comment')
	->findBy(array('fileId' => $id), array('path' => 'ASC'));
	$info = $file->getMediainfo();
	$app->render('view.html', array('file' => $file,
		'info' => $info,
		'helper' => $helper,
		'comments' => $comments));
});
$app->get('/comment/:id/', function($id) use ($app) {
	$comment = $app->em->find('Uppu3\Entity\Comment', $id);
	echo $comment->getComment();
});


$app->get('/download/:id/:name', function($id, $name) use ($app) {
	$file = $app->em->find('Uppu3\Entity\File', $id);
	$name = FormatHelper::formatDownloadFile($id, $file->getName());

	if (file_exists($name)) {
		header("X-Sendfile:".realpath(dirname(__FILE__)).'/'.$name);
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment");
		return;
	} else {
		
		$app->notFound();
	}
});

$app->get('/list', function() use ($app) {
	$helper= new FormatHelper();
	$page = 'list';
	$files = $app->em->getRepository('Uppu3\Entity\File')
	->findBy([],['uploaded' => 'DESC'],50,0);
	$app->render('list.html', array('files' => $files,
		'page' => $page,
		'helper' => $helper));
});

$app->post('/send/:id', function($id) use ($app) {
	$parent = isset($_POST['parent']) ?  $app->em->find('Uppu3\Entity\Comment', $_POST['parent']) : false;
	$file = $app->em->find('Uppu3\Entity\File', $id);
	CommentHelper::saveComment($_POST, $app->em, $parent, $file);
	$app->redirect("/view/$id");
});

$app->run();