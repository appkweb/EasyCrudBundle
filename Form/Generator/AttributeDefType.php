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
use Appkweb\Bundle\EasyCrudBundle\Generator\YamlCrudTranslatorInterface;
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
    /**
     * @var YamlCrudTranslatorInterface
     */
    private $yamlCrudTranslator;

    public function __construct(YamlCrudTranslatorInterface $yamlCrudTranslator)
    {
        $this->yamlCrudTranslator = $yamlCrudTranslator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attributeTypes = AttributeDefinition::ATTRIBUTE_TYPES;
        $entityList = $this->yamlCrudTranslator->getEntities();
        $entityNameList = [];
        if (count($entityList) < 1) {
            unset($attributeTypes['Simple select']);
            unset($attributeTypes['Add list']);
            unset($attributeTypes['Multiple select']);
        } else {
            foreach ($entityList as $entity) {
                $entityNameList[$entity->getClassName()] = $entity->getClassName();
            }
        }
        $builder
            ->add('name', TextType::class, ['label' => 'Attribute name', 'required' => true, 'attr' => ['class' => 'form-control']])
            ->add('type', ChoiceType::class, [
                'label' => 'Attribute type',
                'choices' => $attributeTypes,
                'attr' => ['class' => 'form-control'],
                'required' => true
            ])
            ->add('entity', ChoiceType::class, [
                'label' => 'Entity linked',
                'choices' => $entityNameList,
                'attr' => ['class' => 'form-control']
            ])
            ->add('label', TextType::class, ['label' => 'Label of attribute', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('order', NumberType::class, ['label' => 'Order of print', 'required' => true, 'attr' => ['class' => 'form-control']])
            ->add('show', CheckboxType::class, ['label' => 'Attribute can be showed ?', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('list', CheckboxType::class, ['label' => 'Attribute can be listed ?', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('edit', CheckboxType::class, ['label' => 'Attribute can be edited ?', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('unique', CheckboxType::class, ['label' => 'Attribute is unique ?', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('nullable', CheckboxType::class, ['label' => 'Attribute can be null ?', 'required' => false, 'attr' => ['class' => 'form-control']]);
    }
}