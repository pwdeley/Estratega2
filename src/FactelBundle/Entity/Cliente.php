<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Cliente
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FactelBundle\Entity\Repository\ClienteRepository")
 */
class Cliente {

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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="tipoIdentificacion", type="string", length=2)
     */
    private $tipoIdentificacion;

    /**
     * @var string
     *
     * @ORM\Column(name="identificacion", type="string", length=25, unique = FALSE)
     */
    private $identificacion;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="text", nullable=true)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="celular", type="string", length=255, nullable=true)
     */
    private $celular;

    /**
     * @var string
     *
     * @ORM\Column(name="correoElectronico", type="string", length=255, nullable=true)
     */
    private $correoElectronico;

    /**
     * @ORM\OneToMany(targetEntity="Factura", mappedBy="cliente")
     */
    protected $facturas;

    /**
     * @ORM\OneToMany(targetEntity="Proforma", mappedBy="cliente")
     */
    protected $proformas;

    /**
     * @ORM\OneToMany(targetEntity="LiquidacionCompra", mappedBy="cliente")
     */
    protected $liquidacionescompra;

    /**
     * @ORM\OneToMany(targetEntity="NotaCredito", mappedBy="cliente")
     */
    protected $notasCredito;

    /**
     * @ORM\OneToMany(targetEntity="NotaDebito", mappedBy="cliente")
     */
    protected $notasDebito;

    /**
     * @ORM\OneToMany(targetEntity="Retencion", mappedBy="cliente")
     */
    protected $retencion;

    /**
     * @ORM\OneToMany(targetEntity="Guia", mappedBy="cliente")
     */
    protected $guias;

    /**
     * @ORM\ManyToOne(targetEntity="Emisor", inversedBy="clientes")
     * @ORM\JoinColumn(name="emisor_id", referencedColumnName="id", nullable=false)
     */
    protected $emisor;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Cliente
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
     * Set tipoIdentificacion
     *
     * @param string $tipoIdentificacion
     * @return Cliente
     */
    public function setTipoIdentificacion($tipoIdentificacion) {
        $this->tipoIdentificacion = $tipoIdentificacion;

        return $this;
    }

    /**
     * Get tipoIdentificacion
     *
     * @return string 
     */
    public function getTipoIdentificacion() {
        return $this->tipoIdentificacion;
    }

    /**
     * Set identificacion
     *
     * @param string $identificacion
     * @return Cliente
     */
    public function setIdentificacion($identificacion) {
        $this->identificacion = $identificacion;

        return $this;
    }

    /**
     * Get identificacion
     *
     * @return string 
     */
    public function getIdentificacion() {
        return $this->identificacion;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     * @return Cliente
     */
    public function setDireccion($direccion) {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string 
     */
    public function getDireccion() {
        return $this->direccion;
    }

    /**
     * Set celular
     *
     * @param string $celular
     * @return Cliente
     */
    public function setCelular($celular) {
        $this->celular = $celular;

        return $this;
    }

    /**
     * Get celular
     *
     * @return string 
     */
    public function getCelular() {
        return $this->celular;
    }

    /**
     * Set correoElectronico
     *
     * @param string $correoElectronico
     * @return Cliente
     */
    public function setCorreoElectronico($correoElectronico) {
        $this->correoElectronico = $correoElectronico;

        return $this;
    }

    /**
     * Get correoElectronico
     *
     * @return string 
     */
    public function getCorreoElectronico() {
        return $this->correoElectronico;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->facturas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->proformas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notasCredito = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notasDebito = new \Doctrine\Common\Collections\ArrayCollection();
        $this->guias = new \Doctrine\Common\Collections\ArrayCollection();
        $this->liquidacionescompra = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add facturas
     *
     * @param \FactelBundle\Entity\Factura $facturas
     * @return Cliente
     */
    public function addFactura(\FactelBundle\Entity\Factura $facturas) {
        $this->facturas[] = $facturas;

        return $this;
    }

    /**
     * Remove facturas
     *
     * @param \FactelBundle\Entity\Factura $facturas
     */
    public function removeFactura(\FactelBundle\Entity\Factura $facturas) {
        $this->facturas->removeElement($facturas);
    }

    /**
     * Get facturas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFacturas() {
        return $this->facturas;
    }

    /**
     * Add notasCredito
     *
     * @param \FactelBundle\Entity\NotaCredito $notasCredito
     * @return Cliente
     */
    public function addNotasCredito(\FactelBundle\Entity\NotaCredito $notasCredito) {
        $this->notasCredito[] = $notasCredito;

        return $this;
    }

    /**
     * Remove notasCredito
     *
     * @param \FactelBundle\Entity\NotaCredito $notasCredito
     */
    public function removeNotasCredito(\FactelBundle\Entity\NotaCredito $notasCredito) {
        $this->notasCredito->removeElement($notasCredito);
    }

    /**
     * Get notasCredito
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotasCredito() {
        return $this->notasCredito;
    }

    /**
     * Add notasDebito
     *
     * @param \FactelBundle\Entity\NotaDebito $notasDebito
     * @return Cliente
     */
    public function addNotasDebito(\FactelBundle\Entity\NotaDebito $notasDebito) {
        $this->notasDebito[] = $notasDebito;

        return $this;
    }

    /**
     * Remove notasDebito
     *
     * @param \FactelBundle\Entity\NotaDebito $notasDebito
     */
    public function removeNotasDebito(\FactelBundle\Entity\NotaDebito $notasDebito) {
        $this->notasDebito->removeElement($notasDebito);
    }

    /**
     * Get notasDebito
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotasDebito() {
        return $this->notasDebito;
    }

    /**
     * Add retencion
     *
     * @param \FactelBundle\Entity\Retencion $retencion
     * @return Cliente
     */
    public function addRetencion(\FactelBundle\Entity\Retencion $retencion) {
        $this->retencion[] = $retencion;

        return $this;
    }

    /**
     * Remove retencion
     *
     * @param \FactelBundle\Entity\Retencion $retencion
     */
    public function removeRetencion(\FactelBundle\Entity\Retencion $retencion) {
        $this->retencion->removeElement($retencion);
    }

    /**
     * Get retencion
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRetencion() {
        return $this->retencion;
    }

    /**
     * Set emisor
     *
     * @param \FactelBundle\Entity\Emisor $emisor
     * @return Cliente
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
     * Add guia
     *
     * @param \FactelBundle\Entity\Guia $guia
     *
     * @return Cliente
     */
    public function addGuia(\FactelBundle\Entity\Guia $guia) {
        $this->guias[] = $guia;

        return $this;
    }

    /**
     * Remove guia
     *
     * @param \FactelBundle\Entity\Guia $guia
     */
    public function removeGuia(\FactelBundle\Entity\Guia $guia) {
        $this->guias->removeElement($guia);
    }

    /**
     * Get guias
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGuias() {
        return $this->guias;
    }

    /**
     * Add liquidacionescompra
     *
     * @param \FactelBundle\Entity\LiquidacionCompra $liquidacionescompra
     *
     * @return Cliente
     */
    public function addLiquidacionescompra(\FactelBundle\Entity\LiquidacionCompra $liquidacionescompra) {
        $this->liquidacionescompra[] = $liquidacionescompra;

        return $this;
    }

    /**
     * Remove liquidacionescompra
     *
     * @param \FactelBundle\Entity\LiquidacionCompra $liquidacionescompra
     */
    public function removeLiquidacionescompra(\FactelBundle\Entity\LiquidacionCompra $liquidacionescompra) {
        $this->liquidacionescompra->removeElement($liquidacionescompra);
    }

    /**
     * Get liquidacionescompra
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLiquidacionescompra() {
        return $this->liquidacionescompra;
    }

    /**
     * Add proforma
     *
     * @param \FactelBundle\Entity\Proforma $proforma
     *
     * @return Cliente
     */
    public function addProforma(\FactelBundle\Entity\Proforma $proforma) {
        $this->proformas[] = $proforma;

        return $this;
    }

    /**
     * Remove proforma
     *
     * @param \FactelBundle\Entity\Proforma $proforma
     */
    public function removeProforma(\FactelBundle\Entity\Proforma $proforma) {
        $this->proformas->removeElement($proforma);
    }

    /**
     * Get proformas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProformas() {
        return $this->proformas;
    }

}
