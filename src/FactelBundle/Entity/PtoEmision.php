<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * PtoEmision
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="FactelBundle\Entity\Repository\PtoEmisionRepository")
 */
class PtoEmision {

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
     * @ORM\OneToOne(targetEntity="User", inversedBy="ptoEmision")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id" )
     */
    protected $usuario;

    /**
     * @ORM\ManyToOne(targetEntity="Establecimiento", inversedBy="ptosEmision")
     * @ORM\JoinColumn(name="establecimiento_id", referencedColumnName="id", nullable=false)
     */
    protected $establecimiento;

    /**
     * @ORM\OneToMany(targetEntity="Factura", mappedBy="ptoEmision")
     */
    protected $facturas;

    /**
     * @ORM\OneToMany(targetEntity="LiquidacionCompra", mappedBy="ptoEmision")
     */
    protected $liquidacionescompra;

    /**
     * @ORM\OneToMany(targetEntity="NotaCredito", mappedBy="ptoEmision")
     */
    protected $notasCredito;

    /**
     * @ORM\OneToMany(targetEntity="NotaDebito", mappedBy="ptoEmision")
     */
    protected $notasDebito;

    /**
     * @ORM\OneToMany(targetEntity="Retencion", mappedBy="ptoEmision")
     */
    protected $retencion;

