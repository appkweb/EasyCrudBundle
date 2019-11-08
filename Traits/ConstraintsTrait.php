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

namespace Appkweb\Bundle\EasyCrudBundle\Traits;

use Appkweb\Bundle\EasyCrudBundle\Crud\AttributeDefinition;
use Appkweb\Bundle\EasyCrudBundle\Crud\CrudDefinition;
use Appkweb\Bundle\EasyCrudBundle\Utils\CrudHelper;
use Appkweb\Bundle\EasyCrudBundle\Validator\CrudValidator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Trait ConstraintsTrait
 * @package Appkweb\Bundle\EasyCrudBundle\Traits
 */
trait ConstraintsTrait
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var CrudDefinition
     */
    private $crudDef;

    /**
     * @var AttributeDefinition
     */
    private $attributeDefinition;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    private $value;
    private $type;
    private $id;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    /**
     *
     */
    protected function notBlankConstraint(): void
    {
        if (!$this->attributeDefinition->isNullable() && ($this->value == '' || $this->value == null)) {
            $this->errors[$this->attributeDefinition->getName()]['not_blank'] = CrudValidator::FIELD_BLANK_ERROR_MSG;
        }
    }

    /**
     *
     */
    protected function specialCharConstraint(): void
    {
        if ($this->type != 'TinyMce' && (preg_match("/<[^>]+>/", $this->value) || preg_match("/<[^>]+>/", $this->value))) {
            $this->errors[$this->attributeDefinition->getName()]['special_char'] = CrudValidator::FIELD_FORBIDEN_SPECIAL_CHAR_ERROR_MSG;
        }
    }

    /**
     *
     */
    protected function unsafeUrlConstraint(): void
    {
        if (strpos($this->value, 'http://') !== false) {
            $this->errors[$this->attributeDefinition->getName()]['unsafe_url'] = CrudValidator::FIELD_FORBIDEN_UNSAFE_URL_MSG;
        }
    }

    /**
     *
     */
    protected function numericConstraint(): void
    {
        if (!is_numeric($this->value) && $this->type == 'Number' && $this->value != '') {
            $this->errors[$this->attributeDefinition->getName()]['not_numeric'] = CrudValidator::FIELD_NUMBER_ERROR_MSG;
        }
    }

    /**
     *
     */
    protected function dateConstraint(): void
    {
        if ($this->type == 'Date picker' && !\DateTime::createFromFormat('d/m/Y', $this->value)) {
            $this->errors[$this->attributeDefinition->getName()]['date_format'] = CrudValidator::FIELD_DATEPICKER_FORMAT_MSG;
        }
    }

    /**
     *
     */
    protected function imgFormatConstraint(): void
    {
        if ($this->value)
        {
            if ($this->type == 'Simple image picker' && $this->value->getMimeType() != 'image/jpeg' && $this->value->getMimeType() != 'image/png' && $this->value != '') {
                $this->errors[$this->attributeDefinition->getName()]['img_file_format'] = CrudValidator::FIELD_FORBIDEN_FILE_FORMAT_MSG;
            }
        }
    }

    /**
     *
     */
    protected function isNotUnique(): void
    {
        if ($this->attributeDefinition->isUnique()) {
            $exist = $this->manager->getRepository(CrudHelper::getAbsoluteClassName($this->crudDef->getClassName()))->findOneBy([$this->attributeDefinition->getName() => $this->value]);
            if (($exist && !$this->id) || ($exist && $this->id && $this->id != $exist->getId()))
            {
                $this->errors[$this->attributeDefinition->getName()]['img_file_format'] = CrudValidator::FIELD_ALREADY_EXIST_MSG;
            }
        }
    }
}