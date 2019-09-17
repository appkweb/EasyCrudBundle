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

use Appkweb\Bundle\EasyCrudBundle\Crud\CrudDefinition;
use Appkweb\Bundle\EasyCrudBundle\Form\Crud\CrudMakerType;
use Appkweb\Bundle\EasyCrudBundle\Generator\YamlCrudTranslatorInterface;
use Appkweb\Bundle\EasyCrudBundle\Providers\GalleryInterface;
use Appkweb\Bundle\EasyCrudBundle\Traits\CrudTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use phpDocumentor\Reflection\Types\Object_;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Appkweb\Bundle\EasyCrudBundle\Utils\CrudHelper;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AbstractCrudController
 * @package Appkweb\Bundle\EasyCrudBundle\Controller
 */
abstract class AbstractCrudController
{
    use CrudTrait;

    /**
     * @var YamlCrudTranslatorInterface
     */
    protected $yamlCrudTranslator;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    /**
     * @var RouterInterface
     */
    protected $route;

    /**
     * @var GalleryInterface
     */
    protected $gallery;

    public function __construct(GalleryInterface $gallery, RouterInterface $route, EntityManagerInterface $entityManager, YamlCrudTranslatorInterface $yamlCrudTranslator, FormFactoryInterface $formFactory)
    {
        $this->yamlCrudTranslator = $yamlCrudTranslator;
        $this->formFactory = $formFactory;
        $this->manager = $entityManager;
        $this->route = $route;
        $this->gallery = $gallery;
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function add(Request $request)
    {
        $className = $request->get('classname', false);
        if (!$className) {
            throw new \Exception("Param classname is missing", 500);
        }
        $crudDefinition = $this->yamlCrudTranslator->getCrudDefByClassName($className);
        /* @var FormInterface $form */
        $form = $this->getForm($crudDefinition);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->save($form);
            return new RedirectResponse($this->route->generate('appkweb_easy_crud_generator_list', ['classname' => $className]));
        }
        return [
            'crud_def' => $crudDefinition,
            'form' => $form->createView(),
            'page' => $request->get('page', $crudDefinition->getClassName() . '_add')
        ];
    }


    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    protected function list(Request $request)
    {
        $className = $request->get('classname', false);
        if (!$className) {
            throw new \Exception("Param classname is missing", 500);
        }
        $crudDefinition = $this->yamlCrudTranslator->getCrudDefByClassName($className);
        $list = $this->manager->getRepository(CrudHelper::getAbsoluteClassName($className))->findAll();

        return ['list' => $list,
            'crud_def' => $crudDefinition,
            'page' => $request->get('page', $crudDefinition->getClassName() . '_list')
        ];
    }

    protected function show(CrudDefinition $crudDefinition)
    {

    }
}
