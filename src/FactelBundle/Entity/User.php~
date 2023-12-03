<?php

namespace FactelBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="FactelBundle\Entity\Repository\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable {

    use ORMBehaviors\Timestampable\Timestampable,
        ORMBehaviors\Blameable\Blameable
    ;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Emisor", inversedBy="usuarios")
     * @ORM\JoinColumn(name="emisor_id", referencedColumnName="id", nullable=false)
     */
    protected $emisor;

    /**
     * @ORM\OneToOne(targetEntity="PtoEmision", mappedBy="usuario")
     **/
    private $ptoEmision;
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $username;

    /**
     * @ORM\Column(name="password", type="string", length=255)
     */
    protected $password;

    /**
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected $email;

    /**
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    protected $nombre;

    /**
     * @ORM\Column(name="apellidos", type="string", length=255)
     */
    protected $apellidos;

    /**
     * @ORM\Column(name="salt", type="string", length=255)
     */
    protected $salt;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    
    /**
     * @ORM\Column(name="copiar_email", type="boolean")
     */
    private $copiarEmail = false;

    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="users")
     * @ORM\JoinColumn(name="rol_id", referencedColumnName="id", nullable=false)
     */
    private $rol;

    public function __construct() {
        $this->isActive = true;
    }

    public function isAccountNonExpired() {
        return true;
    }

    public function isAccountNonLocked() {
        return true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    public function isEnabled() {
        return $this->isActive;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt) {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string 
     */
    public function getSalt() {
        return $this->salt;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive() {
        return $this->isActive;
    }
    
    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setCopiarEmail($copiarEmail) {
        $this->copiarEmail = $copiarEmail;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getCopiarEmail() {
        return $this->copiarEmail;
    }

    /**
     * Set emisor
     *
     * @param \FactelBundle\Entity\Emisor $emisor
     * @return User
     */
    public function setEmisor(\FactelBundle\Entity\Emisor $emisor) {
        $this->emisor = $emisor;

        return $this;
    }

    /**
     * Get emisor
     *
     * @return \FactelBundle\Entity\Emisor 
     */
    public function getEmisor() {
        return $this->emisor;
    }

    /**
     * Get roles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRoles() {
        return array($this->rol);
    }

    public function getRolesString() {

        $abrev = "";
        if ($this->getRol() == "ROLE_ADMIN") {
            $abrev = "ADMIN";
        } else if ($this->getRol() == "ROLE_EMISOR_ADMIN") {
            $abrev = "EMISOR_ADMIN";
        } else {
            $abrev = "EMISOR";
        }

        return $abrev;
    }

    public function eraseCredentials() {
        
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return User
     */
    public function setNombre($nombre) {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Set apellidos
     *
     * @param string $apellidos
     * @return User
     */
    public function setApellidos($apellidos) {
        $this->apellidos = $apellidos;

        return $this;
    }

    /**
     * Get apellidos
     *
     * @return string 
     */
    public function getApellidos() {
        return $this->apellidos;
    }

    public function serialize() {
        return \serialize(array(
            $this->id,
            $this->username,
            $this->nombre,
            $this->apellidos,
            $this->email,
            $this->salt,
            $this->password,
            $this->isActive
        ));
    }

    public function unserialize($serialized) {
        list (
                $this->id,
                $this->username,
                $this->nombre,
                $this->apellidos,
                $this->email,
                $this->salt,
                $this->password,
                $this->isActive
                ) = \unserialize($serialized);
    }

    public function __toString() {
        return $this->username;
    }

    /**
     * Set rol
     *
     * @param \FactelBundle\Entity\Role $rol
     * @return User
     */
    public function setRol(\FactelBundle\Entity\Role $rol) {
        $this->rol = $rol;

        return $this;
    }

    /**
     * Get rol
     *
     * @return \FactelBundle\Entity\Role 
     */
    public function getRol() {
        return $this->rol;
    }
    public function getNombreCompleto(){
        return $this->nombre ." ".$this->apellidos;
    }

    /**
     * Set ptoEmision
     *
     * @param \FactelBundle\Entity\PtoEmision $ptoEmision
     * @return User
     */
    public function setPtoEmision(\FactelBundle\Entity\PtoEmision $ptoEmision = null)
    {
        $this->ptoEmision = $ptoEmision;

        return $this;
    }

    /**
     * Get ptoEmision
     *
     * @return \FactelBundle\Entity\PtoEmision 
     */
    public function getPtoEmision()
    {
        return $this->ptoEmision;
    }
    
    
   
}
