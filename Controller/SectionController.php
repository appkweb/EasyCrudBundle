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

use Appkweb\Bundle\EasyCrudBundle\Generator\YamlCrudTranslatorInterface;
use Appkweb\Bundle\EasyCrudBundle\Providers\GalleryInterface;
use Appkweb\Bundle\EasyCrudBundle\Traits\CrudTrait;
use Appkweb\Bundle\EasyCrudBundle\Utils\CrudHelper;
use Appkweb\Bundle\EasyCrudBundle\Validator\CrudValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * Class SectionController
 * @package Appkweb\Bundle\EasyCrudBundle\Controller
 * @Route("/section")
 */
class SectionController
{
    use CrudTrait;

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
    public function getSection(string $classname = '', string $parent_classname = '', int $id = null, bool $allow_actions = true)
    {
        $parentEntity = false;
        if ($id) $parentEntity = $this->manager->getRepository(CrudHelper::getAbsoluteClassName($parent_classname))->find($id);
        $crud_def = $this->yamlCrudTranslator->getCrudDefByClassName($classname);
        $parent_crud_def = $this->getCrudDefinition($parent_classname);
        $list = $this->getEntityInstance($parent_crud_def, $id);
        $attrName = '';
        foreach ($parent_crud_def->getAttributes() as $attr) {
            if ($attr->getEntityRelation() == $classname) {
                $attrName = $attr->getName();
                if ($parentEntity) $id = $parentEntity->{'get' . ucwords($attr->getName())}()->getId();
            }
        }

        return new Response($this->template->render('@EasyCrud/section/section_view.html.twig', ['id' => $id, 'allow_actions' => $allow_actions, 'parent_classname' => $parent_classname, 'crud_def' => $crud_def]));
    }
}