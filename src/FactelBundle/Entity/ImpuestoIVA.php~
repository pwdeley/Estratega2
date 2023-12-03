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
class ImpuestoIVA {

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
     * @var integer
     *
     * @ORM\Column(name="tarifa", type="decimal", scale=2)
     */
    protected $tarifa;
    /**
     * @ORM\OneToMany(targetEntity="Producto", mappedBy="impuestoIVA")
     */
    protected $productos;

    /**
     * Constructor
     */
    public function __construct() {
        $this->productos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set codigoPorcentaje
     *
     * @param string $codigoPorcentaje
     * @return ImpuestoIVA
     */
    public function setCodigoPorcentaje($codigoPorcentaje) {
        $this->codigoPorcentaje = $codigoPorcentaje;

        return $this;
    }

    /**
     * Get codigoPorcentaje
     *
     * @return string 
     */
    public function getCodigoPorcentaje() {
        return $this->codigoPorcentaje;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return ImpuestoIVA
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
     * Add productos
     *
     * @param \FactelBundle\Entity\Producto $productos
     * @return ImpuestoIVA
     */
    public function addProducto(\FactelBundle\Entity\Producto $productos) {
        $this->productos[] = $productos;

        return $this;
    }

    /**
     * Remove productos
     *
     * @param \FactelBundle\Entity\Producto $productos
     */
    public function removeProducto(\FactelBundle\Entity\Producto $productos) {
        $this->productos->removeElement($productos);
    }

    /**
     * Get productos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductos() {
        return $this->productos;
    }

    public function __toString() {
        return $this->nombre;
    }


    /**
     * Set tarifa
     *
     * @param string $tarifa
     * @return ImpuestoIVA
     */
    public function setTarifa($tarifa)
    {
        $this->tarifa = $tarifa;

        return $this;
    }

    /**
     * Get tarifa
     *
     * @return string 
     */
    public function getTarifa()
    {
        return $this->tarifa;
    }
}
