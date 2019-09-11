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

use Appkweb\Bundle\EasyCrudBundle\Crud\CrudDefinition;
use PhpParser\Node\Stmt\TraitUseAdaptation;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Interface PhpClassCreator
 * @package Appkweb\Bundle\EasyCrudBundle\Generator
 */
interface PhpClassCreatorInterface
{
    /**
     * @param CrudDefinition $crudDefinition
     */
    public function save(CrudDefinition $crudDefinition): void;

    /**
     * @param string $sfType
     * @return string
     */
    public function getPhpType(string $sfType): string;

    /**
     * @param array $content
     * @param bool $space
     * @return string
     */
    public function getAnnotation(array $content, $space = false): string;

    /**
     * @param string $name
     * @return string
     */
    public function replaceUpperCaseByUnderscore(string $name): string;

    /**
     * @param string $name
     */
    public function createEntityRepoIfNotExist(string $name): void;

}