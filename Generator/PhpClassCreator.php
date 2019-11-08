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
use PhpParser\Node\Stmt\TraitUseAdaptation;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class PhpClassCreator
 * @package Appkweb\Bundle\EasyCrudBundle\Generator
 */
final class PhpClassCreator implements PhpClassCreatorInterface
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var string
     */
    private $relatedAttrName;

    /**
     * @var YamlCrudTranslatorInterface
     */
    private $yamlCrudTranslator;

    public function __construct(KernelInterface $kernel, YamlCrudTranslatorInterface $yamlCrudTranslator)
    {
        $this->kernel = $kernel;
        $this->yamlCrudTranslator = $yamlCrudTranslator;
    }

    /**
     * @param string $classname
     */
    public function removeRelations(string $classname): void
    {
        $crudDefinition = $this->yamlCrudTranslator->getCrudDefByClassName($classname);
        $attributes = $crudDefinition->getAttributes();
        if (count($attributes) > 0)
            foreach ($attributes as $attribute) {
                $entityRelation = $attribute->getEntityRelation();
                if ($entityRelation != false) {
                    $crud = $this->yamlCrudTranslator->getCrudDefByClassName($entityRelation);
                    foreach ($crud->getAttributes() as $key => $item) {
                        unset($crud->getAttributes()[$key]);
                    }
                    $this->yamlCrudTranslator->save($crud);
                    $this->save($crud);
                }
            }
    }


    /**
     * Create php Class by CrudDefinition transmit in param
     * @param CrudDefinition $crudDefinition
     */
    public function save(CrudDefinition $crudDefinition, $oldClassName = "false"): void
    {
        $pathFile = $this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Entity' . DIRECTORY_SEPARATOR . $crudDefinition->getClassName() . '.php';
        $oldContent = $this->getOldCustomMethods($pathFile);
        if ($oldClassName != "false" && $oldClassName != $crudDefinition->getClassName()) {
            $this->removeRelations($oldClassName);
            $this->yamlCrudTranslator->remove($oldClassName);
            $this->remove($oldClassName);
        }


        // Entity header
        $fileContent = "<?php \n\n";
        $fileContent .= "namespace App\Entity; \n\n";
        $fileContent .= "use Doctrine\ORM\Mapping as ORM; \n";
        $fileContent .= "use Symfony\Component\Validator\Constraints as Assert; \n\n";
        $annotations = [
            'Class ' . $crudDefinition->getClassName(),
            '',
            '@ORM\Entity(repositoryClass="' . 'App\Repository\\' . $crudDefinition->getClassName() . 'Repository' . '")',
            '@ORM\HasLifecycleCallbacks'
        ];
        $fileContent .= $this->getAnnotation($annotations);
        $fileContent .= "class " . $crudDefinition->getClassName() . "\n{\n";
        if ($oldContent) {
            $fileContent .= $oldContent;
        }
        $fileContent .= '    /* Don\'t write anything above this line */' . "\n\n";

        $getterOfToString = '(string)$this->get' . ucfirst($crudDefinition->getReferrer()) . '();';
        $fileContent .= "    public function __toString()\n    {\n        return " . $getterOfToString . "\n    }\n\n";


        // Id of entity
        $annotations = [
            "@var int",
            "",
            '@ORM\\Column(type="integer")',
            '@ORM\\Id',
            '@ORM\\GeneratedValue(strategy="AUTO")'
        ];
        $fileContent .= $this->getAnnotation($annotations, 4);
        $fileContent .= '    private $id;' . "\r\r";

        // Attributes of entity
        foreach ($crudDefinition->getAttributes() as $attribute) {
            $fileContent .= $this->getAttributeToStr($crudDefinition, $attribute);
        }
        $fileContent .= "\n}";
        if (file_exists($pathFile)) {
            unlink($pathFile);
        }
        $file = fopen($pathFile, 'w');
        fwrite($file, $fileContent);
        fclose($file);
        $this->createEntityRepoIfNotExist($crudDefinition->getClassName());
    }

    /**
     * @param string $sfType
     * @return string
     */
    public function getPhpType(string $sfType): string
    {
        switch (true) {
            case $sfType == 'Simple input text' || $sfType == 'TextArea' || $sfType == "TinyMce" || $sfType == "Simple image picker" :
                return 'string';
                break;
            case $sfType == 'Number':
                return 'int';
                break;
            case $sfType == "Date picker":
                return '\DateTime';
            default :
                return $sfType;
                break;
        }
    }

    /**
     * @param string $sfType
     * @return string
     */
    public function getOrmType(string $sfType): string
    {
        switch (true) {
            case $sfType == 'Simple input text' || $sfType == 'Simple image picker':
                return 'string';
                break;
            case $sfType == 'TextArea' || $sfType == "TinyMce" :
                return 'text';
                break;
            case $sfType == 'Number':
                return 'integer';
                break;
            case $sfType = 'Date picker':
                return 'datetime';
                break;
            default :
                throw new \Exception("Type does not exist", 500);
                break;
        }
    }

    /**
     * @param string $className
     */
    public function remove(string $className): void
    {
        unlink($this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Entity' . DIRECTORY_SEPARATOR . $className . '.php');
        unlink($this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Repository' . DIRECTORY_SEPARATOR . $className . 'Repository.php');
    }

    /**
     * @param array $content
     * @param bool $space
     * @return string
     */
    public function getAnnotation(array $content, $space = false): string
    {
        $ind = 0;
        $annotation = '';
        $annotationSpace = '';
        if ($space) {
            while ($space > $ind) {
                $annotationSpace .= ' ';
                $ind++;
            }
        }
        $annotation .= $annotationSpace . "/**\n";
        foreach ($content as $line) {
            $annotation .= $annotationSpace . " * " . $line . "\n";
        }
        $annotation .= $annotationSpace . " */\n";
        return $annotation;
    }

    /**
     * @param string $name
     * @return string
     */
    public function replaceUpperCaseByUnderscore(string $name): string
    {
        $ind = 0;
        $indBis = 0;
        $newName = '';
        if (preg_match("#[A-Z]#", $name[0])) {
            $name[0] = strtolower($name[0]);
        }
        while (strlen($name) > $ind) {
            if (preg_match("#[A-Z]#", $name[$ind]) && $ind != 0) {
                $newName[$indBis + 1] = strtolower($name[$ind]);
                $newName[$indBis] = '_';
                $indBis++;
            } else {
                $newName[$indBis] = $name[$ind];
            }
            $ind++;
            $indBis++;
        }
        return $newName;
    }

    /**
     * @param string $name
     */
    public function createEntityRepoIfNotExist(string $name): void
    {
        $name = ucfirst($name) . 'Repository';
        $pathFile = $this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Repository' . DIRECTORY_SEPARATOR . $name . '.php';
        if (!file_exists($pathFile)) {
            $fileContent = "<?php \n\n";
            $fileContent .= "namespace App\Repository; \n\n";
            $fileContent .= "use Doctrine\ORM\EntityRepository; \n\n";
            $fileContent .= "class " . $name . " extends EntityRepository\n{\n";
            $fileContent .= "\n}";

            $file = fopen($pathFile, 'w');
            fwrite($file, $fileContent);
            fclose($file);
        }
    }

    // Internal methods

    /**
     * Return old custom medthods
     * @param $path
     * @return bool|string
     */
    protected function getOldCustomMethods($path)
    {
        $content = '';
        $ind = 0;
        $stop = false;
        $start = false;
        if (file_exists($path)) {
            $file = fopen($path, 'r');
            while (($line = fgets($file)) !== false && !$stop) {
                if ($line == "    /* Don't write anything above this line */\n") {
                    $stop = true;
                }
                if ($line == "{\n" || $start) {
                    if ($start && !$stop) {
                        $content .= $line;
                    }
                    $start = true;
                }
                $ind++;
            }
        } else {
            return false;
        }
        fclose($file);
        return $content;
    }

    /**
     * @param AttributeDefinition $attribute
     * @return string
     */
    protected function getAttributeToStr(CrudDefinition $crudDefinition, AttributeDefinition $attribute): string
    {
        $type = $attribute->getType();
        $nullable = $attribute->isNullable();
        $unique = $attribute->isUnique();
        $nullable = $nullable ? "true" : "false";
        $unique = $unique ? "true" : "false";
        $column = '';
        $entityRelation = $attribute->getEntityRelation();
        $column = '(type="' . $this->getOrmType($type) . '",nullable=' . $nullable . ',unique=' . $unique . ')';
        $related = false;
        if ($attribute->getType() == 'Add list') {
            $related = strtolower($crudDefinition->getClassName());
        } elseif ($this->relatedAttrName != "false") {
            $related = $this->relatedAttrName;
        }

        $data = [
            '@var ' . $this->getPhpType($type),
            '@ORM\Column' . $column
        ];
        switch (true) {
            case $type === 'Simple image picker':
                $data[] = '@Assert\File(mimeTypes={"image/jpeg","image/gif","image/png"}';
                $data[] = '     ,maxSize = "1024k", mimeTypesMessage = "Veuillez selectionner une image valide (PNG/JPEG) "';
                $data[] = '     ,maxSize= "1024k",maxSizeMessage = "Veuillez selectionner une image moins volumineuse (1 Mo maximum)"';
                $data[] = '     )';
                break;
            case $type === "Simple select":
                if ($related) {
                    $data[] = "@ORM\ManyToOne(targetEntity=\"App\Entity\\$entityRelation\",inversedBy=\"$related\")";
                } else {
                    $data[] = "@ORM\ManyToOne(targetEntity=\"App\Entity\\$entityRelation\")";
                }
                unset($data[0]); // remove "@var type"
                unset($data[1]); // remove "@Column"
                break;
            case $type === "Add list":
                $this->relatedAttrName = $attribute->getName();
                $this->createManyToOneIfNotExist($entityRelation, $crudDefinition->getClassName());
                $data[] = "@ORM\OneToMany(targetEntity=\"App\Entity\\$entityRelation\",mappedBy=\"$related\")";
                unset($data[0]); // remove "@var type"
                unset($data[1]); // remove "@Column"
                break;
        }

        $str = $this->getAnnotation($data, 4);
        $str .= "    private $" . $attribute->getName() . ";\n\n";

        return $str;
    }

    /**
     * @param string $name
     * @param string $relationClassName
     */
    protected function createManyToOneIfNotExist(string $name, string $relationClassName): void
    {
        $crudDef = $this->yamlCrudTranslator->getCrudDefByClassName($name);
        $crudDefRelated = $this->yamlCrudTranslator->getCrudDefByClassName($relationClassName);
        $exist = false;
        foreach ($crudDef->getAttributes() as $key => $attribute) {
            if ($attribute->getName() == strtolower($relationClassName)) {
                unset($crudDef->getAttributes()[$key]);
                break;
            }
        }

        $relatedAttribute = new AttributeDefinition();
        $relatedAttribute->setUnique(false);
        $relatedAttribute->setShow(true);
        $relatedAttribute->setEdit(true);
        $relatedAttribute->setList(true);
        $relatedAttribute->setEntityRelation(ucfirst($relationClassName));
        $relatedAttribute->setLabel($crudDefRelated->getLabel());
        $relatedAttribute->setNullable(false);
        $relatedAttribute->setName(strtolower($crudDefRelated->getClassName()));
        $relatedAttribute->setType('Simple select');
        $relatedAttribute->setOrder(0);
        $crudDef->addAttributes($relatedAttribute);

        $this->yamlCrudTranslator->save($crudDef);
        $this->save($crudDef);
    }

}