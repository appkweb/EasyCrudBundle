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
     * @Route("/add/{prefix}/{classname}/{id}", name="appkweb_easy_crud_add",defaults={"id"=false})
     * @Template()
     */
    public function add(string $classname, $id)
    {
        return parent::add($classname, $id);
    }

    /**
     * @Route("/{classname}/list.html", name="appkweb_easy_crud_list")
     * @Template()
     */
    public function list()
    {
        return parent::list();
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
     * @Route("/{classname}/show", name="appkweb_easy_crud_show")
     * @Template()
     */
    public function show()
    {
        return parent::show();
    }
}