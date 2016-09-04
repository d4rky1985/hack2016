<?php

namespace ShoppingListBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductsSuggestions
 *
 * @ORM\Table(name="products_suggestions")
 * @ORM\Entity(repositoryClass="ShoppingListBundle\Repository\ProductsSuggestionsRepository")
 */
class ProductsSuggestions
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="short_description", type="text", length=65535, nullable=true)
     */
    private $shortDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=100, nullable=false)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="feeling_group", type="string", length=100, nullable=true)
     */
    private $feelingGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=10, nullable=true)
     */
    protected $gender;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ProductsSuggestions
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param string $shortDescription
     *
     * @return ProductsSuggestions
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
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
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return ProductsSuggestions
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     *
     * @return ProductsSuggestions
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getFeelingGroup()
    {
        return $this->feelingGroup;
    }

    /**
     * @param string $feelingGroup
     *
     * @return ProductsSuggestions
     */
    public function setFeelingGroup($feelingGroup)
    {
        $this->feelingGroup = $feelingGroup;

        return $this;
    }

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     *
     * @return ProductsSuggestions
     */
    public function setGender(string $gender)
    {
        $this->gender = $gender;

        return $this;
    }
}
