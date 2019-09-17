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
use Appkweb\Bundle\EasyCrudBundle\Type\TinymceType;
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
            case $type == "Simple input text":
                return TextType::class;
            case $type == "Number":
                return NumberType::class;
            case $type == "TextArea" :
                return TextareaType::class;
            case $type == "TinyMce" :
                return TinymceType::class;
            case $type == "Simple filepicker" :
                return FileType::class;
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