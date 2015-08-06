<?php namespace Uppu3\Resource;
use Uppu3\Helper\HashGenerator as HashGenerator;

class User {

	static public function userSave($data, $em)
	{
		$userResource = new UserResource;
        $userResource->setLogin($data['login']);
        $userResource->setEmail($data['email']);
        $userResource->setCreated();
        $salt = HashGenerator::generateSalt();
        $userResource->setSalt($salt);
        $password = HashGenerator::generateHash($data['password'], $salt);
        $userResource->setPassword($password);
        $em->persist($userResource);
        $em->flush();
	}
}