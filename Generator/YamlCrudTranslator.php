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
     * @param CrudDefinition $crudDef
     */
    public function save(CrudDefinition $crudDef): void
    {
        $this->createDirIfNotExist($this->root);
        $attributes = false;
        $yamlData = [
            'class_name' => $crudDef->getClassName(),
            'entity_name' => $crudDef->getEntityName(),
            'label' => $crudDef->getLabel(),
            'visible' => $crudDef->isVisible(),
            'edit' => $crudDef->isEdit(),
            'remove' => $crudDef->isRemove(),
            'list' => $crudDef->isList(),
            'order' => $crudDef->getOrder(),
            'prefix' => $crudDef->getPrefix(),
            'add' => $crudDef->isAdd()
        ];
        if (count($crudDef->getAttributes()) > 0) {
            foreach ($crudDef->getAttributes() as $attribute) {
                $attributeArray = [
                    'name' => $attribute->getName(),
                    'type' => $attribute->getType(),
                    'label' => $attribute->getLabel(),
                    'entity_relation' => $attribute->getEntityRelation(),
                    'size' => $attribute->getSize(),
                    'order' => $attribute->getOrder(),
                    'visible' => $attribute->isVisible(),
                    'nullable' => $attribute->isNullable()
                ];
                $attributes[] = $attributeArray;
            }
        }
        $yamlData['attributes'] = $attributes;
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
                    $crudDef->setVisible($entityData['visible']);
                    $crudDef->setRemove($entityData['remove']);
                    $crudDef->setOrder($entityData['order']);
                    $crudDef->setEntityName($entityData['entity_name']);
                    $crudDef->setEdit($entityData['edit']);
                    $crudDef->setPrefix($entityData['prefix']);
                    $crudDef->setList($entityData['list']);
                    $crudDef->setAdd($entityData['add']);
                    // Attributes list was useless in this case

                    $data[] = $crudDef;
                }
            }
        }
        return $data;
    }

    /**
     * @param string $className
     * @return CrudDefinition
     */
    public function getCrudDefByClassName(string $className): CrudDefinition
    {
        $fileName = ucfirst($className) . '.yaml';
        $dataFile = Yaml::parseFile($this->root . DIRECTORY_SEPARATOR . $fileName);
        if (!$dataFile)
        {
            throw new \Exception("Any entity definition of EasyCrudBundle corespond to " . $className . " classname",500);
        }
        $crudDef = new CrudDefinition();
        $crudDef->setList($dataFile['list']);
        $crudDef->setPrefix($dataFile['prefix']);
        $crudDef->setEdit($dataFile['edit']);
        $crudDef->setEntityName($dataFile['entity_name']);
        $crudDef->setClassName($dataFile['class_name']);
        $crudDef->setRemove($dataFile['remove']);
        $crudDef->setOrder($dataFile['order']);
        $crudDef->setVisible($dataFile['visible']);
        $crudDef->setLabel($dataFile['label']);
        $crudDef->setAdd($dataFile['add']);
        $attributes = [];
        foreach ($dataFile['attributes'] as $attribute) {
            $attrDef = new AttributeDefinition();
            $attrDef->setLabel($attribute['label']);
            $attrDef->setVisible($attribute['visible']);
            $attrDef->setOrder($attribute['order']);
            $attrDef->setType($attribute['type']);
            $attrDef->setNullable($attribute['nullable']);
            $attrDef->setName($attribute['name']);
            $attrDef->setEntityRelation($attribute['entity_relation']);
            $attrDef->setSize($attribute['size']);
            $crudDef->addAttributes($attrDef);
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