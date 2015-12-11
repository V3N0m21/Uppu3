<?php
namespace Uppu3\Entity;

use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity @ORM\Table(name="files") */
class File
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
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

    /** @ORM\OneToMany(targetEntity="Comment", mappedBy="fileId") */
    protected $comments;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="uploadedBy", referencedColumnName="id")
     *
     */
    protected $uploadedBy;

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

    public function getUploadedBy()
    {
        return $this->uploadedBy;
    }

    public function getUploader()
    {

    }

    public function setUploadedBy(\Uppu3\Entity\User $user)
    {
        $this->uploadedBy = $user;
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
