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


namespace Appkweb\Bundle\EasyCrudBundle\Generator;

use Appkweb\Bundle\EasyCrudBundle\Crud\AttributeDefinition;
use Appkweb\Bundle\EasyCrudBundle\Crud\CrudDefinition;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlCrudTranslator
 * @package Appkweb\Bundle\EasyCrudBundle\Generator
 */
final class YamlCrudTranslator implements YamlCrudTranslatorInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    private $root;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->root = $this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'crud_definition';
    }

    /**
     * @param string $className
     */
    public function remove(string $className): void
    {
        unlink($this->root . DIRECTORY_SEPARATOR . $className . ".yaml");
    }

    /**
     * @param CrudDefinition $crudDef
     */
    public function save(CrudDefinition $crudDef): void
    {
        $this->createDirIfNotExist($this->root);
        $attributes = false;
        $yamlData = [
            'class_name' => $crudDef->getClassName(),
            'singular_label' => $crudDef->getSingularLabel(),
            'plurial_label' => $crudDef->getPlurialLabel(),
            'label' => $crudDef->getLabel(),
            'visible' => $crudDef->isVisible(),
            'edit' => $crudDef->isEdit(),
            'remove' => $crudDef->isRemove(),
            'list' => $crudDef->isList(),
            'order' => $crudDef->getOrder(),
            'add' => $crudDef->isAdd(),
            'show' => $crudDef->isShow(),
            'referrer' => $crudDef->getReferrer()
        ];
        if (count($crudDef->getAttributes()) > 0) {
            foreach ($crudDef->getAttributes() as $attribute) {
                $attributeArray = [
                    'name' => $attribute->getName(),
                    'type' => $attribute->getType(),
                    'label' => $attribute->getLabel(),
                    'show' => $attribute->isShow(),
                    'list' => $attribute->isList(),
                    'edit' => $attribute->isEdit(),
                    'unique' => $attribute->isUnique(),
                    'nullable' => $attribute->isNullable(),
                    'order' => $attribute->getOrder(),
                    'entity_relation' => $attribute->getEntityRelation()
                ];
                $attributes[] = $attributeArray;
            }
        }
        $yamlData['attributes'] = $attributes;
        if ($attributes != false) {
            usort($yamlData['attributes'], function ($a, $b) {
                if (is_array($a)) {
                    return $a['order'] <=> $b['order'];
                }
                return $a->getOrder() <=> $b->getOrder();
            });
        }
        file_put_contents($this->root . DIRECTORY_SEPARATOR . $crudDef->getClassName() . ".yaml", Yaml::dump($yamlData));
    }

    /**
     * @return array
     */
    public function getEntities(): array
    {
        $data = [];
        if (is_dir($this->root)) {
            $list = scandir($this->root);
            foreach ($list as $file) {
                if ($file != '..' && $file != '.') {
                    $entityData = Yaml::parseFile($this->root . DIRECTORY_SEPARATOR . $file);
                    $crudDef = new CrudDefinition();
                    $crudDef->setClassName($entityData['class_name']);
                    $crudDef->setLabel($entityData['label']);
                    $crudDef->setSingularLabel($entityData['singular_label']);
                    $crudDef->setPlurialLabel($entityData['plurial_label']);
                    $crudDef->setVisible($entityData['visible']);
                    $crudDef->setRemove($entityData['remove']);
                    $crudDef->setOrder($entityData['order']);
                    $crudDef->setEdit($entityData['edit']);
                    $crudDef->setReferrer($entityData['referrer']);
                    $crudDef->setList($entityData['list']);
                    $crudDef->setAdd($entityData['add']);
                    $crudDef->setShow($entityData['show']);
                    // Attributes list was useless in this case

                    $data[] = $crudDef;
                }
            }
        }
        usort($data, function ($a, $b) {
            return $a->getOrder() <=> $b->getOrder();
        });
        return $data;
    }


    /**
     * @param string $className
     * @return CrudDefinition
     */
    public function getCrudDefByClassName(string $className): CrudDefinition
    {
        if (!$className) throw new \Exception("classname param is missing !", 500);
        $fileName = $className . '.yaml';
        $dataFile = Yaml::parseFile($this->root . DIRECTORY_SEPARATOR . $fileName);
        $crudDef = new CrudDefinition();
        $crudDef->setList($dataFile['list']);
        $crudDef->setEdit($dataFile['edit']);
        $crudDef->setClassName($dataFile['class_name']);
        $crudDef->setRemove($dataFile['remove']);
        $crudDef->setOrder($dataFile['order']);
        $crudDef->setVisible($dataFile['visible']);
        $crudDef->setLabel($dataFile['label']);
        $crudDef->setSingularLabel($dataFile['singular_label']);
        $crudDef->setPlurialLabel($dataFile['plurial_label']);
        $crudDef->setAdd($dataFile['add']);
        $crudDef->setShow($dataFile['show']);
        $crudDef->setReferrer($dataFile['referrer']);
        $attributes = [];
        if ($dataFile['attributes']) {
            foreach ($dataFile['attributes'] as $attribute) {
                $attrDef = new AttributeDefinition();
                $attrDef->setLabel($attribute['label']);
                $attrDef->setList($attribute['list']);
                $attrDef->setEdit($attribute['edit']);
                $attrDef->setShow($attribute['show']);
                $attrDef->setUnique($attribute['unique']);
                $attrDef->setOrder($attribute['order']);
                $attrDef->setType($attribute['type']);
                $attrDef->setNullable($attribute['nullable']);
                $attrDef->setName($attribute['name']);
                $attrDef->setEntityRelation($attribute['entity_relation']);
                $crudDef->addAttributes($attrDef);
            }
        }

        return $crudDef;
    }

    /**
     * /!\ Don't forgot to chmod 777 public dir of your symfony project on prod
     * @param $dir -> Directory
     */
    protected function createDirIfNotExist($dir): void
    {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }


}