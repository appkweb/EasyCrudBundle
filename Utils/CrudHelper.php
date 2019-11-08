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

namespace Appkweb\Bundle\EasyCrudBundle\Utils;

use Appkweb\Bundle\EasyCrudBundle\Crud\AttributeDefinition;
use Appkweb\Bundle\EasyCrudBundle\Crud\CrudDefinition;
use Appkweb\Bundle\EasyCrudBundle\Type\CalendarType;
use Appkweb\Bundle\EasyCrudBundle\Type\EasyCrudFileType;
use Appkweb\Bundle\EasyCrudBundle\Type\TinymceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class CrudHelper
 * @package Appkweb\Bundle\EasyCrudBundle\Utils
 */
class CrudHelper
{
    /**
     * @param AttributeDefinition $attributeDefinition
     * @return string
     */
    public static function getStrFormType(AttributeDefinition $attributeDefinition): string
    {
        $type = $attributeDefinition->getType();
        switch (true) {
            case $type == "Simple input text" || $type == "Date picker" || $type == 'Number':
                return TextType::class;
            case $type == "TextArea" || $type == "TinyMce" :
                return TextareaType::class;
            case $type == "Simple image picker" :
                return EasyCrudFileType::class;
            case $type == "Simple select":
                return EntityType::class;
        }
    }

    /**
     * This function return an empty instance of entity
     * @param string $className
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    public static function getNewInstanceOf(string $className)
    {
        $r = new \ReflectionClass("App\\Entity\\" . $className);
        return $r->newInstance();
    }

    /**
     * @param string $classname
     * @return string
     */
    public static function getAbsoluteClassName(string $classname)
    {
        return "App\\Entity\\" . ucfirst($classname);
    }
}