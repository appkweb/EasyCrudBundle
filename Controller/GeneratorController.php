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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

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
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FlashBagInterface
     */
    private $flash;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * GeneratorController constructor.
     * @param FlashBagInterface $flash
     * @param RouterInterface $router
     * @param KernelInterface $kernel
     * @param YamlCrudTranslatorInterface $yamlCrudTranslator
     * @param FormFactoryInterface $formFactory
     * @param PhpClassCreatorInterface $phpClassCreator
     */
    public function __construct(EntityManagerInterface $em, FlashBagInterface $flash, RouterInterface $router, KernelInterface $kernel, YamlCrudTranslatorInterface $yamlCrudTranslator, FormFactoryInterface $formFactory, PhpClassCreatorInterface $phpClassCreator)
    {
        $this->em = $em;
        $this->flash = $flash;
        $this->router = $router;
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
        $refferers = ['Id' => 'Id'];
        $attr = [];
        if ($className) {
            $crudDef = $this->yamlCrudTranslator->getCrudDefByClassName($className);
            $attr = $crudDef->getAttributes();
        }
        $form = $this->formFactory->create(EntityDefType::class, $crudDef);
        $formAttr = $this->formFactory->create(AttributeDefType::class);
        $page = $request->get('page', 'generator_add');
        return ['page' => $page, 'form' => $form->createView(), 'form_attr' => $formAttr->createView(), 'attributes' => $attr, 'classname' => $className];
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
     * @Route("/remove", name="appkweb_easy_crud_generator_remove")
     * @Template()
     */
    public function remove(Request $request)
    {
        $classname = $request->get('className');
        $crudDef = $this->yamlCrudTranslator->getCrudDefByClassName($classname);
        $entityName = $crudDef->getEntityName();
        $this->phpClassCreator->removeRelations($classname);
        $this->yamlCrudTranslator->remove($classname);
        $this->phpClassCreator->remove($classname);


        // Create Database if not exist
        $process = new Process(['php', '../bin/console', 'doctrine:database:create']);
        $process->run();

        // Check diff between object relation and database schema
        $process = new Process(['php', '../bin/console', 'doctrine:schema:update', '--force']);
        $process->run();

        // Create getters ans setters
        $process = new Process(['php', '../bin/console', 'make:entity', '--regenerate', 'App']);
        $process->run();

        $sql = 'DROP TABLE ' . $entityName . ';';
        $connection = $this->em->getConnection();
        $stmt = $connection->prepare($sql);
        $stmt->execute();
        $stmt->closeCursor();

        $this->flash->add("success", $classname . " crud removed with success !");

        return new RedirectResponse($this->router->generate("appkweb_easy_crud_generator_list"));
    }

    /**
     * @Route("/save.json", name="appkweb_easy_crud_generator_save_json")
     * @Template()
     */
    public function save(Request $request)
    {
        $crud = new CrudDefinition();
        $oldClassName = $request->get('oldClassName', false);
        $crud->setClassName(ucfirst($request->get('className')));
        $crud->setEdit($request->get('edit') === 'true' ? true : false);
        $crud->setLabel(ucfirst($request->get('label')));
        $crud->setEntityName(strtolower($request->get('entityName')));
        $crud->setList($request->get('list')=== 'true' ? true : false);
        $crud->setPrefix($request->get('prefix'));
        $crud->setRemove($request->get('remove')=== 'true' ? true : false);
        $crud->setVisible($request->get('visible') === 'true' ? true : false);
        $crud->setOrder($request->get('order'));
        $crud->setAdd($request->get('add') === 'true' ? true : false);
        $crud->setReferrer($request->get('referrer'));
        $crud->setShow($request->get('show')=== 'true' ? true : false);
        $attributes = [];
        foreach (json_decode($request->get('attributes'), true) as $attribute) {
            $attrDef = new AttributeDefinition();
            $attrDef->setOrder($attribute['attr_order']);
            $attrDef->setLabel($attribute['attr_label']);
            $attrDef->setName($attribute['attr_name']);
            $attrDef->setEntityRelation($attribute['attr_entity_relation']);
            $attrDef->setShow($attribute['attr_show'] === 'true' ? true : false);
            $attrDef->setList($attribute['attr_list'] === 'true' ? true : false);
            $attrDef->setEdit($attribute['attr_edit'] === 'true' ? true : false);
            $attrDef->setUnique($attribute['attr_unique'] === 'true' ? true : false);
            $attrDef->setNullable($attribute['attr_nullable'] === 'true' ? true : false);
            $attrDef->setType($attribute['attr_type']);
            $crud->addAttributes($attrDef);
        }

        // Write Crud définition into Yaml file
        $this->yamlCrudTranslator->save($crud);

        // Write php object class
        $this->phpClassCreator->save($crud, $oldClassName);


        // Create Database if not exist
        $process = new Process(['php', '../bin/console', 'doctrine:database:create']);
        $process->run();

        // Check diff between object relation and database schema
        $process = new Process(['php', '../bin/console', 'doctrine:schema:update', '--force']);
        $process->run();

        // Create getters ans setters
        $process = new Process(['php', '../bin/console', 'make:entity', '--regenerate', 'App']);
        $process->run();

        $this->flash->add("success", $crud->getClassName() . " crud generated with success !");

        return new JsonResponse(["status" => true, "location" => $this->router->generate("appkweb_easy_crud_generator_list")]);
    }
}