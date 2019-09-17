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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class MakerType
 * @package Appkweb\Bundle\EasyCrudBundle\Form\Generator
 */
class EntityDefType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('className', TextType::class, ['label' => 'Name of class','required' => false,'attr' => ['class' => 'form-control']])
            ->add('entityName', TextType::class, ['label' => 'Name of entity','required' => true,'attr' => ['class' => 'form-control']])
            ->add('label', TextType::class, ['label' => 'Label to generate crud title','required' => true,'attr' => ['class' => 'form-control']])
            ->add('prefix', TextType::class, ['label' => 'Prefix of crud path','required' => false,'attr' => ['class' => 'form-control']])
            ->add('order', NumberType::class, ['label' => 'Order of print','required' => false,'attr' => ['class' => 'form-control']])
            ->add('visible', CheckboxType::class, ['label' => 'Menu of entity is visible ?','required' => false,'attr' => ['class' => 'form-control']])
            ->add('remove', CheckboxType::class, ['label' => 'Values can be removed ?','required' => false,'attr' => ['class' => 'form-control']])
            ->add('list', CheckboxType::class, ['label' => 'Values can be listed ?','required' => false,'attr' => ['class' => 'form-control']])
            ->add('edit', CheckboxType::class, ['label' => 'Values can be edited ?','required' => false,'attr' => ['class' => 'form-control']])
            ->add('add', CheckboxType::class, ['label' => 'Values can be added ?','required' => false,'attr' => ['class' => 'form-control']])
            ->add('show', CheckboxType::class, ['label' => 'Values can be showed ?','required' => false,'attr' => ['class' => 'form-control']]);
    }
}