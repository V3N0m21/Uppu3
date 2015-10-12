<?php namespace Uppu3\Helper;
use Uppu3\Helper\HashGenerator;
use Uppu3\Entity\User;

class UserHelper {

        // static public function userData($data) {
        // $userResource = new User;
        // $userResource->setLogin($data['login']);
        // $userResource->setEmail($data['email']);
        // $userResource->setCreated();
        // $salt = HashGenerator::generateSalt();
        // $userResource->setSalt($salt);
        // $password = HashGenerator::generateHash($data['password'], $salt);
        // $userResource->setPassword($password);
        // return $userResource;
        // }

	static public function userSave($data, $cookie, $em)
	{
        $userResource = $em->getRepository('Uppu3\Entity\User')->findOneBy(array('salt' => $cookie));
        if (!$userResource) {
            $userResource = self::saveAnonymousUser($cookie, $em);
        }
        $userResource->setLogin($data['login']);
        $userResource->setEmail($data['email']);
        $userResource->setCreatedNow();
        $hash = HashGenerator::generateHash($data['password'], $cookie);
        $userResource->setHash($hash);
        $em->persist($userResource);
        $em->flush();
        return $userResource;
	}

    static public function saveAnonymousUser($salt, $em)
    {
        $userModel = new User;
        $userModel->setSalt($salt);
        $userModel->setLogin('Anonymous');
        $em->persist($userModel);
        $em->flush();
        return $userModel;
    }
}