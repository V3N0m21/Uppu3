<?php
namespace Uppu3\Helper;

class DataValidator
{
    public $error;

    public function validateUser(\Uppu3\Entity\User $user, $data)
    {
        $this->checkLogin($user->getLogin());
        $this->checkPassword($data['password'], $data['confirmation']);
        $this->checkEmail($user->getEmail());
    }

    public function validateComment(\Uppu3\Entity\Comment $comment)
    {
        $this->checkComment($comment->getComment());
    }

    public function hasErrors()
    {
        (!empty($this->error)) ? true : false;
    }

    private function checkComment($comment)
    {
        if ($this->notEmpty($comment)) {
            return true;
        }
        return $this->error['comment'] = "Comment should not be empty";
    }

    private function checkLogin($login)
    {
        if ($this->notEmpty($login)) {
            return true;
        }
        return $this->error['login'] = "Login should be filled";
    }

    private function checkEmail($email)
    {
        if ($this->notEmpty($email)) {
            if ($this->isEmail($email)) {
                return true;
            } else {
                return $this->error['email'] = 'Email isn\'t valid';
            }
        }
        return $this->error['email'] = "Email must be filled.";
    }

    private function checkPassword($password, $confirmation)
    {
        if ($this->notEmpty($password) && $this->notEmpty($confirmation)) {
            if ($password !== $confirmation) {
                return $this->error['password'] = 'Password and confirmation doesn\'t match.';
            }
            return true;
        }
        return $this->error['password'] = 'Password must be present';
    }

    private function notEmpty($field)
    {
        if (empty($field)) {
            return false;
        }
        return true;
    }

    private function isEmail($field)
    {
        $regExp = "/.+@.+\..+/i";
        if (!preg_match($regExp, $field)) {
            return false;
        }
        return true;
    }
}
