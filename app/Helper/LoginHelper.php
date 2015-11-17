<?php
namespace Uppu3\Helper;

class LoginHelper
{
	protected $em;
	public $user = null;
    public $login;
    public $isAuthorized = false;

	public function __construct()
	{
		if ($_COOKIE['user']) {
            $this->isAuthorized = true;
        }
	}

	protected function getUser($cookie)
	{
		$user = $this->em->getRepository('Uppu3\Entity\User')->findOneBy(array('salt' => $cookie));
        
        return $user;
	}

    public function authenticateUser(\Uppu3\Entity\User $user)
    {
        setcookie('user', $user->getLogin(), time() + 3600 *24*7);
        $this->isAuthorized = true;
    }

    public function logout()
    {
        setcookie('user', '');
    }

}