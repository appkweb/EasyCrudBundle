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

namespace Appkweb\Bundle\EasyCrudBundle\Validator;

use Appkweb\Bundle\EasyCrudBundle\Crud\CrudDefinition;

/**
 * Class CrudValidator
 * @package Appkweb\Bundle\EasyCrudBundle\Validator
 */
interface CrudValidatorInterface
{
    /**
     * @param CrudDefinition $crudDefinition
     * @param array $data
     * @return array
     */
    public function validate(CrudDefinition $crudDefinition, $data = [],$id = false);

    /**
     * @return array
     */
    public function getErrors(): array;

    /**
     * @return bool
     */
    public function isValid(): bool;
}