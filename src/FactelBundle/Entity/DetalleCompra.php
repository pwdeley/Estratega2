<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FacturaHasProducto
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class DetalleCompra {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Compra", inversedBy="detallesCompra")
     * @ORM\JoinColumn(name="compra_id", referencedColumnName="id", nullable=false)
     */
    protected $compra;

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
    protected $descuento = 0.00;

    /**
     * @var integer
     *
     * @ORM\Column(name="subTotal", type="decimal", scale=2)
     */
    protected $subTotal = 0.00;

    /**
     * @ORM\Column(name="iva12", type="decimal", scale=2)
     */
    protected $iva12 = 0.00;

    /**
     * @ORM\Column(name="ice", type="decimal", scale=2)
     */
    protected $ice = 0.00;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set cantidad
     *
     * @param string $cantidad
     *
     * @return DetalleCompra
     */
    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return string
     */
    public function getCantidad() {
        return $this->cantidad;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return DetalleCompra
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
     * Set codigoProducto
     *
     * @param string $codigoProducto
     *
     * @return DetalleCompra
     */
    public function setCodigoProducto($codigoProducto) {
        $this->codigoProducto = $codigoProducto;

        return $this;
    }

    /**
     * Get codigoProducto
     *
     * @return string
     */
    public function getCodigoProducto() {
        return $this->codigoProducto;
    }

    /**
     * Set precioUnitario
     *
     * @param string $precioUnitario
     *
     * @return DetalleCompra
     */
    public function setPrecioUnitario($precioUnitario) {
        $this->precioUnitario = $precioUnitario;

        return $this;
    }

    /**
     * Get precioUnitario
     *
     * @return string
     */
    public function getPrecioUnitario() {
        return $this->precioUnitario;
    }

    /**
     * Set descuento
     *
     * @param string $descuento
     *
     * @return DetalleCompra
     */
    public function setDescuento($descuento) {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * Get descuento
     *
     * @return string
     */
    public function getDescuento() {
        return $this->descuento;
    }

    /**
     * Set subTotal
     *
     * @param string $subTotal
     *
     * @return DetalleCompra
     */
    public function setSubTotal($subTotal) {
        $this->subTotal = $subTotal;

        return $this;
    }

    /**
     * Get subTotal
     *
     * @return string
     */
    public function getSubTotal() {
        return $this->subTotal;
    }

    /**
     * Set iva12
     *
     * @param string $iva12
     *
     * @return DetalleCompra
     */
    public function setIva12($iva12) {
        $this->iva12 = $iva12;

        return $this;
    }

    /**
     * Get iva12
     *
     * @return string
     */
    public function getIva12() {
        return $this->iva12;
    }

    /**
     * Set ice
     *
     * @param string $ice
     *
     * @return DetalleCompra
     */
    public function setIce($ice) {
        $this->ice = $ice;

        return $this;
    }

    /**
     * Get ice
     *
     * @return string
     */
    public function getIce() {
        return $this->ice;
    }

    /**
     * Set compra
     *
     * @param \FactelBundle\Entity\Compra $compra
     *
     * @return DetalleCompra
     */
    public function setCompra(\FactelBundle\Entity\Compra $compra) {
        $this->compra = $compra;

        return $this;
    }

    /**
     * Get compra
     *
     * @return \FactelBundle\Entity\Compra
     */
    public function getCompra() {
        return $this->compra;
    }

}
