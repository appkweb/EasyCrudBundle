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

interface GalleryInterface
{
    /**
     * @param UploadedFile $file
     * @param bool $dirName
     * @return string
     */
    public function upload(UploadedFile $file, $dirName = false): string;

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
     * @return string
     */
    public function generateUniqueFileName(): string;

    /**
     * /!\ Don't forgot to chmod 777 public dir of your symfony project on prod
     * @param $dir -> Directory
     */
    public function createDirIfNotExist($dir): void;
}