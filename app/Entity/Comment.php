<?php
namespace Uppu3\Entity;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity @ORM\Table(name="comments")
 * @Gedmo\Tree(type="materializedPath")
 */
class Comment
{
    
    /**
     *@ORM\Id @ORM\Column(type="integer")
     *@ORM\GeneratedValue
     *@Gedmo\TreePathSource
     */
    protected $id;
    
    /** 
     * @ORM\Column(type="string")
     * @Gedmo\TreePath
     */
    private $path;
    
    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     *
     */
    private $user;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Comment", inversedBy="children")
     *
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $parent;
    
    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="level", type="integer", nullable=true)
     */
    private $level;
    
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="parent")
     *
     */
    private $children;
    
    /** @ORM\Column(type="string") */
    protected $comment;
    
    /** @ORM\Column(type="datetime") */
    protected $posted;
    
    /**
     * @ORM\ManyToOne(targetEntity="File")
     * @ORM\JoinColumn(name="fileId", referencedColumnName="id")
     *
     */
    protected $fileId;
    
    public function getId() {
        return $this->id;
    }
    
    public function getUser() {
        return $this->user;
    }

    public function setUser(\Uppu3\Entity\User $user) {
        $this->user = $user;
    }
    
    public function getComment() {
        return $this->comment;
    }
    
    public function setComment($comment) {
        $this->comment = $comment;
    }
    
    public function getPosted() {
        return $this->posted;
    }
    
    public function setPosted() {
        $this->posted = new \DateTime("now");
    }
    
    public function setFileId(\Uppu3\Entity\File $file) {
        $this->fileId = $file;
    }
    
    public function setParent(Comment $parent = null) {
        $this->parent = $parent;
    }
    
    public function getParent() {
        return $this->parent;
    }
    
    public function setPath($path) {
        $this->path = $path;
    }
    
    public function getPath() {
        return $this->path;
    }
    
    public function getLevel() {
        return $this->level;
    }
    
    public function getFileId() {
        return $this->fileId;
    }
}
