<?php
namespace Uppu3\Helper;

class LoginHelper
{
    protected $app;
	protected $em;
    public $logged = null;

	public function __construct($app)
	{
        $this->app = $app;
	 	$this->em = $app->em;
        return $this->checkAuthorization();
	}

	protected function getUser($cookie)
	{
		$user = $this->em->getRepository('Uppu3\Entity\User')->findOneBy(array('salt' => $cookie));

        return $user;
	}

    public function authenticateUser(\Uppu3\Entity\User $user)
    {
        setcookie('id', $user->getId(), time() + 3600 *24*7);
        setcookie('hash', $user->getHash(), time() + 3600 *24*7);
    }

    private function checkAuthorization()
    {
        if (!isset($_COOKIE['id']) or !isset($_COOKIE['hash'])) {
            return null;
        } else {
            $id = intval($_COOKIE['id']);
            $hash = strval($_COOKIE['hash']);
            $user = $this->em->getRepository('Uppu3\Entity\User')->findOneById($id);
            if ($user->getHash() != $hash) return null;
            $this->logged = true;
    }
    }
    public function checkUser() {
        $cookie = $this->app->getCookie('token');
        if ($cookie == '') {
            $cookie = HashGenerator::generateSalt();
            $this->app->setCookie('token', $cookie, '1 month');
        }
        $user = $this->app->em->getRepository('Uppu3\Entity\User')->findOneBy(array('token' => $cookie));
        if (!$user) {
        $salt = HashGenerator::generateSalt();
        $user = \Uppu3\Helper\UserHelper::saveAnonymousUser($salt, $this->app->em, $cookie);
}
        return $user;
    }

    public function getCurrentUser() {
        if ($this->logged) {
            $id = intval($_COOKIE['id']);
            $user = $this->em->getRepository('Uppu3\Entity\User')->findOneById($id);
            return $user;
        }
    }


    public function logout()
    {
        setcookie('id', '');
        setcookie('hash', '');
    }

}