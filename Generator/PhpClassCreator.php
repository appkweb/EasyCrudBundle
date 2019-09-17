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

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Create php Class by CrudDefinition transmit in param
     * @param CrudDefinition $crudDefinition
     */
    public function save(CrudDefinition $crudDefinition): void
    {
        $pathFile = $this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Entity' . DIRECTORY_SEPARATOR . $crudDefinition->getClassName() . '.php';
        $oldContent = $this->getOldCustomMethods($pathFile);
        // Entity header
        $fileContent = "<?php \n\n";
        $fileContent .= "namespace App\Entity; \n\n";
        $fileContent .= "use Doctrine\ORM\Mapping as ORM; \n";
        $fileContent .= "use Symfony\Component\Validator\Constraints as Assert; \n\n";
        $annotations = [
            'Class ' . $crudDefinition->getClassName(),
            '',
            '@ORM\Table(name="' . $crudDefinition->getEntityName() . '")',
            '@ORM\Entity(repositoryClass="' . 'App\Repository\\' . $crudDefinition->getClassName() . 'Repository' . '")',
            '@ORM\HasLifecycleCallbacks'
        ];
        $fileContent .= $this->getAnnotation($annotations);
        $fileContent .= "class " . $crudDefinition->getClassName() . "\n{\n";
        if ($oldContent) {
            $fileContent .= $oldContent;
        }
        $fileContent .= '    /* Don\'t write anything above this line */' . "\n\n";

        // Id of entity
        $annotations = [
            "@var int",
            "",
            '@ORM\\Column(name="id",type="integer")',
            '@ORM\\Id',
            '@ORM\\GeneratedValue(strategy="AUTO")'
        ];
        $fileContent .= $this->getAnnotation($annotations, 4);
        $fileContent .= '    private $id;' . "\r\r";

        // Attributes of entity
        foreach ($crudDefinition->getAttributes() as $attribute) {
            $fileContent .= $this->getAttributeToStr($attribute);
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
            case $sfType == 'Simple input text' || $sfType == 'TextArea' || $sfType == "TinyMce" || $sfType == "Simple filepicker" :
                return 'string';
                break;
            case $sfType == 'Number':
                return 'int';
                break;
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
            case $sfType == 'Simple input text' || $sfType == 'Simple filepicker' || $sfType == "Simple filepicker" :
                return 'string';
                break;
            case $sfType == 'TextArea' || $sfType == "TinyMce" :
                return 'text';
                break;
            case $sfType == 'Number':
                return 'int';
                break;
            default :
                return $sfType;
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

        if (file_exists($path)) {
            $file = fopen($path, 'r');
            while (($line = fgets($file)) !== false && !$stop) {
                if ($line == "    /* Don't write anything above this line */\n") {
                    $stop = true;
                }
                if ($ind >= 16 && !$stop) {
                    $content .= $line;
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
    protected function getAttributeToStr(AttributeDefinition $attribute): string
    {
        $name = $this->replaceUpperCaseByUnderscore($attribute->getName());
        $type = $attribute->getType();
        $size = $attribute->getSize();
        $nullable = $attribute->isNullable();
        if ($nullable) {
            $nullable = "true";
        } else {
            $nullable = "false";
        }
        $column = '';
        switch (true) {
            case $type === 'string':
                $column = '(type="string", length=' . $size . ',nullable=' . $nullable . ')';
                break;
            default:
                $column = '(type="' . $this->getOrmType($type) . '",nullable=' . $nullable . ')';
                break;
        }
        $data = [
            '@var ' . $this->getPhpType($type),
            '@ORM\Column' . $column
        ];

        if ($type === "Simple filepicker") {
            $strExtension = '';
            foreach ($attribute->getExtension() as $key => $item) {
                if ($key + 1 == count($attribute->getExtension())) {
                    $strExtension .= '"' . $item . '"';
                } else {
                    $strExtension .= '"' . $item . '",';
                }
            }
            $data[] = '@Assert\File(mimeTypes={' . $strExtension . '})';
        }

        $str = $this->getAnnotation($data, 4);
        $str .= "    private $" . $attribute->getName() . ";\n";

        return $str;
    }
}