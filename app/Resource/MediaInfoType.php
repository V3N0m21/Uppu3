<?php namespace Uppu3\Resource;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class MediaInfoType extends Type
{
    const MEDIAINFOTYPE = 'mediainfotype';

     public function convertToPHPValue($info)
    {
        
        $info = json_decode($info);
        $mediaInfo = new MediaInfo;
        $mediaInfo = $mediaInfo->setMediaInfo($mediaInfo, $info);
        return $mediaInfo;
    }

    public function convertToDatabaseValue(MediaInfo $mediaInfo)
    {
        $mediaInfo = json_encode($mediaInfo);
        return $mediaInfo;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        // return the SQL used to create your column type. To create a portable column type, use the $platform.
    }

    public function getName()
    {
        return self::MEDIAINFOTYPE;
    }
}