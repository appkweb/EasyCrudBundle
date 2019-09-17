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

namespace Appkweb\Bundle\EasyCrudBundle\Form\Generator;

use Appkweb\Bundle\EasyCrudBundle\Crud\AttributeDefinition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AttributeDefType
 * @package Appkweb\Bundle\EasyCrudBundle\Form\Generator
 */
class AttributeDefType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Attribute name', 'required' => true, 'attr' => ['class' => 'form-control']])
            ->add('type', ChoiceType::class, [
                'label' => 'Attribute type',
                'choices' => AttributeDefinition::ATTRIBUTE_TYPES,
                'attr' => ['class' => 'form-control'],
                'required' => true
            ])
            ->add('extension', ChoiceType::class, [
                'label' => 'Extensions allowed',
                'choices' => AttributeDefinition::FILE_EXTENSIONS,
                'attr' => ['class' => 'selectpicker','multiple' => true],
                'required' => true
            ])
            ->add('entity', ChoiceType::class, [
                'label' => 'Entity linked',
                'choices' => [],
                'attr' => ['class' => 'form-control']
            ])
            ->add('label', TextType::class, ['label' => 'Label of attribute', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('order', NumberType::class, ['label' => 'Order of print', 'required' => true, 'attr' => ['class' => 'form-control']])
            ->add('visible', CheckboxType::class, ['label' => 'Attribute can be visible ?', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('nullable', CheckboxType::class, ['label' => 'Can be null ?', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('length', NumberType::class, ['label' => 'Size', 'required' => false, 'attr' => ['class' => 'form-control']]);
    }
}