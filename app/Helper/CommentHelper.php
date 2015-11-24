<?php
namespace Uppu3\Helper;
use Uppu3\Entity\Comment;

class CommentHelper
{
    static public function saveComment($post, $em, Comment $parent = null, \Uppu3\Entity\File $file, \Uppu3\Entity\User $user) {
        
        $comment = new Comment;
        $comment->setUser($user);
        $comment->setComment($post['comment']);
        $comment->setParent($parent);
        $comment->setFileId($file);
        $comment->setPosted();
        $em->persist($comment);
        $em->flush();
    }
}
