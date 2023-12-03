<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProformaHasProducto
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ProformaHasProducto {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Producto", inversedBy="proformasHasProducto")
     * @ORM\JoinColumn(name="producto_id", referencedColumnName="id", nullable=false)
     */
    protected $producto;

    /**
     * @ORM\ManyToOne(targetEntity="Proforma", inversedBy="proformasHasProducto")
     * @ORM\JoinColumn(name="proforma_id", referencedColumnName="id", nullable=false)
     */
    protected $proforma;

    /**
     * @ORM\OneToMany(targetEntity="Impuesto", mappedBy="proformaHasProducto", cascade={"persist"})
     */
    protected $impuestos;

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
     * Constructor
     */
    public function __construct()
    {
        $this->impuestos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @param string $cantidad
     *
     * @return ProformaHasProducto
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return string
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return ProformaHasProducto
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
     *
     * @return ProformaHasProducto
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
     * Set precioUnitario
     *
     * @param string $precioUnitario
     *
     * @return ProformaHasProducto
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
     *
     * @return ProformaHasProducto
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
     *
     * @return ProformaHasProducto
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
     * Set precioSinSubsidio
     *
     * @param string $precioSinSubsidio
     *
     * @return ProformaHasProducto
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

    /**
     * Set producto
     *
     * @param \FactelBundle\Entity\Producto $producto
     *
     * @return ProformaHasProducto
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
     * Set proforma
     *
     * @param \FactelBundle\Entity\Proforma $proforma
     *
     * @return ProformaHasProducto
     */
    public function setProforma(\FactelBundle\Entity\Proforma $proforma)
    {
        $this->proforma = $proforma;

        return $this;
    }

    /**
     * Get proforma
     *
     * @return \FactelBundle\Entity\Proforma
     */
    public function getProforma()
    {
        return $this->proforma;
    }

    /**
     * Add impuesto
     *
     * @param \FactelBundle\Entity\Impuesto $impuesto
     *
     * @return ProformaHasProducto
     */
    public function addImpuesto(\FactelBundle\Entity\Impuesto $impuesto)
    {
        $this->impuestos[] = $impuesto;

        return $this;
    }

    /**
     * Remove impuesto
     *
     * @param \FactelBundle\Entity\Impuesto $impuesto
     */
    public function removeImpuesto(\FactelBundle\Entity\Impuesto $impuesto)
    {
        $this->impuestos->removeElement($impuesto);
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
}
