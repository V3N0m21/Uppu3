<?php
namespace Uppu3\Helper;
use Uppu3\Helper\HashGenerator;
use Uppu3\Entity\User;

class UserHelper
{
    

    private $em;
    public $user;
    function __construct($data,\Doctrine\ORM\EntityManager $em, $cookie) {
        $this->em = $em;
        $userResource = $em->getRepository('Uppu3\Entity\User')->findOneBy(array('salt' => $cookie));
        if (!$userResource) {
        $this->user = new User;
        } else {
        $this->user = $userResource;
        }
        $this->user->setLogin($data['login']);
        $this->user->setEmail($data['email']);
        return $this->user;
    }

    public function userSave($password, $cookie, $em) {
        $this->user->setCreatedNow();
        $this->user->setSalt($cookie);
        $hash = HashGenerator::generateHash($password, $cookie);
        $this->user->setHash($hash);
        $em->persist($this->user);
        $em->flush();
        return $this->user;
    }
    
    static public function saveAnonymousUser($salt, $em) {
        $userModel = new User;
        $userModel->setSalt($salt);
        $userModel->setLogin('Anonymous');
        $em->persist($userModel);
        $em->flush();
        return $userModel;
    }
    
}
