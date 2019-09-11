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


use Appkweb\Bundle\EasyCrudBundle\Crud\AbstractCrudMaker;
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
class CrudController extends AbstractCrudMaker
{
    /**
     * @var YamlCrudTranslatorInterface
     */
    protected $yamlCrudTranslator;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;


    public function __construct(YamlCrudTranslatorInterface $yamlCrudTranslator, FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        $this->yamlCrudTranslator = $yamlCrudTranslator;
    }

    /**
     * @Route("/{classname}/add.html", name="appkweb_easy_crud_add")
     * @Template()
     */
    public function add(Request $request)
    {
        $className = $request->get('classname', false);
        if (!$className) {
            throw new \Exception("Classname of entity is missing", 500);
        }
        $crudDef = $this->yamlCrudTranslator->getCrudDefByClassName($className);
        $data = ['crud_def' => $crudDef];
        $form = $this->formFactory->create(CrudMakerType::class, $data);
        /* @var FormInterface $form */
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

        }
        return ['crud_def' => $crudDef, 'form' => $form->createView(), 'page' => $request->get('page', ucfirst($className) . '_add')];
    }
}