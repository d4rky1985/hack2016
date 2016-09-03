<?php

namespace ShoppingListBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ListsToProducts
 *
 * @ORM\Table(name="lists_to_products", indexes={@ORM\Index(name="FK_lists_to_products_shopping_list", columns={"shopping_list_id"}), @ORM\Index(name="FK_lists_to_products_products", columns={"product_id"})})
 * @ORM\Entity
 */
class ListsToProducts
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ShoppingListBundle\Entity\ShoppingList
     *
     * @ORM\ManyToOne(targetEntity="ShoppingListBundle\Entity\ShoppingList")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="shopping_list_id", referencedColumnName="id")
     * })
     */
    private $shoppingList;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set shoppingList
     *
     * @param \ShoppingListBundle\Entity\ShoppingList $shoppingList
     *
     * @return ListsToProducts
     */
    public function setShoppingList(\ShoppingListBundle\Entity\ShoppingList $shoppingList = null)
    {
        $this->shoppingList = $shoppingList;

        return $this;
    }

    /**
     * Get shoppingList
     *
     * @return \ShoppingListBundle\Entity\ShoppingList
     */
    public function getShoppingList()
    {
        return $this->shoppingList;
    }

    /**
     * Set product
     *
     * @param \ShoppingListBundle\Entity\Products $product
     *
     * @return ListsToProducts
     */
    public function setProduct(\ShoppingListBundle\Entity\Products $product = null)
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
}
