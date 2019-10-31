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

use Appkweb\Bundle\EasyCrudBundle\Traits\CrudTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AddListController
 * @package Appkweb\Bundle\EasyCrudBundle\Controller
 * @Route("/add_list")
 */
class AddListController
{
    use CrudTrait;

    /**
     *
     * @Route("/getAddListModal.json", name="appkweb_easy_crud_add_list_modal_json")
     * @param $classname
     * @param $current
     * @return \Symfony\Component\HttpFoundation\Response|void
     */
    public function renderFormModalView(Request $request)
    {
        $parent_classname = $request->get('parent_classname', false);
        $classname = $request->get('classname', false);
        $crud_def = $this->yamlCrudTranslator->getCrudDefByClassName($classname);
        return new JsonResponse(['template' => $this->template->render('@EasyCrud/add_list/form_modal_view.html.twig',
            [
                'parent_classname' => $parent_classname,
                'crud_def' => $crud_def
            ])
        ]);
    }

    /**
     * @Route("/save_parent.json", name="appkweb_easy_crud_save_parent_json")
     * @param Request $request
     */
    public function saveParent(Request $request)
    {
        $data = json_decode($request->get('data'), true);
        $crudDef = $this->yamlCrudTranslator->getCrudDefByClassName($data['classname']);
        $entity = $this->getEntityInstance($crudDef);
        $this->hydrateObject($crudDef, $entity, $data);
        $this->manager->persist($entity);
        $this->manager->flush();
        return new JsonResponse(['id' => $entity->getId()]);
    }

    /**
     * @Route("/save_child.json", name="appkweb_easy_crud_save_child_json")
     * @param Request $request
     */
    public function saveChild(Request $request)
    {
        $data = json_decode($request->get('data'), true);
        $crudDefParent = $this->getCrudDefinition($data['parent_classname']);
        $crudDefChild = $this->getCrudDefinition($data['child_classname']);
        $parentEntity = $this->getEntityInstance($crudDefParent, $data['id']);
        $childEntity = $this->getEntityInstance($crudDefChild);
        $childEntity = $this->hydrateObject($crudDefChild, $childEntity, $data['row_datas'], $crudDefParent, $parentEntity);
        $this->manager->persist($childEntity);
        $this->manager->flush();
        return new JsonResponse([true]);
    }

    /**
     * @param $classname
     * @return Response
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getAddList($classname, $parent_classname)
    {
        $crud_def = $this->yamlCrudTranslator->getCrudDefByClassName($classname);
        $this->entity = $this->getEntityInstance($crud_def);
        return new Response($this->template->render('@EasyCrud/add_list/add_list.html.twig', ['parent_classname' => $parent_classname, 'crud_def' => $crud_def]));
    }

    /**
     * @Route("/validator.json", name="appkweb_easy_crud_validator_json")
     * @param Request $request
     */
    public function validator(Request $request)
    {
        $data = json_decode($request->get('data'), true);
        dump($data);
        $classname = $data["classname"];
        $parentClassanme = $data['parent_classname'];
        dump($parentClassanme);
        $crudDef = $this->getCrudDefinition($classname);
        unset($data['classname']);
        $entity = $this->getEntityInstance($crudDef);
        $this->crudValidator->validate($crudDef, $data);
        $status = $this->crudValidator->isValid();
        $view = self::getFormView($classname, $parentClassanme, false, 'array');
        if (!$status) {
            return new JsonResponse(['status' => $status, 'template' => $view]);
        }
        return new JsonResponse(['status' => $status]);
    }

}