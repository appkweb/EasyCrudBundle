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
        $id = $data['id'];
        $args['id'] = $id;
        $status = false;
        $crudDef = $this->yamlCrudTranslator->getCrudDefByClassName($data['classname']);
        $entity = $this->getEntityInstance($crudDef, $id);
        $this->crudValidator->validate($crudDef, $data, $id);
        if ($this->crudValidator->isValid()) {
            if ($id) {
                $this->removeChildEntitiesOf($crudDef, $id);
            }
            $status = true;
            $this->hydrateObject($crudDef, $entity, $data);
            $this->manager->persist($entity);
            $this->manager->flush();
            $args['id'] = $entity->getId();
        } else {
            $template = $this->getFormView($crudDef->getClassName(), false, $id, 'array');
            $args['template'] = $template;
        }
        $args['status'] = $status;
        $this->flash->add('success', $crudDef->getLabel() . ' mis à jour avec succès !');
        return new JsonResponse($args);
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
        return new JsonResponse(['redirect_path' => $this->route->generate('appkweb_easy_crud_list', ['classname' => $data['parent_classname']])]);
    }

    /**
     * @param string $classname
     * @param string $parent_classname
     * @param int|null $id
     * @param bool $allow_actions
     * @return Response
     * @throws \ReflectionException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getAddList(string $classname = '', string $parent_classname = '', int $id = null, bool $allow_actions = true)
    {
        $crud_def = $this->yamlCrudTranslator->getCrudDefByClassName($classname);
        $parent_crud_def = $this->getCrudDefinition($parent_classname);
        $list = $this->getEntityInstance($parent_crud_def, $id);
        $attrName = '';
        foreach ($parent_crud_def->getAttributes() as $attr) {
            if ($attr->getEntityRelation() == $classname) {
                $attrName = $attr->getName();
            }
        }
        $list = $list->{'get' . ucwords($attrName)}();

        return new Response($this->template->render('@EasyCrud/add_list/add_list.html.twig', ['allow_actions' => $allow_actions, 'parent_classname' => $parent_classname, 'crud_def' => $crud_def, 'list' => $list]));
    }

    /**
     * @Route("/validator.json", name="appkweb_easy_crud_validator_json")
     * @param Request $request
     */
    public function validator(Request $request)
    {
        $data = json_decode($request->get('data'), true);
        $classname = $data["classname"];
        $parentClassanme = $data['parent_classname'];
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