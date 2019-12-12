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

namespace Appkweb\Bundle\EasyCrudBundle\Traits;

use Appkweb\Bundle\EasyCrudBundle\Controller\AddListController;
use Appkweb\Bundle\EasyCrudBundle\Crud\CrudDefinition;
use Appkweb\Bundle\EasyCrudBundle\Form\Crud\CrudMakerType;
use Appkweb\Bundle\EasyCrudBundle\Generator\YamlCrudTranslatorInterface;
use Appkweb\Bundle\EasyCrudBundle\Providers\GalleryInterface;
use Appkweb\Bundle\EasyCrudBundle\Utils\CrudHelper;
use Appkweb\Bundle\EasyCrudBundle\Validator\CrudValidator;
use Appkweb\Bundle\EasyCrudBundle\Validator\CrudValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;

/**
 * Trait CrudTrait
 * @package Appkweb\Bundle\EasyCrudBundle\Traits
 */
trait CrudTrait
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var KernelInterface
     */
    private $kernel;


    /**
     * @var Environment
     */
    protected $template;

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

    /**
     * @var FlashBagInterface
     */
    protected $flash;

    /**
     * @var RequestStack
     */
    protected $request;

    /**
     * @var CrudValidatorInterface
     */
    protected $crudValidator;

    public $redirectRoute = false;


    public function __construct(KernelInterface $kernel, CrudValidatorInterface $crudValidator, RequestStack $requestStack, Environment $template, FlashBagInterface $flash, GalleryInterface $gallery, RouterInterface $route, EntityManagerInterface $entityManager, YamlCrudTranslatorInterface $yamlCrudTranslator, FormFactoryInterface $formFactory)
    {
        $this->crudValidator = $crudValidator;
        $this->template = $template;
        $this->yamlCrudTranslator = $yamlCrudTranslator;
        $this->flash = $flash;
        $this->formFactory = $formFactory;
        $this->manager = $entityManager;
        $this->route = $route;
        $this->gallery = $gallery;
        $this->request = $requestStack->getMasterRequest();
        $this->kernel = $kernel;
    }

    /**
     * @param $filename
     * @return mixed
     */
    public function getImgUrl($filename)
    {
        return $this->gallery->getImgUrl($filename);
    }

    /**
     * @param string $classname
     * @param bool $parent_classname
     * @param bool $id
     * @param string $formatResponse
     * @return string|Response
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getFormView(string $classname, $parent_classname = false, $id = false, $formatResponse = 'json', $data = [], $onlyAttribute = false, $redirectPath = false)
    {

        $crudDef = $this->getCrudDefinition($classname);
        if (!$this->redirectRoute) {
            $this->redirectRoute = $redirectPath;
        }
        $entity = $this->getEntityInstance($crudDef, $id);
        if (!$classname) throw new \Exception('Classname param is missing !', 500);
        $this->form = $this->getForm($crudDef, $entity, $data);
        $this->handleFormSubmit($crudDef, $entity); // If form is submitted
        $errors = $this->crudValidator->getErrors();
        $view = $this->template->render('@EasyCrud/crud/form_view.html.twig',
            [
                'parent_classname' => $parent_classname,
                'only_attr' => $onlyAttribute,
                'form' => $this->form->createView(),
                'crud_def' => $crudDef,
                'errors' => $errors,
                'id' => $id
            ]
        );
        if ($formatResponse == 'json') {
            return new Response($view);
        }
        return $view;
    }

    /**
     * @param string $classname
     * @param int|null $id
     * @return Response
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getPrintView(string $classname = '', int $id = null)
    {
        if (!$id) throw new \InvalidArgumentException('id params is missing !', 500);
        $crudDef = $this->getCrudDefinition($classname);
        $entity = $this->getEntityInstance($crudDef, $id);
        $view = $this->template->render('@EasyCrud/crud/print_view.html.twig', ['crud_def' => $crudDef, 'entity' => $entity]);
        return new Response($view);
    }

    /**
     * @param CrudDefinition $crudDef
     * @param $entity
     * @return RedirectResponse
     */
    public function handleFormSubmit(CrudDefinition $crudDef, $entity, CrudValidatorInterface $crudValidator = null)
    {
        $id = false;
        $this->form->handleRequest($this->request);

        if ($this->form->isSubmitted()) {
            if ($entity->getId()) $id = $entity->getId();
            $this->crudValidator->validate($crudDef, $this->form->getData(), $id);
            if ($this->crudValidator->isValid()) {
                $this->save($crudDef, $entity);
                $this->flash->add("success", $crudDef->getLabel() . " mis à jour avec succès !");

                $response = new RedirectResponse($this->redirectRoute);
                $response->send(); // Can't return RedirectResponse in embedded Controller. We need to send the response
            }
        }
    }

    /**
     * @param CrudDefinition $crudDef
     * @param $entity
     * @param FormFactoryInterface $form
     */
    protected function save(CrudDefinition $crudDef, $entity): void
    {
        $entityToSave = $this->hydrateObject($crudDef, $entity, $this->form->getData());
        $this->manager->persist($entityToSave);
        $this->manager->flush();
    }

    /**
     * @param CrudDefinition $crudDef
     * @param $entity
     * @param array $datas
     * @param CrudDefinition|null $parentCrudDef
     * @param bool $parentEntity
     * @return mixed
     * @throws \Exception
     */
    protected function hydrateObject(CrudDefinition $crudDef, $entity, array $datas, CrudDefinition $parentCrudDef = null, $parentEntity = false)
    {
        $extendedDatas = $this->request->get('crud_maker');
        foreach ($extendedDatas as $key => $extended) {
            $datas[$key] = $extended;
        }
        $section = [];
        if ($parentCrudDef && !$parentEntity || $parentEntity && !$parentCrudDef) {
            throw new \Exception("Error ! if you transmit a parent you need 2 params (Entity and CrudDefiniton of it)", 500);
        }
        foreach ($crudDef->getAttributes() as $key => $attribute) {
            $descriptor = $attribute->getName();
            if ($parentCrudDef) $descriptor = ucfirst($attribute->getLabel());
            if ($parentCrudDef && $attribute->getEntityRelation() == $parentCrudDef->getClassName()) { // if we hydrate AddList
                $entity->{'set' . ucwords($attribute->getName())}($parentEntity);
            } else {
                if ($attribute->getType() == 'Section') {
                    $crud = $this->getCrudDefinition($attribute->getEntityRelation());
                    $referer = $crud->getReferrer();
                    $instance = $entity->{'get' . ucwords($attribute->getName())}();
                    $id = null;
                    if ($instance) {
                        $id = $instance->getId();
                    }
                    $instance = $this->getEntityInstance($crud, $entity->{'get' . ucwords($attribute->getName())}(), $id);
                    $data = $this->hydrateObject($crud, $instance, $datas);
//                    dump($data);die;
                    $this->manager->persist($data);
                    $entity->{'set' . ucwords($attribute->getName())}($data);
                }
                if (array_key_exists($descriptor, $datas)) {
                    $data = trim(preg_replace('/\s+/', ' ', $datas[$descriptor]));
                    if ($data != "" && $data) {
                        switch ($attribute->getType()) {
                            case 'Simple image picker' :
                                $oldFile = $entity->{'get' . ucwords($attribute->getName())}();
                                if ($oldFile && $data) {
                                    $this->gallery->remove($oldFile);
                                }
                                if ($data) {
                                    $data = $this->gallery->upload($datas[$descriptor]);
                                }
                                break;
                            case 'Date picker' :
                                $data = \DateTime::createFromFormat('d/m/Y', $data);
                                break;
                            case 'Simple select' :
                                if (is_string($data)) {
                                    $crud = $this->getCrudDefinition($attribute->getEntityRelation());
                                    $referer = $crud->getReferrer();
                                    if ($referer == 'Id') {
                                        $referer = 'id';
                                    }
                                    $data = $this->manager->getRepository(CrudHelper::getAbsoluteClassName($attribute->getEntityRelation()))->findOneBy(['id' => $data]);
                                }
                                break;
                        }
                        if (is_string($data) && $attribute->getEntityRelation() != false) {

                        } else {
                            $entity->{'set' . ucwords($attribute->getName())}($data);

                        }
                    }

                }
            }
        }
        return $entity;
    }


    /**
     * @param CrudDefinition $crudDef
     * @param $entity
     * @return FormInterface
     */
    protected function getForm(CrudDefinition $crudDef, $entity, $data = []): FormInterface
    {
        if ($data == []) {
            foreach ($crudDef->getAttributes() as $attr) {
                $attrData = $entity->{'get' . ucwords($attr->getName())}();
                if ($attrData) {
                    if ($attr->getType() == 'Date picker') {
                        $attrData = $attrData->format('d/m/Y');
                    }
                    $data[$attr->getName()] = $attrData;
                }
            }
        }

        $data['crud_def'] = $crudDef;
        return $this->formFactory->create(CrudMakerType::class, $data);
    }

    /**
     * @param string $className
     */
    protected function getCrudDefinition(string $className)
    {
        return $this->yamlCrudTranslator->getCrudDefByClassName($className);
    }

    /**
     * @param CrudDefinition $crudDefinition
     * @param bool $id
     * @return object|\ReflectionClass|null
     * @throws \ReflectionException
     */
    protected function getEntityInstance(CrudDefinition $crudDef, $id = false)
    {
        if ($id) {
            return $this->manager->getRepository(CrudHelper::getAbsoluteClassName($crudDef->getClassName()))->find($id);
        } else {
            return CrudHelper::getNewInstanceOf($crudDef->getClassName());
        }
    }

    /**
     * @param CrudDefinition $crudDefinition
     * @param int|null $id
     * @throws \ReflectionException
     */
    public function removeChildEntitiesOf(CrudDefinition $crudDefinition, int $id = null): void
    {
        $entity = $this->getEntityInstance($crudDefinition, $id);
        foreach ($crudDefinition->getAttributes() as $attr) {
            if ($attr->getType() == 'Add list') {
                $childCrudDef = $this->getCrudDefinition($attr->getEntityRelation());
                $referer = '';
                foreach ($childCrudDef->getAttributes() as $childAttr) {
                    if ($childAttr->getEntityRelation() == $crudDefinition->getClassName()) {
                        $referer = $childAttr->getName();
                    }
                }
                $entities = $this->manager->getRepository(CrudHelper::getAbsoluteClassName($childCrudDef->getClassName()))->findBy([$referer => $entity]);
                foreach ($entities as $obj) {
                    $this->manager->remove($obj);
                }
                $this->manager->flush();
            }
        }
    }
}
