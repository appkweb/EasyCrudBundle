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

use Appkweb\Bundle\EasyCrudBundle\Crud\AttributeDefinition;
use Appkweb\Bundle\EasyCrudBundle\Crud\CrudDefinition;
use Appkweb\Bundle\EasyCrudBundle\Form\Generator\AttributeDefType;
use Appkweb\Bundle\EasyCrudBundle\Form\Generator\EntityDefType;
use Appkweb\Bundle\EasyCrudBundle\Generator\PhpClassCreatorInterface;
use Appkweb\Bundle\EasyCrudBundle\Generator\YamlCrudTranslator;
use Appkweb\Bundle\EasyCrudBundle\Generator\YamlCrudTranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GeneratorController
 * @package Appkweb\Bundle\EasyCrudBundle\Controller
 * @Route("/generator")
 */
class GeneratorController
{
    /* @var FormFactoryInterface $form */
    private $formFactory;

    /**
     * @var YamlCrudTranslatorInterface
     */
    private $yamlCrudTranslator;

    /**
     * @var PhpClassCreatorInterface
     */
    private $phpClassCreator;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * GeneratorController constructor.
     * @param YamlCrudTranslatorInterface $yamlCrudTranslator
     * @param FormFactoryInterface $formFactory
     * @param PhpClassCreatorInterface $phpClassCreator
     */
    public function __construct(KernelInterface $kernel, YamlCrudTranslatorInterface $yamlCrudTranslator, FormFactoryInterface $formFactory, PhpClassCreatorInterface $phpClassCreator)
    {
        $this->formFactory = $formFactory;
        $this->yamlCrudTranslator = $yamlCrudTranslator;
        $this->phpClassCreator = $phpClassCreator;
        $this->kernel = $kernel;
    }

    /**
     * @Route("/add.html", name="appkweb_easy_crud_generator_add")
     * @Template()
     */
    public function add(Request $request)
    {
        $className = $request->get('className', false);
        $crudDef = [];
        $attr = [];
        if ($className) {
            $crudDef = $this->yamlCrudTranslator->getCrudDefByClassName($className);
            $attr = $crudDef->getAttributes();
        }
        $form = $this->formFactory->create(EntityDefType::class, $crudDef);
        $formAttr = $this->formFactory->create(AttributeDefType::class);
        $page = $request->get('page', 'generator_add');
        return ['page' => $page, 'form' => $form->createView(), 'form_attr' => $formAttr->createView(), 'attributes' => $attr];
    }

    /**
     * @Route("/list.html", name="appkweb_easy_crud_generator_list")
     * @Template()
     */
    public function list(Request $request)
    {
        $page = $request->get('page', 'generator_list');
        $entities = $this->yamlCrudTranslator->getEntities();
        return ['page' => $page, 'entities' => $entities];
    }

    /**
     * @Route("/save.json", name="appkweb_easy_crud_generator_save_json")
     * @Template()
     */
    public function save(Request $request)
    {
        $crud = new CrudDefinition();
        $crud->setClassName(ucfirst($request->get('className')));
        $crud->setEdit($request->get('edit'));
        $crud->setLabel(ucfirst($request->get('label')));
        $crud->setEntityName(strtolower($request->get('entityName')));
        $crud->setList($request->get('list'));
        $crud->setPrefix($request->get('prefix'));
        $crud->setRemove($request->get('remove'));
        $crud->setVisible($request->get('visible'));
        $crud->setOrder($request->get('order'));
        $crud->setAdd($request->get('add'));
        $attributes = [];
        foreach (json_decode($request->get('attributes'), true) as $attribute) {
            $attrDef = new AttributeDefinition();
            $attrDef->setOrder($attribute['attr_order']);
            $attrDef->setLabel($attribute['attr_label']);
            $attrDef->setName($attribute['attr_name']);
            $attrDef->setEntityRelation($attribute['attr_entity_relation']);
            $attrDef->setVisible($attribute['attr_visible']);
            $attrDef->setNullable($attribute['attr_nullable']);
            $attrDef->setType($attribute['attr_type']);
            $attrDef->setSize($attribute['attr_size']);
            $crud->addAttributes($attrDef);
        }

        // Write Crud définition into Yaml file
        $this->yamlCrudTranslator->save($crud);

        // Write php object class
        $this->phpClassCreator->save($crud);

        // Create Database if not exist
        $process = new Process(['php', '../bin/console', 'doctrine:database:create']);
        $process->run();

        // Check diff between object relation and database schema
        $process = new Process(['php', '../bin/console', 'make:migration']);
        $process->run();

        // Run update of Database schema
        $process = new Process(['php', '../bin/console', 'doctrine:migration:migrate']);
        $process->run();

        return new JsonResponse([true]);
    }
}