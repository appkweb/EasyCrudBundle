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

use App\Entity\Gallery;
use http\Env\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface GalleryInterface
 * @package Appkweb\Bundle\EasyCrudBundle\Providers
 */
interface GalleryInterface
{
    /**
     * @param string blob/ UploadedFile $file
     * @param bool $dirName
     * @return string
     */
    public function upload($file, $dirName = false): string;

    /**
     * @param $filename
     * @param bool $dirName
     */
    public function remove($filename, $dirName = false): void;

    /**
     * @param string $filename
     * @param bool $dirName
     * @return Response|mixed
     */
    public function getImgUrl(string $filename, $dirName = false);

    /**
     * @param $extension
     * @return string
     * @throws \Exception
     */
    public function generateUniqueFileName($extension): string;

    /**
     * /!\ Don't forgot to chmod 777 public dir of your symfony project on prod
     * @param $dir -> Directory
     */
    public function createDirIfNotExist($dir): void;
}