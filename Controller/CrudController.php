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

use Appkweb\Bundle\EasyCrudBundle\Form\Crud\CrudMakerType;
use Appkweb\Bundle\EasyCrudBundle\Generator\YamlCrudTranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CrudController
 * @package Appkweb\Bundle\EasyCrudBundle\Controller
 * @Route("/crud")
 */
class CrudController extends AbstractCrudController
{
    /**
     * @Route("/{classname}/add.html", name="appkweb_easy_crud_add")
     * @Template()
     */
    public function add(Request $request)
    {
        return parent::add($request);
    }

    /**
     * @Route("/{classname}/list.html", name="appkweb_easy_crud_list")
     * @Template()
     */
    public function list(Request $request)
    {
        return parent::list($request);
    }
}