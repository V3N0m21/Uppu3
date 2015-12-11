<?php
namespace Uppu3\Helper;
use Uppu3\Entity\File;
define('MAX_SIZE', 10485760 ); //10485760
class FileHelper
{

    private $em;
    private $user;
    public $errors;

    function __construct(\Uppu3\Entity\User $user, \Doctrine\ORM\EntityManager $em)
    {
        $this->user = $user;
        $this->em = $em;
    }

    public function fileValidate($data) {
        if(($data['load']['size'] >= MAX_SIZE) || ($data['load']['size'] == 0)) {
            $this->errors[] = 'Файл должен быть до 10мб.';
        }
    }

    public function fileSave($data) {
        $pictures = array('image/jpeg', 'image/gif', 'image/png');
        $fileResource = new File;

        
        //$fileResource->saveFile($data['load']);
        
        $fileResource->setName($data['load']['name']);
        $fileResource->setSize($data['load']['size']);
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $fileResource->setExtension($finfo->file($data['load']['tmp_name']));
        
        //$fileResource->setMediainfo($data['load']['tmp_name']);
        $fileResource->setComment($_POST['comment']);
        $mediainfo = \Uppu3\Entity\MediaInfo::getMediaInfo($data['load']['tmp_name']);
        
        //$mediainfo = json_encode($mediainfo);
        $fileResource->setMediainfo($mediainfo);
        $fileResource->setUploaded();
        $fileResource->setUploadedBy($this->user);
        
        $this->em->persist($fileResource);
        $this->em->flush();
        $id = $fileResource->getId();
        $tmpFile = $data['load']['tmp_name'];
        $newFile = \Uppu3\Helper\FormatHelper::formatUploadLink($id, $data['load']['name']);
        $result = move_uploaded_file($tmpFile, $newFile);
        
        
        
        if (in_array($fileResource->getExtension(), $pictures)) {
            $path = \Uppu3\Helper\FormatHelper::formatUploadResizeLink($id, $data['load']['name']);
            $resize = new \Uppu3\Helper\Resize;
            $resize->resizeFile($newFile, $path);
        }
        
        return $fileResource;
    }
}
