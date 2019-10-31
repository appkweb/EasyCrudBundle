<?php
/*
 * This file is part of the Appkweb package.
 *
 * (c) Valentin REGNIER <vregnier@appkweb.com>
 *
 * Contributors :
 * - REGNIER Valentin
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Appkweb\Bundle\EasyCrudBundle\Providers;


use App\Providers\Attachment;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class Gallery
 * @package Appkweb\Bundle\EasyCrudBundle\Providers
 */
class Gallery implements GalleryInterface
{
    private $kernel;
    private $ind = 0;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param string blob/ UploadedFile $file
     * @param bool $dirName
     * @return string
     */
    public function upload($file, $dirName = false): string
    {

        if (!$dirName) $dirName = 'default_media';
        $absolutePathDirectory = $this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . $dirName;
        $this->createDirIfNotExist($absolutePathDirectory);
        if ($file instanceof UploadedFile) {

            $filename = $this->generateUniqueFileName($file->guessExtension());
            if ($file->getSize() > 800000) {
                $percent = 0.1;
                list($width, $height) = getimagesize($file->getRealPath());
                $new_width = $width * $percent;
                $new_height = $height * $percent;
                $image_p = imagecreatetruecolor($new_width, $new_height);
                $image = imagecreatefromjpeg($file->getRealPath());
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                imagejpeg($image_p, $absolutePathDirectory . DIRECTORY_SEPARATOR . $filename, 100);
            } else {
                $file->move($absolutePathDirectory, $filename);
            }
        } else {
            $img = $this->getImgByBlob($file);
            $extension = $this->getImgExtensionByBlob($file);
            $filename = $this->generateUniqueFileName($extension);
            imagejpeg($img, $absolutePathDirectory . DIRECTORY_SEPARATOR . $filename, 100);
        }
        return $filename;
    }

    /**
     * @param string $filename
     * @param bool $dirName
     * @return Response|mixed
     */
    public function getImgUrl(string $filename, $dirName = false)
    {
        if ($filename) {
            if (!$dirName) $dirName = 'default_media';
            $absolutePathDirectory = DIRECTORY_SEPARATOR . $dirName . DIRECTORY_SEPARATOR;
            if ($filename) {
                return new Response($absolutePathDirectory . $filename);
            } else {
                return new Response('');
            }
        } else {
            return new Response('');
        }
    }

    /**
     * @param $filename
     * @param bool $dirName
     */
    public function remove($filename, $dirName = false): void
    {
        if (!$dirName) $dirName = 'default_media';
        $absolutePathDirectory = $this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . $dirName;
        unlink($absolutePathDirectory . DIRECTORY_SEPARATOR . $filename);
    }

    /**
     * @param $extension
     * @return string
     * @throws \Exception
     */
    public function generateUniqueFileName($extension): string
    {
        $this->ind ++;
        $date = new \DateTime('now');
        return $date->format('d-m-Y-hh-ii-ss-') . $this->ind . '.' . $extension;
    }

    /**
     * /!\ Don't forgot to chmod 777 public dir of your symfony project on prod
     * @param $dir -> Directory
     */
    public function createDirIfNotExist($dir): void
    {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    /**
     * @param $blob
     * @return false|resource
     */
    public function getImgByBlob(string $blob)
    {
        $blobArray = explode(',', base64_decode($blob));
        return imagecreatefromstring(base64_decode($blobArray[1]));
    }

    /**
     * This function return extension of str blob file
     * @param $blob
     * @return mixed
     */
    public function getImgExtensionByBlob(string $blob)
    {
        $blobArray = explode(';', base64_decode($blob));
        $blobArray = explode('/', $blobArray[0]);
        $extension = $blobArray[1];
        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }
        return $extension;
    }
}