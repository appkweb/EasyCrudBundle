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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class CrudDefinition
 * @package Appkweb\Bundle\EasyCrudBundle\Crud
 */
class CrudDefinition
{

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
    }

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $plurialLabel;

    /**
     * @var string
     */
    private $singularLabel;

    /**
     * @var string
     */
    private $referrer;

    /**
     * @var int
     */
    private $order;

    /**
     * @var bool
     */
    private $visible;

    /**
     * @var bool
     */
    private $show;

    /**
     * @var bool
     */
    private $remove;

    /**
     * @var bool
     */
    private $list;

    /**
     * @var bool
     */
    private $edit;

    /**
     * @var bool
     */
    private $add;

    /**
     * @var AttributeDefinition[]
     */
    private $attributes;

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     * @return CrudDefinition
     */
    public function setClassName(string $className): CrudDefinition
    {
        $this->className = $className;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     * @return CrudDefinition
     */
    public function setPrefix(string $prefix): CrudDefinition
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return CrudDefinition
     */
    public function setLabel(string $label): CrudDefinition
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return CrudDefinition
     */
    public function setOrder(int $order): CrudDefinition
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
     * @return CrudDefinition
     */
    public function setVisible(bool $visible): CrudDefinition
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRemove(): bool
    {
        return $this->remove;
    }

    /**
     * @param bool $remove
     * @return CrudDefinition
     */
    public function setRemove(bool $remove): CrudDefinition
    {
        $this->remove = $remove;
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
     * @return CrudDefinition
     */
    public function setList(bool $list): CrudDefinition
    {
        $this->list = $list;
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
     * @return CrudDefinition
     */
    public function setEdit(bool $edit): CrudDefinition
    {
        $this->edit = $edit;
        return $this;
    }

    /**
     * @return Collection|AttributeDefinition[]
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    /**
     * @param AttributeDefinition $attribute
     * @return CrudDefinition
     */
    public function addAttributes(AttributeDefinition $attribute): self
    {
        $this->attributes[] = $attribute;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAdd(): bool
    {
        return $this->add;
    }

    /**
     * @param bool $add
     * @return CrudDefinition
     */
    public function setAdd(bool $add): CrudDefinition
    {
        $this->add = $add;
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
     * @return CrudDefinition
     */
    public function setShow(bool $show): CrudDefinition
    {
        $this->show = $show;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * @param string $referrer
     * @return CrudDefinition
     */
    public function setReferrer(string $referrer): CrudDefinition
    {
        $this->referrer = $referrer;
        return $this;
    }

    /**
     * @return string
     */
    public function getPlurialLabel(): string
    {
        return $this->plurialLabel;
    }

    /**
     * @param string $plurialLabel
     * @return CrudDefinition
     */
    public function setPlurialLabel(string $plurialLabel): CrudDefinition
    {
        $this->plurialLabel = $plurialLabel;
        return $this;
    }

    /**
     * @return string
     */
    public function getSingularLabel(): string
    {
        return $this->singularLabel;
    }

    /**
     * @param string $singularLabel
     * @return CrudDefinition
     */
    public function setSingularLabel(string $singularLabel): CrudDefinition
    {
        $this->singularLabel = $singularLabel;
        return $this;
    }
}