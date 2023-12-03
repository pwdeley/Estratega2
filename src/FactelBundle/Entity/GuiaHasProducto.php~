<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FacturaHasProducto
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class GuiaHasProducto {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Producto", inversedBy="guiasHasProducto")
     * @ORM\JoinColumn(name="producto_id", referencedColumnName="id", nullable=false)
     */
    protected $producto;

    /**
     * @ORM\ManyToOne(targetEntity="Guia", inversedBy="guiasHasProducto")
     * @ORM\JoinColumn(name="guia_id", referencedColumnName="id", nullable=false)
     */
    protected $guia;

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
     * @return GuiaHasProducto
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
     * @return GuiaHasProducto
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
     * @return GuiaHasProducto
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
     * Set producto
     *
     * @param \FactelBundle\Entity\Producto $producto
     *
     * @return GuiaHasProducto
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
     * Set guia
     *
     * @param \FactelBundle\Entity\Guia $guia
     *
     * @return GuiaHasProducto
     */
    public function setGuia(\FactelBundle\Entity\Guia $guia)
    {
        $this->guia = $guia;

        return $this;
    }

    /**
     * Get guia
     *
     * @return \FactelBundle\Entity\Guia
     */
    public function getGuia()
    {
        return $this->guia;
    }
}
