<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NotaCreditoHasProducto
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class NotaCreditoHasProducto {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Producto", inversedBy="notaCreditoHasProducto")
     * @ORM\JoinColumn(name="producto_id", referencedColumnName="id", nullable=false)
     */
    protected $producto;

    /**
     * @ORM\ManyToOne(targetEntity="NotaCredito", inversedBy="notaCreditoHasProducto")
     * @ORM\JoinColumn(name="notaCredito_id", referencedColumnName="id", nullable=false)
     */
    protected $notaCredito;

    /**
     * @ORM\OneToMany(targetEntity="Impuesto", mappedBy="notaCreditoHasProducto", cascade={"persist"})
     */
    protected $impuestos;

    /**
     * @ORM\OneToMany(targetEntity="DetalleAdicional", mappedBy="notaCreditoHasProducto")
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
     * Constructor
     */
    public function __construct()
    {
        $this->impuestos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->detallesAdicionales = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set cantidad
     *
     * @param integer $cantidad
     * @return NotaCreditoHasProducto
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return integer 
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set precioUnitario
     *
     * @param string $precioUnitario
     * @return NotaCreditoHasProducto
     */
    public function setPrecioUnitario($precioUnitario)
    {
        $this->precioUnitario = $precioUnitario;

        return $this;
    }

    /**
     * Get precioUnitario
     *
     * @return string 
     */
    public function getPrecioUnitario()
    {
        return $this->precioUnitario;
    }

    /**
     * Set descuento
     *
     * @param string $descuento
     * @return NotaCreditoHasProducto
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get descuento
     *
     * @return string 
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * Set valorTotal
     *
     * @param string $valorTotal
     * @return NotaCreditoHasProducto
     */
    public function setValorTotal($valorTotal)
    {
        $this->valorTotal = $valorTotal;

        return $this;
    }

    /**
     * Get valorTotal
     *
     * @return string 
     */
    public function getValorTotal()
    {
        return $this->valorTotal;
    }

    /**
     * Set producto
     *
     * @param \FactelBundle\Entity\Producto $producto
     * @return NotaCreditoHasProducto
     */
    public function setProducto(\FactelBundle\Entity\Producto $producto)
    {
        $this->producto = $producto;

        return $this;
    }

    /**
     * Get producto
     *
     * @return \FactelBundle\Entity\Producto 
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set notaCredito
     *
     * @param \FactelBundle\Entity\NotaCredito $notaCredito
     * @return NotaCreditoHasProducto
     */
    public function setNotaCredito(\FactelBundle\Entity\NotaCredito $notaCredito)
    {
        $this->notaCredito = $notaCredito;

        return $this;
    }

    /**
     * Get notaCredito
     *
     * @return \FactelBundle\Entity\NotaCredito 
     */
    public function getNotaCredito()
    {
        return $this->notaCredito;
    }

    /**
     * Add impuestos
     *
     * @param \FactelBundle\Entity\Impuesto $impuestos
     * @return NotaCreditoHasProducto
     */
    public function addImpuesto(\FactelBundle\Entity\Impuesto $impuestos)
    {
        $this->impuestos[] = $impuestos;

        return $this;
    }

    /**
     * Remove impuestos
     *
     * @param \FactelBundle\Entity\Impuesto $impuestos
     */
    public function removeImpuesto(\FactelBundle\Entity\Impuesto $impuestos)
    {
        $this->impuestos->removeElement($impuestos);
    }

    /**
     * Get impuestos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImpuestos()
    {
        return $this->impuestos;
    }

    /**
     * Add detallesAdicionales
     *
     * @param \FactelBundle\Entity\DetalleAdicional $detallesAdicionales
     * @return NotaCreditoHasProducto
     */
    public function addDetallesAdicionale(\FactelBundle\Entity\DetalleAdicional $detallesAdicionales)
    {
        $this->detallesAdicionales[] = $detallesAdicionales;

        return $this;
    }

    /**
     * Remove detallesAdicionales
     *
     * @param \FactelBundle\Entity\DetalleAdicional $detallesAdicionales
     */
    public function removeDetallesAdicionale(\FactelBundle\Entity\DetalleAdicional $detallesAdicionales)
    {
        $this->detallesAdicionales->removeElement($detallesAdicionales);
    }

    /**
     * Get detallesAdicionales
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDetallesAdicionales()
    {
        return $this->detallesAdicionales;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return NotaCreditoHasProducto
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
     * @return NotaCreditoHasProducto
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
}
