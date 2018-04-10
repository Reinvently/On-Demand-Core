<?php
/**
 * @copyright Reinvently (c) 2018
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

namespace reinvently\ondemand\core\modules\resource\components;


use himiklab\thumbnail\FileNotFoundException;
use Imagine\Image\Box;
use Yii;
use yii\helpers\FileHelper;
use yii\imagine\Image;

class EasyThumbnailImage extends \himiklab\thumbnail\EasyThumbnailImage
{
    /**
     * Creates and caches the image thumbnail and returns full path from thumbnail file.
     *
     * @param string $filename
     * @param integer $width
     * @param integer $height
     * @param string $mode
     * @param string $thumbnailFileExt
     * @return string
     * @throws FileNotFoundException
     */
    public static function thumbnailFile($filename, $width, $height, $mode = self::THUMBNAIL_OUTBOUND, $thumbnailFileExt = '')
    {
        $filename = FileHelper::normalizePath(Yii::getAlias($filename));
        if (!is_file($filename)) {
            throw new FileNotFoundException("File $filename doesn't exist");
        }
        $cachePath = Yii::getAlias('@webroot/' . self::$cacheAlias);

        if (!$thumbnailFileExt) {
            $thumbnailFileExt = strrchr($filename, '.');
        }
        $thumbnailFileName = md5($filename . $width . $height . $mode . filemtime($filename));
        $thumbnailFilePath = $cachePath . DIRECTORY_SEPARATOR . substr($thumbnailFileName, 0, 2);
        $thumbnailFile = $thumbnailFilePath . DIRECTORY_SEPARATOR . $thumbnailFileName . $thumbnailFileExt;

        if (file_exists($thumbnailFile)) {
            if (self::$cacheExpire !== 0 && (time() - filemtime($thumbnailFile)) > self::$cacheExpire) {
                unlink($thumbnailFile);
            } else {
                return $thumbnailFile;
            }
        }
        if (!is_dir($thumbnailFilePath)) {
            mkdir($thumbnailFilePath, 0755, true);
        }

        $box = new Box($width, $height);
        $image = Image::getImagine()->open($filename);
        $image = $image->thumbnail($box, $mode);

        $image->save($thumbnailFile);
        return $thumbnailFile;
    }

    /**
     * Creates and caches the image thumbnail and returns URL from thumbnail file.
     *
     * @param string $filename
     * @param integer $width
     * @param integer $height
     * @param string $mode
     * @param string $thumbnailFileExt
     * @return string
     */
    public static function thumbnailFileUrl($filename, $width, $height, $mode = self::THUMBNAIL_OUTBOUND, $thumbnailFileExt = '')
    {
        $filename = FileHelper::normalizePath(Yii::getAlias($filename));
        $cacheUrl = Yii::getAlias('@web/' . self::$cacheAlias);
        $thumbnailFilePath = self::thumbnailFile($filename, $width, $height, $mode, $thumbnailFileExt);

        preg_match('#[^\\' . DIRECTORY_SEPARATOR . ']+$#', $thumbnailFilePath, $matches);
        $fileName = $matches[0];

        return $cacheUrl . '/' . substr($fileName, 0, 2) . '/' . $fileName;
    }


}