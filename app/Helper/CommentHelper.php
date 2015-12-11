<?php
namespace Uppu3\Helper;

use Uppu3\Entity\Comment;

class CommentHelper
{
    private $em;
    public $comment;

    function __construct($data, \Doctrine\ORM\EntityManager $em, Comment $parent = null, \Uppu3\Entity\File $file, \Uppu3\Entity\User $user)
    {
        $this->comment = new Comment;
        $this->comment->setUser($user);
        $this->comment->setComment($data['comment']);
        $this->comment->setParent($parent);
        $this->comment->setFileId($file);
        $this->comment->setPosted();
        $this->em = $em;
    }

    public function commentSave()
    {
        $this->em->persist($this->comment);
        $this->em->flush();

    }


}
