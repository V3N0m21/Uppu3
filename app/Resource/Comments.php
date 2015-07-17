<?php namespace Uppu3\Resource;

/**
* @Entity @Table(name="comments")
*/
class Comments {
	/** @Id @Column(type="integer") @GeneratedValue */
	protected $id;
	/** @Column(type="string") */
	protected $user;
	/** @Column(type="string") */
	protected $comment;
	/** @Column(type="datetime") */
	protected $posted;
	/** @Column(type="integer") */
	protected $parentId;
	/** @Column(type="integer") */
	protected $fileId;

	public function getId() 
	{
		return $this->id;
	}

	public function getUser()
	{
		return $this->user;
	}

	public function setUser($user)
	{
		$this->user = $user;
	}

	public function getComment()
	{
		return $this->comment;
	}

	public function setComment($comment)
	{
		$this->comment = $comment;
	}

	public function getPosted()
	{
		return $this->posted;
	}

	public function setPosted()
	{
		$this->posted = new \DateTime("now");
	}

	public function setParentId($id)
	{
		$this->parentId = $id;
	}

	public function getParentId()
	{
		return $this->parentId;
	}

	public function setFileId($id)
	{
		$this->fileId = $id;
	}

	public function getFileId()
	{
		return $this->fileId;
	}

	static public function saveComment($post, $em)
	{
		$comment = new self;
		$comment->setUser($post['name']);
		$comment->setComment($post['comment']);
		$comment->setFileId($post['fileId']);
		$comment->setPosted();
		$em->persist($comment);
		$em->flush();
	}

}