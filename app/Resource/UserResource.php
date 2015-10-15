<?php
namespace Uppu3\Resource;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity @ORM\Table(name="users") */

class UserResource
{
    
    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
    protected $id;
    
    /** @ORM\Column(type="string") */
    protected $login;
    
    /** @ORM\Column(type="string") */
    protected $email;
    
    /** @ORM\Column(type="datetime") */
    protected $created;
    
    /** @ORM\Column(type="string") */
    protected $salt;
    
    /** @ORM\Column(type="string") */
    protected $password;
    
    public function getId() {
        return $this->id;
    }
    
    public function getLogin() {
        return $this->login;
    }
    
    public function setLogin($login) {
        $this->login = $login;
    }
    
    public function getEmail() {
        return $this->email;
    }
    
    public function setEmail($email) {
        $this->email = $email;
    }
    
    public function getCreated() {
        return $this->created;
    }
    
    public function setCreated() {
        $this->created = new \DateTime("now");
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    public function setPassword($password) {
        $this->password = $password;
    }
    
    public function getSalt() {
        return $this->salt;
    }
    
    public function setSalt($salt) {
        $this->salt = $salt;
    }
}
