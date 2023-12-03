<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
/**
 * Impuesto
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ImpuestoIRBPNR {

     use ORMBehaviors\Timestampable\Timestampable,
        ORMBehaviors\Blameable\Blameable
    ;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="codigoPorcentaje", type="string", length=4)
     */
    protected $codigoPorcentaje;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    protected $nombre;

    /**
     * @ORM\OneToMany(targetEntity="Producto", mappedBy="impuestoIRBPNR")
     */
    
    protected $productos;

   
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set codigoPorcentaje
     *
     * @param string $codigoPorcentaje
     * @return ImpuestoIRBPNR
     */
    public function setCodigoPorcentaje($codigoPorcentaje)
    {
        $this->codigoPorcentaje = $codigoPorcentaje;

        return $this;
    }

    /**
     * Get codigoPorcentaje
     *
     * @return string 
     */
    public function getCodigoPorcentaje()
    {
        return $this->codigoPorcentaje;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return ImpuestoIRBPNR
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Add productos
     *
     * @param \FactelBundle\Entity\Producto $productos
     * @return ImpuestoIRBPNR
     */
    public function addProducto(\FactelBundle\Entity\Producto $productos)
    {
        $this->productos[] = $productos;

        return $this;
    }

    /**
     * Remove productos
     *
     * @param \FactelBundle\Entity\Producto $productos
     */
    public function removeProducto(\FactelBundle\Entity\Producto $productos)
    {
        $this->productos->removeElement($productos);
    }

    /**
     * Get productos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductos()
    {
        return $this->productos;
    }
    public function __toString()
    {
        return  $this->nombre;
    }
}
