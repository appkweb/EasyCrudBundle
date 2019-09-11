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

interface YamlCrudTranslatorInterface
{
    /**
     * This function write CrudDefinition into a yaml file
     * @param CrudDefinition $crudDef
     * @return mixed
     */
    public function save(CrudDefinition $crudDef);

    /**
     * This function return entity list created with EasyCrudBundle
     * @return array
     */
    public function getEntities(): array;

    /**
     * @param string $className
     * @return CrudDefinition
     */
    public function getCrudDefByClassName(string $className): CrudDefinition;
}