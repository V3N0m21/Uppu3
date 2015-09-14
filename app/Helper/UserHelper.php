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

	static public function userSave($data, $em)
	{
        $userResource = new User;
        $userResource->setLogin($data['login']);
        $userResource->setEmail($data['email']);
        $userResource->setCreated();
        $salt = HashGenerator::generateSalt();
        $userResource->setSalt($salt);
        $password = HashGenerator::generateHash($data['password'], $salt);
        $userResource->setPassword($password);
        $em->persist($user);
        $em->flush();
	}
}