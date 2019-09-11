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

use Appkweb\Bundle\EasyCrudBundle\Generator\YamlCrudTranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package Appkweb\Bundle\EasyCrudBundle\\Controller
 */
class DefaultController
{
    /**
     * @var YamlCrudTranslatorInterface
     */
    private $yamlCrudTranslator;

    public function __construct(YamlCrudTranslatorInterface $yamlCrudTranslator)
    {
        $this->yamlCrudTranslator = $yamlCrudTranslator;
    }

    /**
     * @Route("/index.html", name="appkweb_easy_crud_index")
     * @Template()
     */
    public function index()
    {
        return [];
    }

    /**
     * @Route("/autoMenu", name="appkweb_easy_crud_auto_menu")
     * @Template()
     */
    public function autoMenu(Request $request)
    {
        $page = $request->get('page', false);
        if (!$page) {
            throw new \Exception("Variable \"page\" does not exist.", 500);
        }
        return ['page' => $page, 'entities' => $this->yamlCrudTranslator->getEntities()];
    }
}