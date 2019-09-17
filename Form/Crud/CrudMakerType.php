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

namespace Appkweb\Bundle\EasyCrudBundle\Form\Crud;

use Appkweb\Bundle\EasyCrudBundle\Utils\CrudHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;


/**
 * Class CrudMakerType
 * @package Appkweb\Bundle\EasyCrudBundle\Form\Crud
 */
class CrudMakerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param CrudDefinition $crudDefinition
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $crudDefinition = $options['data']['crud_def'];
        foreach ($crudDefinition->getAttributes() as $attribute) {
            $builder->add($attribute->getName(), CrudHelper::getStrFormType($attribute), [
                'required' => (!$attribute->isNullable()),
                'attr' => ['class' => 'form-control'],
                'label' => $attribute->getLabel()
            ]);
        }
    }
}