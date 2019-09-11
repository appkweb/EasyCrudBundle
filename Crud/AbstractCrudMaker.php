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

namespace Appkweb\Bundle\EasyCrudBundle\Crud;

use Appkweb\Bundle\EasyCrudBundle\Form\Crud\CrudMakerType;
use Appkweb\Bundle\EasyCrudBundle\Generator\YamlCrudTranslatorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Class CrudMaker
 * @package Appkweb\Bundle\EasyCrudBundle\Crud
 */
abstract class AbstractCrudMaker
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param string $classname
     * @return FormInterface
     */
    protected function buildForm(CrudDefinition $crudDefinition): FormInterface
    {
        return $form;
    }
}
