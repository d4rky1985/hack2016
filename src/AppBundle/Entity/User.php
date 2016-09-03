<?php
namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User extends BaseUser
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
     * @ORM\Column(name="sex", type="string", length=50, nullable=true)
     */
    private $sex;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthday", type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @var string
     *
     * @ORM\Column(name="fb_token", type="string", length=255, nullable=true)
     */
    protected $fbToken;

    /**
     * @var string
     *
     * @ORM\Column(name="push_token", type="string", length=255, nullable=true)
     */
    protected $pushToken;

    /**
     * @var string
     *
     * @ORM\Column(name="fb_id", type="string", length=255, nullable=true)
     */
    protected $fbId;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

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
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * @param string $sex
     *
     * @return User
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     * @return User
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return string
     */
    public function getFbToken()
    {
        return $this->fbToken;
    }

    /**
     * @param string $fbToken
     *
     * @return User
     */
    public function setFbToken($fbToken)
    {
        $this->fbToken = $fbToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getPushToken()
    {
        return $this->pushToken;
    }

    /**
     * @param string $pushToken
     *
     * @return User
     */
    public function setPushToken($pushToken)
    {
        $this->pushToken = $pushToken;

        return $this;
    }

    /**
     * @return string
     */
    public function getFbId()
    {
        return $this->fbId;
    }

    /**
     * @param string $fbId
     *
     * @return User
     */
    public function setFbId($fbId)
    {
        $this->fbId = $fbId;

        return $this;
    }
}