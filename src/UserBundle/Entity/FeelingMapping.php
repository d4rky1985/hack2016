<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="UserBundle\Repository\FeelingMappingRepository")
 * @ORM\Table(name="feeling_mapping")
 */
class FeelingMapping
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="fb_status", type="string", length=100, nullable=true)
     */
    private $fbStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="status_group", type="string", length=255, nullable=true)
     */
    private $statusGroup;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFbStatus()
    {
        return $this->fbStatus;
    }

    /**
     * @param string $fbStatus
     *
     * @return FeelingMapping
     */
    public function setFbStatus(string $fbStatus)
    {
        $this->fbStatus = $fbStatus;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatusGroup(): string
    {
        return $this->statusGroup;
    }

    /**
     * @param string $statusGroup
     *
     * @return FeelingMapping
     */
    public function setStatusGroup(string $statusGroup)
    {
        $this->statusGroup = $statusGroup;

        return $this;
    }
}