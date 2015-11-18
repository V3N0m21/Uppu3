<?php
require '../vendor/autoload.php';
require_once '../app/bootstrap.php';
use Uppu3\Helper\FormatHelper;
use Uppu3\Entity\Comment;
use Uppu3\Helper\CommentHelper;
use Uppu3\Helper\HashGenerator;
use Uppu3\Helper\LoginHelper;

$app = new \Slim\Slim(array('view' => new \Slim\Views\Twig(), 'templates.path' => '../app/templates'));

$app->container->singleton('em', function () use ($entityManager) {

    return $entityManager;
});

$app->container->singleton('loginHelper', function() use($app) {
    return new LoginHelper($app->em);
});

$app->view->appendData( array(
    'loginHelper' => $app->loginHelper
    ));

function checkAuthorization() {
    $app = \Slim\Slim::getInstance();
    $isLogged = $app->loginHelper;
    if ($isLogged->logged != true) {
        
        $app->flash('error', 'Login required');
        $app->redirect('/login');
    }
};

$app->get('/', function () use ($app) {
    $page = 'index';
    $flash = '';
    $app->render('file_load.html', array('page' => $page, 'flash' => $flash));
});

$app->get('/login', function () use ($app) {
    $page = 'login';
    $flash = '';
    $app->render('login_form.html', array('page' => $page, 'flash' => $flash));
});

$app->post('/login', function () use ($app) {
    if ($user = $app->em->getRepository('Uppu3\Entity\User')
        ->findOneBy(array('login' => $_POST['login']))) {
        if($user->getHash() === HashGenerator::generateHash($_POST['password'], $user->getSalt()))
        {
            $id = $user->getId();
            $app->loginHelper->authenticateUser($user);
            $app->redirect("users/$id");
        } else {
            $error = "Invalid login or password";   
            $app->render('login_form.html', array('flash' => $error, 'data' => $_POST));
            return;                   
        }

    } 
    $error = "Invalid login or password";   
    $app->render('login_form.html', array('flash' => $error, 'data' => $_POST));
}); 

$app->get('/logout', function () use ($app) {
    $app->loginHelper->logout();
    $app->redirect('/');
});              

$app->get('/register', function () use ($app) {
    $app->render('register.html');
});

$app->post('/register', function () use ($app) {

    $cookie = $app->getCookie('salt');
    if (!$cookie) {
        $cookie = HashGenerator::generateSalt();
        $app->setCookie('salt', $cookie, '1 month');
    }
    $validation = new \Uppu3\Helper\UserValidator;
    $validation->validateData($_POST);
    if (!$validation->hasErrors()) {
        $user = \Uppu3\Helper\UserHelper::userSave($_POST, $cookie, $app->em);
        $id = $user->getId();
        $app->loginHelper->authenticateUser($user);
        $app->redirect("users/$id");
    } 
    else {
        $app->render('register.html', array('errors' => $validation->error, 'data' => $_POST));
    };
});

$app->post('/', function () use ($app) {
    if (file_exists($_FILES['load']['tmp_name'])) {
        $cookie = $app->getCookie('salt');
        if (!$cookie) {
            $cookie = HashGenerator::generateSalt();
            $app->setCookie('salt', $cookie, '1 month');
        }
        $user = $app->em->getRepository('Uppu3\Entity\User')->findOneBy(array('salt' => $cookie));
        if (!$user) {
            $user = \Uppu3\Helper\UserHelper::saveAnonymousUser($cookie, $app->em);
        }
        $file = \Uppu3\Helper\FileHelper::fileSave($_FILES, $user, $app->em);
        $id = $file->getId();
        $app->redirect("/view/$id");
    } 
    else {
        $flash = "Вы не выбрали файл";
        $app->render('file_load.html', array('flash' => $flash));
    }
});

$app->get('/view/:id/', function ($id) use ($app) {
    $file = $app->em->find('Uppu3\Entity\File', $id);
    $user = $app->em->getRepository('Uppu3\Entity\User')->findOneById($file->getUploadedBy());
    if (!$file) {
        $app->notFound();
    }
    $helper = new FormatHelper();
    $comments = $app->em->getRepository('Uppu3\Entity\Comment')->findBy(array('fileId' => $id), array('path' => 'ASC'));
    $info = $file->getMediainfo();
    $app->render('view.html', array('file' => $file, 'user' => $user, 'info' => $info, 'helper' => $helper, 'comments' => $comments));
});

$app->get('/comment/:id/', function ($id) use ($app) {
    $comment = $app->em->find('Uppu3\Entity\Comment', $id);
    echo $comment->getComment();
});

$app->get('/download/:id/:name', function ($id, $name) use ($app) {
    $file = $app->em->find('Uppu3\Entity\File', $id);
    $name = FormatHelper::formatDownloadFile($id, $file->getName());
    
    if (file_exists($name)) {
        header("X-Sendfile:" . realpath(dirname(__FILE__)) . '/' . $name);
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment");
        return;
    } 
    else {
        $app->notFound();
    }
});

$app->get('/users/', 'checkAuthorization', function () use ($app) {
    $cookie = $app->getCookie('salt');
    $page = 'users';
    $users = $app->em->getRepository('Uppu3\Entity\User')->findBy([], ['created' => 'DESC']);
    $filesCount = [];
    foreach ($users as $user) {
        $filesCount[$user->getId() ] = count($app->em->getRepository('Uppu3\Entity\File')
                                             ->findByUploadedBy($user->getId()));
    }
    $app->render('users.html', array('users' => $users, 'page' => $page, 'cookie' => $cookie, 'filesCount' => $filesCount));
});

$app->get('/users/:id/', function ($id) use ($app) {
    $helper = new FormatHelper();
    $page = 'users';
    $user = $app->em->getRepository('Uppu3\Entity\User')->findOneById($id);
    $files = $app->em->getRepository('Uppu3\Entity\File')->findByUploadedBy($id);
    $app->render('user.html', array('user' => $user, 'files' => $files, 'helper' => $helper, 'page' => $page));
});

$app->get('/list', 'checkAuthorization', function () use ($app) {
    $helper = new FormatHelper();
    $page = 'list';
    $files = $app->em->getRepository('Uppu3\Entity\File')->findBy([], ['uploaded' => 'DESC'], 50, 0);
    $users = [];
    foreach ($files as $file) {
        $id = $file->getUploadedBy();
        $user = $app->em->getRepository('Uppu3\Entity\User')->findOneById($id);
        $users[$user->getId() ] = $user->getLogin();
    };
    $app->render('list.html', array('files' => $files, 'users' => $users, 'page' => $page, 'helper' => $helper));
});

$app->post('/send/:id', function ($id) use ($app) {
    $parent = isset($_POST['parent']) ? $app->em->find('Uppu3\Entity\Comment', $_POST['parent']) : null;
    $file = $app->em->find('Uppu3\Entity\File', $id);
    CommentHelper::saveComment($_POST, $app->em, $parent, $file);
    $comments = $app->em->getRepository('Uppu3\Entity\Comment')->findBy(array('fileId' => $id), array('path' => 'ASC'));
    $app->render('comments.html', array('comments' => $comments));
});

$app->post('/ajaxComments/:id', function ($id) use ($app) {
    $comments = $app->em->getRepository('Uppu3\Entity\Comment')->findBy(array('fileId' => $id), array('path' => 'ASC'));
    $app->render('comments.html', array('comments' => $comments));
});


$app->run();
