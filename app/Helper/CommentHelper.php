<?php namespace Uppu3\Helper;
use Uppu3\Entity\Comment;

class CommentHelper {
	static public function saveComment($post, $em, Comment $parent = null, \Uppu3\Entity\File $file)
	{
		
		$comment = new Comment;
		$comment->setUser($post['name']);
		$comment->setComment($post['comment']);
		$comment->setParent($parent);
		$comment->setFileId($file);
		$comment->setPosted();
		$em->persist($comment);
		$em->flush();
	}

}