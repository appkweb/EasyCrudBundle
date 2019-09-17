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

use Appkweb\Bundle\EasyCrudBundle\Crud\CrudDefinition;
use Appkweb\Bundle\EasyCrudBundle\Form\Crud\CrudMakerType;
use Appkweb\Bundle\EasyCrudBundle\Providers\GalleryInterface;
use Appkweb\Bundle\EasyCrudBundle\Utils\CrudHelper;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Trait CrudTrait
 * @package Appkweb\Bundle\EasyCrudBundle\Traits
 */
trait CrudTrait
{
    /**
     * @param FormInterface $form
     * @throws \ReflectionException
     */
    protected function save(FormInterface $form): void
    {
        $objectToSave = $this->hydrateObject($form);
        $this->manager->persist($objectToSave);
        $this->manager->flush();
    }

    /**
     * @param $form
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    protected function hydrateObject($form)
    {
        $datas = $form->getData();
        $objInstance = CrudHelper::getNewInstanceOf($datas['crud_def']->getClassName());
        foreach ($datas as $key => $data) {
            if ($key != "crud_def") {
                if ($data instanceof UploadedFile) {
                    $data = $this->gallery->upload($data);
                }
                $objInstance->{'set' . ucwords($key)}($data);
            }
        }
        return $objInstance;
    }

    /**
     * @param CrudDefinition $crudDefinition
     * @return FormInterface
     */
    protected function getForm(CrudDefinition $crudDefinition): FormInterface
    {
        $data = ['crud_def' => $crudDefinition];
        $form = $this->formFactory->create(CrudMakerType::class, $data);
        return $form;
    }
}