<?php

namespace ShoppingListBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductsBought
 *
 * @ORM\Table(name="products_bought", indexes={@ORM\Index(name="FK_products_bought_products", columns={"product_id"}), @ORM\Index(name="FK_products_bought_products_2", columns={"parent_id"})})
 * @ORM\Entity
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
     * @var \AppBundle\Entity\Products
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Products")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    private $product;

    /**
     * @var \AppBundle\Entity\Products
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Products")
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
     * @param \AppBundle\Entity\Products $product
     *
     * @return ProductsBought
     */
    public function setProduct(\AppBundle\Entity\Products $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \AppBundle\Entity\Products
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\Products $parent
     *
     * @return ProductsBought
     */
    public function setParent(\AppBundle\Entity\Products $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Products
     */
    public function getParent()
    {
        return $this->parent;
    }
}
