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

use http\Env\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class Gallery
 * @package Appkweb\Bundle\EasyCrudBundle\Providers
 */
class Gallery implements GalleryInterface
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @param UploadedFile $file
     * @param bool $dirName
     * @return string
     */
    public function upload(UploadedFile $file, $dirName = false): string
    {
        /* @var File $file */
        $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
        if (!$dirName) $dirName = 'default_media';
        $absolutePathDirectory = $this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . $dirName;
        $this->createDirIfNotExist($absolutePathDirectory);

        if ($file->getSize() > 800000) {
            $percent = 0.1;
            list($width, $height) = getimagesize($file->getRealPath());
            $new_width = $width * $percent;
            $new_height = $height * $percent;
            $image_p = imagecreatetruecolor($new_width, $new_height);
            $image = imagecreatefromjpeg($file->getRealPath());
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagejpeg($image_p, $absolutePathDirectory . DIRECTORY_SEPARATOR . $fileName, 100);
        } else {
            $file->move($absolutePathDirectory, $fileName);
        }
        return $fileName;
    }

    /**
     * @param string $filename
     * @param bool $dirName
     * @return Response|mixed
     */
    public function getImgUrl(string $filename, $dirName = false)
    {
        if ($gallery) {
            if (!$dirName) $dirName = 'default_media';
            $absolutePathDirectory = DIRECTORY_SEPARATOR . $dirName . DIRECTORY_SEPARATOR;
            if ($filename) {
                return new Response($absolutePathDirectory . $filename) ;
            } else {
                return new Response(false);
            }
        } else {
            return new Response(false);
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
     * @return string
     */
    public function generateUniqueFileName(): string
    {
        return md5(uniqid());
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


}