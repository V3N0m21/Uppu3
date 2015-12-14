<?php
namespace Uppu3\Helper;

class HashGenerator
{

    public static function generateSalt($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
        $charactersLength = strlen($characters);
        $salt = '';
        for ($i = 0; $i < $length; $i++) {
            $salt .= $characters[rand(0, $charactersLength - 1)];
        }
        return $salt;
    }

    public static function generateHash($password, $salt)
    {
        return sha1($salt . $password);
    }
}
