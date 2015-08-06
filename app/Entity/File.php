<?php namespace Uppu3\Entity;
use Doctrine\ORM\Mapping as ORM;
/** @ORM\Entity @ORM\Table(name="files") */


class File {
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue
	* @ORM\ManyToMany(targetEntity="Comments", inversedBy="fileId")
	  */
	protected $id;
	/** @ORM\Column(type="string") */
	protected $name;
	/** @ORM\Column(type="integer") */
	protected $size;
	/** @ORM\Column(type="datetime") */
	protected $uploaded;
	/** @ORM\Column(type="string") */
	protected $comment;
	/** @ORM\Column(type="string") */
	protected $extension;
	/** @ORM\Column(type="mediainfotype") */
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
		return $this->mediainfo;
	}
}