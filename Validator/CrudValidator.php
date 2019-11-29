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

use Appkweb\Bundle\EasyCrudBundle\Crud\AttributeDefinition;
use Appkweb\Bundle\EasyCrudBundle\Crud\CrudDefinition;
use Appkweb\Bundle\EasyCrudBundle\Traits\ConstraintsTrait;

/**
 * Class CrudValidator
 * @package Appkweb\Bundle\EasyCrudBundle\Validator
 */
class CrudValidator implements CrudValidatorInterface
{
    use ConstraintsTrait;

    const FIELD_BLANK_ERROR_MSG = 'Veuillez remplir ce champs';
    const FIELD_NUMBER_ERROR_MSG = 'Ce champs peut uniquement contenir des chiffres de 0 à 9';
    const FIELD_FORBIDEN_SPECIAL_CHAR_ERROR_MSG = 'Ce champs contient des caractères invalides';
    const FIELD_FORBIDEN_UNSAFE_URL_MSG = 'Ce champs contient un url(http://) non sécurisé';
    const FIELD_DATEPICKER_FORMAT_MSG = 'Ce champs doit contenir une date au format JOUR/MOIS/ANNEE';
    const FIELD_FORBIDEN_FILE_FORMAT_MSG = 'Ce champs doit contenir un fichier au format PNG/JPG uniquement';
    const FIELD_ALREADY_EXIST_MSG = 'Ce champs doit être unique. Un enregistrement possède déjà cette valeur';

    /**
     * @param CrudDefinition $crudDefinition
     * @param array $data
     * @param int|null $id
     * @param bool $checkUnique
     * @return array|void
     */
    public function validate(CrudDefinition $crudDefinition, array $data = [], int $id = null, bool $checkUnique = true)
    {
        $this->id = $id;
        foreach ($crudDefinition->getAttributes() as $attribute) {
            if ($attribute->getType() == "Section") { // valid fields of section type
                $crudDefSection = $this->yamlCrudTranslator->getCrudDefByClassName($attribute->getEntityRelation());
                $this->validate($crudDefSection, $data, null, false);
            }
            if (array_key_exists($attribute->getName(), $data)) {
                $this->crudDef = $crudDefinition;
                $this->value = $data[$attribute->getName()];
                $this->type = $attribute->getType();
                $this->attributeDefinition = $attribute;
                $this->notBlankConstraint();
                $this->specialCharConstraint();
                $this->unsafeUrlConstraint();
                $this->numericConstraint();
                $this->dateConstraint();
                $this->imgFormatConstraint();
                $this->isNotUnique();
                if (!array_key_exists($attribute->getName(), $this->errors)) $this->errors[$attribute->getName()] = true;
            }
        }
    }


    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        foreach ($this->getErrors() as $error) {
            if ($error !== true) {
                return false;
            }
        }
        return true;
    }
}