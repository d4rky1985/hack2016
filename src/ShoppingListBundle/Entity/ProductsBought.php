<?php

namespace ShoppingListBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductsBought
 *
 * @ORM\Table(name="products_bought", indexes={@ORM\Index(name="FK_products_bought_products", columns={"product_id"}), @ORM\Index(name="FK_products_bought_products_2", columns={"parent_id"})})
 * @ORM\Entity(repositoryClass="ShoppingListBundle\Repository\ProductsBoughtRepository")
 */
class ProductsBought
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="buying_date", type="datetime", nullable=false)
     */
    private $buyingDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ShoppingListBundle\Entity\Products
     *
     * @ORM\ManyToOne(targetEntity="ShoppingListBundle\Entity\Products")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    private $product;

    /**
     * @var \ShoppingListBundle\Entity\Products
     *
     * @ORM\ManyToOne(targetEntity="ShoppingListBundle\Entity\Products")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    private $parent;



    /**
     * Set buyingDate
     *
     * @param \DateTime $buyingDate
     *
     * @return ProductsBought
     */
    public function setBuyingDate($buyingDate)
    {
        $this->buyingDate = $buyingDate;

        return $this;
    }

    /**
     * Get buyingDate
     *
     * @return \DateTime
     */
    public function getBuyingDate()
    {
        return $this->buyingDate;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set product
     *
     * @param \ShoppingListBundle\Entity\Products $product
     *
     * @return ProductsBought
     */
    public function setProduct(Products $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \ShoppingListBundle\Entity\Products
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set parent
     *
     * @param \ShoppingListBundle\Entity\Products $parent
     *
     * @return ProductsBought
     */
    public function setParent(\ShoppingListBundle\Entity\Products $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \ShoppingListBundle\Entity\Products
     */
    public function getParent()
    {
        return $this->parent;
    }
}
