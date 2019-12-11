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

use Appkweb\Bundle\EasyCrudBundle\Crud\AttributeDefinition;
use Appkweb\Bundle\EasyCrudBundle\Utils\CrudHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            /* @var AttributeDefinition $attribute */
            $args = [
                'required' => false,
                'label' => ucfirst($attribute->getLabel()),
                'data_class' => null,
                'attr' => ['class' => 'form-control','data-type' => $attribute->getType()]
            ];
            switch ($attribute->getType()) {
                case 'Simple select':
                    $args['class'] = 'App\Entity\\' . $attribute->getEntityRelation();
                    break;
                case 'Simple image picker':
                    $args['attr']['class'] = "file-input";
                    break;
                case 'TinyMce':
                    $args['attr']['class'] = "tinymce";
                    break;
                case "Date picker":
                    $args['attr']['class'] = "datepicker form-control";
                    break;
            }
            if ($attribute->getType() != "Add list" && $attribute->getType() != "Section") {
                $builder->add($attribute->getName(), CrudHelper::getStrFormType($attribute), $args);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => false,
        ]);
    }
}