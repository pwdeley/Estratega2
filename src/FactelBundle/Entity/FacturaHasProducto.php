<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FacturaHasProducto
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FacturaHasProducto {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Producto", inversedBy="facturasHasProducto")
     * @ORM\JoinColumn(name="producto_id", referencedColumnName="id", nullable=false)
     */
    protected $producto;

    /**
     * @ORM\ManyToOne(targetEntity="Factura", inversedBy="facturasHasProducto")
     * @ORM\JoinColumn(name="factura_id", referencedColumnName="id", nullable=false)
     */
    protected $factura;

    /**
     * @ORM\OneToMany(targetEntity="Impuesto", mappedBy="facturaHasProducto", cascade={"persist"})
     */
    protected $impuestos;

    /**
     * @ORM\OneToMany(targetEntity="DetalleAdicional", mappedBy="facturaHasProducto")
     */
    protected $detallesAdicionales;

    /**
     * @ORM\Column(name="cantidad", type="decimal", scale=2)
     */
    protected $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=300)
     */
    protected $nombre;
    
    /**
     * @var string
     *
     * @ORM\Column(name="codigoProducto", type="string", length=300)
     */
    protected $codigoProducto;
    
    /**
     * @ORM\Column(name="precioUnitario", type="decimal", scale=2)
     */
    protected $precioUnitario;

    /**
     * @ORM\Column(name="descuento", type="decimal", scale=2)
     */
    protected $descuento;

    /**
     * @var integer
     *
     * @ORM\Column(name="valorTotal", type="decimal", scale=2)
     */
    protected $valorTotal;

    
     /**
     * @var integer
     *
     * @ORM\Column(name="precioSinSubsidio", type="decimal", scale=2, nullable=true)
     */
    protected $precioSinSubsidio;
    
  
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->impuestos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->detallesAdicionales = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add impuestos
     *
     * @param \FactelBundle\Entity\Impuesto $impuestos
     * @return FacturaHasProducto
     */
    public function addImpuesto(\FactelBundle\Entity\Impuesto $impuestos) {
        $this->impuestos[] = $impuestos;

        return $this;
    }

    /**
     * Remove impuestos
     *
     * @param \FactelBundle\Entity\Impuesto $impuestos
     */
    public function removeImpuesto(\FactelBundle\Entity\Impuesto $impuestos) {
        $this->impuestos->removeElement($impuestos);
    }

    /**
     * Get impuestos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImpuestos() {
        return $this->impuestos;
    }

    /**
     * Add detallesAdicionales
     *
     * @param \FactelBundle\Entity\DetalleAdicional $detallesAdicionales
     * @return FacturaHasProducto
     */
    public function addDetallesAdicionale(\FactelBundle\Entity\DetalleAdicional $detallesAdicionales) {
        $this->detallesAdicionales[] = $detallesAdicionales;

        return $this;
    }

    /**
     * Remove detallesAdicionales
     *
     * @param \FactelBundle\Entity\DetalleAdicional $detallesAdicionales
     */
    public function removeDetallesAdicionale(\FactelBundle\Entity\DetalleAdicional $detallesAdicionales) {
        $this->detallesAdicionales->removeElement($detallesAdicionales);
    }

    /**
     * Get detallesAdicionales
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDetallesAdicionales() {
        return $this->detallesAdicionales;
    }

    /**
     * Set cantidad
     *
     * @param integer $cantidad
     * @return FacturaHasProducto
     */
    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer 
     */
    public function getCantidad() {
        return $this->cantidad;
    }

    /**
     * Set precioUnitario
     *
     * @param float $precioUnitario
     * @return FacturaHasProducto
     */
    public function setPrecioUnitario($precioUnitario) {
        $this->precioUnitario = $precioUnitario;

        return $this;
    }

    /**
     * Get precioUnitario
     *
     * @return float 
     */
    public function getPrecioUnitario() {
        return $this->precioUnitario;
    }

    /**
     * Set descuento
     *
     * @param float $descuento
     * @return FacturaHasProducto
     */
    public function setDescuento($descuento) {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get descuento
     *
     * @return float 
     */
    public function getDescuento() {
        return $this->descuento;
    }

    /**
     * Set producto
     *
     * @param \FactelBundle\Entity\Producto $producto
     * @return FacturaHasProducto
     */
    public function setProducto(\FactelBundle\Entity\Producto $producto = null) {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto
     *
     * @return \FactelBundle\Entity\Producto 
     */
    public function getProducto() {
        return $this->producto;
    }

    /**
     * Set factura
     *
     * @param \FactelBundle\Entity\Factura $factura
     * @return FacturaHasProducto
     */
    public function setFactura(\FactelBundle\Entity\Factura $factura = null) {
        $this->factura = $factura;

        return $this;
    }

    /**
     * Get factura
     *
     * @return \FactelBundle\Entity\Factura 
     */
    public function getFactura() {
        return $this->factura;
    }


    /**
     * Set valorTotal
     *
     * @param float $valorTotal
     * @return FacturaHasProducto
     */
    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;

        return $this;
    }

    /**
     * Get valorTotal
     *
     * @return float 
     */
    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return FacturaHasProducto
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
     * Set codigoProducto
     *
     * @param string $codigoProducto
     * @return FacturaHasProducto
     */
    public function setCodigoProducto($codigoProducto)
    {
        $this->codigoProducto = $codigoProducto;

        return $this;
    }

    /**
     * Get codigoProducto
     *
     * @return string 
     */
    public function getCodigoProducto()
    {
        return $this->codigoProducto;
    }

    /**
     * Set precioSinSubsidio
     *
     * @param string $precioSinSubsidio
     *
     * @return FacturaHasProducto
     */
    public function setPrecioSinSubsidio($precioSinSubsidio)
    {
        $this->precioSinSubsidio = $precioSinSubsidio;

        return $this;
    }

    /**
     * Get precioSinSubsidio
     *
     * @return string
     */
    public function getPrecioSinSubsidio()
    {
        return $this->precioSinSubsidio;
    }
}
