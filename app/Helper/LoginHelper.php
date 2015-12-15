<?php
namespace Uppu3\Helper;

class LoginHelper
{

    protected $em;
    protected $requestCookies;
    protected $responseCookies;
    public $logged = null;

    public function __construct($em, $requestCookies, $responseCookies)
    {
        $this->em = $em;
        $this->requestCookies = $requestCookies;
        $this->responseCookies = $responseCookies;
        $this->checkAuthorization();
    }

    protected function getUser($cookie)
    {
        $user = $this->em->getRepository('Uppu3\Entity\User')->findOneBy(array('salt' => $cookie));

        return $user;
    }

    public function authenticateUser(\Uppu3\Entity\User $user)
    {
        $this->responseCookies->set('id', $user->getId(), time() + 3600 * 24 * 7);
        $this->responseCookies->set('hash', $user->getHash(), time() + 3600 * 24 * 7);
    }

    private function checkAuthorization()
    {

        if ($this->requestCookies['id'] == '' || $this->requestCookies == '') {
            return null;
        } else {
            $id = intval($this->requestCookies['id']);
            $hash = strval($this->requestCookies['hash']);
            $user = $this->em->getRepository('Uppu3\Entity\User')->findOneById($id);
            if ($user->getHash() != $hash) return null;
            $this->logged = true;
        }
    }

    public function checkUserRegistered()
    {
        $cookie = $this->requestCookies['token'];
        if ($cookie == '') {
            $cookie = HashGenerator::generateSalt();
            $this->responseCookies->set('token', $cookie, '1 month');
        }
        $user = $this->em->getRepository('Uppu3\Entity\User')->findOneBy(array('token' => $cookie));
        if (!$user) {
            $salt = HashGenerator::generateSalt();
            $user = \Uppu3\Helper\UserHelper::saveAnonymousUser($salt, $this->em, $cookie);
        }
        return $user;
    }

    public function getCurrentUser()
    {
        if ($this->logged) {

            $token = $this->requestCookies['token'];
            $user = $this->em->getRepository('Uppu3\Entity\User')->findOneByToken($token);
            return $user;
        }
    }


    public function logout()
    {
        $this->responseCookies->set('id', '');
        $this->responseCookies->set('hash', '');
    }

}