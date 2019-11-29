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

namespace Appkweb\Bundle\EasyCrudBundle\Crud;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class AttributeDefinition
 * @package Appkweb\Bundle\EasyCrudBundle\Crud
 */
class AttributeDefinition
{
    const ATTRIBUTE_TYPES = [
        'Number' => 'Number',
        'TextArea' => 'TextArea',
        'Simple input text' => 'Simple input text',
        'TinyMce' => 'TinyMce',
        'Simple image picker' => 'Simple image picker',
        'Date picker' => 'Date picker',
        'Simple select' => 'Simple select',
        'Add list' => 'Add list',
        'Section' => 'Section',
//        'Multiple select' => 'Multiple select'
    ];

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $entity_relation;

    /**
     * @var bool
     */
    private $nullable;

    /**
     * @var int
     */
    private $order;

    /**
     * @var bool
     */
    private $list;

    /**
     * @var bool
     */
    private $show;

    /**
     * @var bool
     */
    private $edit;

    /**
     * @var bool
     */
    private $unique;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return AttributeDefinition
     */
    public function setName(string $name): AttributeDefinition
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return AttributeDefinition
     */
    public function setLabel(string $label): AttributeDefinition
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return AttributeDefinition
     */
    public function setType(string $type): AttributeDefinition
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return bool|string
     */
    public function getEntityRelation()
    {
        if ($this->entity_relation === 'false' || $this->entity_relation === '') {
            $this->entity_relation = false;
        }

        return $this->entity_relation;
    }

    /**
     * @param string $entity_relation
     * @return AttributeDefinition
     */
    public function setEntityRelation(string $entity_relation): AttributeDefinition
    {
        $this->entity_relation = $entity_relation;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * @param bool $nullable
     * @return AttributeDefinition
     */
    public function setNullable(bool $nullable): AttributeDefinition
    {
        $this->nullable = $nullable;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return AttributeDefinition
     */
    public function setOrder(int $order): AttributeDefinition
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return bool
     */
    public function isList(): bool
    {
        return $this->list;
    }

    /**
     * @param bool $list
     * @return AttributeDefinition
     */
    public function setList(bool $list): AttributeDefinition
    {
        $this->list = $list;
        return $this;
    }

    /**
     * @return bool
     */
    public function isShow(): bool
    {
        return $this->show;
    }

    /**
     * @param bool $show
     * @return AttributeDefinition
     */
    public function setShow(bool $show): AttributeDefinition
    {
        $this->show = $show;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEdit(): bool
    {
        return $this->edit;
    }

    /**
     * @param bool $edit
     * @return AttributeDefinition
     */
    public function setEdit(bool $edit): AttributeDefinition
    {
        $this->edit = $edit;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->unique;
    }

    /**
     * @param bool $unique
     * @return AttributeDefinition
     */
    public function setUnique(bool $unique): AttributeDefinition
    {
        $this->unique = $unique;
        return $this;
    }
}