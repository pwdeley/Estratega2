<?php

namespace FactelBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
/**
 * @ORM\Entity
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FactelBundle\Entity\Repository\RoleRepository")
 */
class Role implements RoleInterface, \Serializable {

    use ORMBehaviors\Timestampable\Timestampable,
        ORMBehaviors\Blameable\Blameable
    ;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="rol")
     */
    private $users;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    public function getRole() {
        return $this->getName();
    }

    public function __toString() {
        return $this->getRole();
    }

    public function serialize() {
        return \serialize(array(
            $this->id,
            $this->name
        ));
    }

    public function unserialize($serialized) {
        list(
                $this->id,
                $this->name
                ) = \unserialize($serialized);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add users
     *
     * @param \FactelBundle\Entity\User $users
     * @return Role
     */
    public function addUser(\FactelBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \FactelBundle\Entity\User $users
     */
    public function removeUser(\FactelBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }
}
