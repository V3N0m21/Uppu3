<?php
namespace Uppu3\Helper;

use Uppu3\Entity\File;


class FileHelper
{

    private $em;

    private $maxSize = 10485760;
    private $pictures = array('image/jpeg', 'image/gif', 'image/png');
    public $errors;

    function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    public function fileValidate($data)
    {
        if (($data['load']['size'] >= $this->maxSize) || ($data['load']['size'] == 0)) {
            $this->errors[] = 'Файл должен быть до 10мб.';
        }
    }

    public function fileSave($data, \Uppu3\Entity\User $user)
    {
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
        $fileResource->setUploadedBy($user);

        $this->em->persist($fileResource);
        $this->em->flush();
        $id = $fileResource->getId();
        $tmpFile = $data['load']['tmp_name'];
        $newFile = \Uppu3\Helper\FormatHelper::formatUploadLink($id, $data['load']['name']);
        $result = move_uploaded_file($tmpFile, $newFile);


        if (in_array($fileResource->getExtension(), $this->pictures)) {
            $path = \Uppu3\Helper\FormatHelper::formatUploadResizeLink($id, $data['load']['name']);
            $resize = new \Uppu3\Helper\Resize;
            $resize->resizeFile($newFile, $path);
        }

        return $fileResource;
    }
    public function fileDelete($id) {
        $file = $this->em->getRepository('Uppu3\Entity\File')->findOneById($id);
        $filePath = \Uppu3\Helper\FormatHelper::formatUploadLink($file->getId(), $file->getName());
        if (in_array($file->getExtension(), $this->pictures)) {
            $fileResizePath = \Uppu3\Helper\FormatHelper::formatUploadResizeLink($file->getId(), $file->getName());
            unlink($fileResizePath);
        }
        unlink($filePath);
        $this->em->remove($file);
        $this->em->flush();
        echo "all is done"; die();

    }
}
