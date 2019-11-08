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
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CrudController
 * @package Appkweb\Bundle\EasyCrudBundle\Controller
 * @Route("/crud")
 */
class CrudController extends AbstractCrudController
{
    /**
     * @Route("/add/{classname}/{id}", name="appkweb_easy_crud_add",defaults={"id"=false})
     * @Template()
     */
    public function add(string $classname, $id)
    {
        return parent::add($classname, $id);
    }

    /**
     * @Route("/list/{classname}", name="appkweb_easy_crud_list")
     * @Template()
     * @param string $classname
     * @return array
     * @throws \Exception
     */
    public function list(string $classname = '')
    {
        return parent::list($classname);
    }

    /**
     * @Route("/remove/{classname}/{id}", name="appkweb_easy_crud_remove",defaults={"id"=null})
     * @param string $classname
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function remove(string $classname = '', int $id)
    {
        return parent::remove($classname, $id);
    }

    /**
     * @Route("/{classname}/show/{id}", name="appkweb_easy_crud_show",defaults={"id"=null})
     * @Template()
     */
    public function show(string $classname = '', int $id = null)
    {
        return parent::show($classname,$id);
    }
}