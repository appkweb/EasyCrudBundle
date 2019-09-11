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

use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class AttributeDefinition
 * @package Appkweb\Bundle\EasyCrudBundle\Crud
 */
class AttributeDefinition
{
    const ATTRIBUTE_TYPES = [
        'integer' => 'integer',
        'text' => 'text',
        'string' => 'string',
        'ManyToOne' => 'ManyToOne',
        'OneToOne' => 'OneToOne',
        'OneToMany' => 'OneToMany',
    ];

    /**
     * @param CrudDefinition $crudDefinition
     * @return string
     */
    public static function getStrFormType(AttributeDefinition $attributeDefinition):string
    {
        $type = $attributeDefinition->getType();
        switch (true)
        {
            case $type == "string" || $type == "text":
                return TextType::class;
        }
    }

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
     * @var string
     */
    private $size;

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
    private $visible;

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
        if ($this->entity_relation === 'false' || $this->entity_relation === '')
        {
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
     * @return string
     */
    public function getSize()
    {
        if(!$this->size) return false;
        return $this->size;
    }

    /**
     * @param string $size
     * @return AttributeDefinition
     */
    public function setSize(string $size): AttributeDefinition
    {
        $this->size = $size;
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
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     * @return AttributeDefinition
     */
    public function setVisible(bool $visible): AttributeDefinition
    {
        $this->visible = $visible;
        return $this;
    }
}