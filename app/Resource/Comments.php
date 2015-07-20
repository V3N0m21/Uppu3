<?php namespace Uppu3\Resource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
* @ORM\Entity @ORM\Table(name="comments")
* @Gedmo\Tree(type="materializedPath")
*/
class Comments {
	/** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue */
	protected $id;
	/** 
	* @ORM\Column(type="string")
	* @Gedmo\TreePath
	*/
	private $path;
	/**
     * @Gedmo\TreePathSource
     * @ORM\Column(name="user", type="string", length=64)
     */
    private $user;
    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Comments", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $parent;
    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="level", type="integer", nullable=true)
     */
    private $level;

    /**
     * @ORM\OneToMany(targetEntity="Comments", mappedBy="parent")
     */
    private $children;

	
	/** @ORM\Column(type="string") */
	protected $comment;
	/** @ORM\Column(type="datetime") */
	protected $posted;
	/** @ORM\Column(type="integer") */
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


	public function setFileId($id)
	{
		$this->fileId = $id;
	}


        public function setParent(Comments $parent = null)
    {
        $this->parent = $parent;
    }

    public function getParent()
    {
        return $this->parent;
    }

     public function setPath($path)
    {
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getLevel()
    {
        return $this->level;
    }

	public function getFileId()
	{
		return $this->fileId;
	}

	static public function saveComment($post, $em, self $parent = null)
	{
		// $parent = new self;
		// $parent->setUser('parent');
		// $parent->setTitle('comment');
		// $parent->setComment('comment');
		// $parent->setFileId($post['fileId']);
		// $parent->setPosted();
		// $em->persist($parent);
		// $em->flush();


		$comment = new self;
		$comment->setUser($post['name']);
		$comment->setComment($post['comment']);
		$comment->setParent($parent);
		$comment->setFileId($post['fileId']);
		$comment->setPosted();
		$em->persist($comment);
		$em->flush();
	}

}