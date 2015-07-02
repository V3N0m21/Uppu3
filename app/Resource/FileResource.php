<?php namespace Uppu3\Resource;
/**
* @Entity @Table(name="files")
*/


class FileResource {
	/** @Id @Column(type="integer") @GeneratedValue */
	protected $id;
	/** @Column(type="string") */
	protected $name;
	/** @Column(type="integer") */
	protected $size;
	/** @Column(type="datetime") */
	protected $uploaded;
	/** @Column(type="string") */
	protected $comment;
	/** @Column(type="string") */
	protected $extension;
	/** @Column(type="text") */
	protected $mediainfo;

	public function getId() 
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function getSize()
	{
		return $this->size;
	}

	public function setSize($size)
	{
		$this->size = $size;
	}

	public function getUploaded()
	{
		return $this->uploaded;
	}

	public function setUploaded()
	{
		$this->uploaded = new \DateTime("now");
	}

	public function getComment()
	{
		return $this->comment;
	}

	public function setComment($comment)
	{
		$this->comment = $comment;
	}

	public function getExtension()
	{
		return $this->extension;
	}

	public function setExtension($extension)
	{
		$this->extension = $extension;
	}

	public function setMediainfo($mediainfo)
	{
		$this->mediainfo = $mediainfo;
	}

	public function getMediaInfo()
	{
		return json_decode($this->mediainfo);
	}

}