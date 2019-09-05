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

namespace Appkweb\Bundle\EasyCrudBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class DefaultController
 * @package Appkweb\Bundle\EasyCrudBundle\\Controller
 */
class DefaultController extends AbstractController
{
    /**
     * @Template()
     */
    public function index()
    {
        return [];
    }
}