    /**
     * @ORM\OneToMany(targetEntity="Guia", mappedBy="ptoEmision")
     */
    protected $guias;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=60)
     */
    protected $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=3)
     */
    protected $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="secuencialFactura", type="string", length=9)
     */
    protected $secuencialFactura;
    
     /**
     * @var string
     *
     * @ORM\Column(name="secuencialLiquidacionCompra", type="string", length=9)
     */
    protected $secuencialLiquidacionCompra;


    /**
     * @var string
     *
     * @ORM\Column(name="secuencialNotaCredito", type="string", length=9)
     */
    protected $secuencialNotaCredito;

    /**
     * @var string
     *
     * @ORM\Column(name="secuencialNotaDebito", type="string", length=9)
     */
    protected $secuencialNotaDebito;

    /**
     * @var string
     *
     * @ORM\Column(name="secuencialGuiaRemision", type="string", length=9)
     */
    protected $secuencialGuiaRemision;

    /**
     * @var string
     *
     * @ORM\Column(name="secuencialRetencion", type="string", length=9)
     */
    protected $secuencialRetencion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean")
     */
    protected $activo;

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
     * @return PtoEmision
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
     * Set codigo
     *
     * @param string $codigo
     * @return PtoEmision
     */
    public function setCodigo($codigo) {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo() {
        return $this->codigo;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return PtoEmision
     */
    public function setActivo($activo) {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean 
     */
    public function getActivo() {
        return $this->activo;
    }

    /**
     * Set establecimiento
     *
     * @param \FactelBundle\Entity\Establecimiento $establecimiento
     * @return PtoEmision
     */
    public function setEstablecimiento(\FactelBundle\Entity\Establecimiento $establecimiento) {
        $this->establecimiento = $establecimiento;

        return $this;
    }

    /**
     * Get establecimiento
     *
     * @return \FactelBundle\Entity\Establecimiento 
     */
    public function getEstablecimiento() {
        return $this->establecimiento;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->facturas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notasCredito = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notasDebito = new \Doctrine\Common\Collections\ArrayCollection();
        $this->guias = new \Doctrine\Common\Collections\ArrayCollection();
        $this->liquidacionescompra = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString() {
        return $this->nombre . " <---> " . $this->establecimiento->getNombre();
    }

    /**
     * Set usuario
     *
     * @param \FactelBundle\Entity\User $usuario
     * @return PtoEmision
     */
    public function setUsuario(\FactelBundle\Entity\User $usuario = null) {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \FactelBundle\Entity\User 
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * Add facturas
     *
     * @param \FactelBundle\Entity\Factura $facturas
     * @return PtoEmision
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
     * @return PtoEmision
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
     * @return PtoEmision
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
     * Set secuencialFactura
     *
     * @param string $secuencialFactura
     * @return PtoEmision
     */
    public function setSecuencialFactura($secuencialFactura) {
        $this->secuencialFactura = $secuencialFactura;

        return $this;
    }

    /**
     * Get secuencialFactura
     *
     * @return string 
     */
    public function getSecuencialFactura() {
        return $this->secuencialFactura;
    }

    /**
     * Set secuencialNotaCredito
     *
     * @param string $secuencialNotaCredito
     * @return PtoEmision
     */
    public function setSecuencialNotaCredito($secuencialNotaCredito) {
        $this->secuencialNotaCredito = $secuencialNotaCredito;

        return $this;
    }

    /**
     * Get secuencialNotaCredito
     *
     * @return string 
     */
    public function getSecuencialNotaCredito() {
        return $this->secuencialNotaCredito;
    }

    /**
     * Set secuencialNotaDebito
     *
     * @param string $secuencialNotaDebito
     * @return PtoEmision
     */
    public function setSecuencialNotaDebito($secuencialNotaDebito) {
        $this->secuencialNotaDebito = $secuencialNotaDebito;

        return $this;
    }

    /**
     * Get secuencialNotaDebito
     *
     * @return string 
     */
    public function getSecuencialNotaDebito() {
        return $this->secuencialNotaDebito;
    }

    /**
     * Set secuencialGuiaRemision
     *
     * @param string $secuencialGuiaRemision
     * @return PtoEmision
     */
    public function setSecuencialGuiaRemision($secuencialGuiaRemision) {
        $this->secuencialGuiaRemision = $secuencialGuiaRemision;

        return $this;
    }

    /**
     * Get secuencialGuiaRemision
     *
     * @return string 
     */
    public function getSecuencialGuiaRemision() {
        return $this->secuencialGuiaRemision;
    }

    /**
     * Set secuencialRetencion
     *
     * @param string $secuencialRetencion
     * @return PtoEmision
     */
    public function setSecuencialRetencion($secuencialRetencion) {
        $this->secuencialRetencion = $secuencialRetencion;

        return $this;
    }

    /**
     * Get secuencialRetencion
     *
     * @return string 
     */
    public function getSecuencialRetencion() {
        return $this->secuencialRetencion;
    }

    /**
     * Add retencion
     *
     * @param \FactelBundle\Entity\Retencion $retencion
     * @return PtoEmision
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
     * Add guia
     *
     * @param \FactelBundle\Entity\Guia $guia
     *
     * @return PtoEmision
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
     * @return PtoEmision
     */
    public function addLiquidacionescompra(\FactelBundle\Entity\LiquidacionCompra $liquidacionescompra)
    {
        $this->liquidacionescompra[] = $liquidacionescompra;

        return $this;
    }

    /**
     * Remove liquidacionescompra
     *
     * @param \FactelBundle\Entity\LiquidacionCompra $liquidacionescompra
     */
    public function removeLiquidacionescompra(\FactelBundle\Entity\LiquidacionCompra $liquidacionescompra)
    {
        $this->liquidacionescompra->removeElement($liquidacionescompra);
    }

    /**
     * Get liquidacionescompra
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLiquidacionescompra()
    {
        return $this->liquidacionescompra;
    }

    /**
     * Set secuencialLiquidacionCompra
     *
     * @param string $secuencialLiquidacionCompra
     *
     * @return PtoEmision
     */
    public function setSecuencialLiquidacionCompra($secuencialLiquidacionCompra)
    {
        $this->secuencialLiquidacionCompra = $secuencialLiquidacionCompra;

        return $this;
    }

    /**
     * Get secuencialLiquidacionCompra
     *
     * @return string
     */
    public function getSecuencialLiquidacionCompra()
    {
        return $this->secuencialLiquidacionCompra;
    }
}
