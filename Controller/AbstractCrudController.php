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
use Appkweb\Bundle\EasyCrudBundle\Traits\CrudTrait;
use http\Env\Response;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Appkweb\Bundle\EasyCrudBundle\Utils\CrudHelper;

/**
 * Class AbstractCrudController
 * @package Appkweb\Bundle\EasyCrudBundle\Controller
 */
abstract class AbstractCrudController
{
    use CrudTrait;

    /**
     * @param string $classname
     * @param bool $id
     * @return array
     */
    protected function add(string $classname, $id)
    {
        $crudDef = $this->getCrudDefinition($classname);
        return
            [
                'crud_def' => $crudDef,
                'page' => $this->request->get('page', $crudDef->getClassName() . '_add'),
                'id' => $id
            ];
    }

    /**
     * @param string $classname
     * @return array
     * @throws \Exception
     */
    protected function list(string $classname = '')
    {
        $crudDef = $this->yamlCrudTranslator->getCrudDefByClassName($classname);
        $referer = $crudDef->getReferrer();
        if ($referer == "Id") $referer = "id";
        $list = $this->manager->getRepository(CrudHelper::getAbsoluteClassName($classname))
            ->findBy([], [$referer => "ASC"]);

        return [
            'list' => $list,
            'crud_def' => $crudDef,
            'page' => $this->request->get('page', $crudDef->getClassName() . '_list')
        ];
    }

    /**
     * @param string $classname
     * @param int|null $id
     * @return array
     */
    protected function show(string $classname = '', int $id = null)
    {
        if (!$id) throw new \InvalidArgumentException('Id is missing', 500);
        $crudDef = $this->yamlCrudTranslator->getCrudDefByClassName($classname);
        $entity = $this->manager->getRepository(CrudHelper::getAbsoluteClassName($classname))->find($id);
        return [
            'crud_def' => $crudDef,
            'entity' => $entity,
            'page' => $this->request->get('page', $crudDef->getClassName() . '_list')
        ];
    }

    /**
     * @param string $classname
     * @param int $id
     * @return RedirectResponse
     * @throws \Exception
     */
    protected function remove(string $classname = '', int $id)
    {
        if (!$id || $classname == '') throw new \Exception("Param id or classname is missing", 500);
        $entity = $this->manager->getRepository(CrudHelper::getAbsoluteClassName($classname))->find($id);
        if (!$entity) {
            $this->flash->add('error', 'Cette élément à déja été supprimé');
        } else {
            $crudDef = $this->getCrudDefinition($classname);
            foreach ($crudDef->getAttributes() as $attr) {
                if ($attr->getType() == 'Add list') {
                    $datas = $entity->{'get' . ucwords($attr->getName())}();
                    foreach ($datas as $data) {
                        $this->manager->remove($data);
                    }
                    $this->manager->flush();
                }
            }
            $this->manager->remove($entity);
            $this->manager->flush();
            $this->flash->add('success', $crudDef->getLabel() . " supprimé avec succès");
        }
        return new RedirectResponse($this->route->generate('appkweb_easy_crud_list', ['classname' => $classname]));
    }
}
