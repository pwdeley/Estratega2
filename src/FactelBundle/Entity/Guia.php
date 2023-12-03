<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * GuiaRemision
 * @ORM\Entity(repositoryClass="FactelBundle\Entity\Repository\GuiaRepository")
 */
class Guia {

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
     * @ORM\Column(name="claveAcceso", type="string", length=49)
     */
    protected $claveAcceso;

    /**
     * @var string
     *
     * @ORM\Column(name="numeroAutorizacion", type="string", length=49, nullable=true)
     */
    protected $numeroAutorizacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaAutorizacion", type="datetime", nullable=true)
     */
    protected $fechaAutorizacion;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=100)
     */
    protected $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="ambiente", type="string", length=1)
     */
    protected $ambiente;

    /**
     * @var string
     *
     * @ORM\Column(name="tipoEmision", type="string", length=100)
     */
    protected $tipoEmision;

    /**
     * @var string
     *
     * @ORM\Column(name="secuencial", type="string", length=9)
     */
    protected $secuencial;

    /**
     * @var string
     *
     * @ORM\Column(name="nombreArchivo", type="string", length=200, nullable=true)
     */
    protected $nombreArchivo;

    /**
     * @var string
     *
     * @ORM\Column(name="dirPartida", type="string", length=300)
     */
    protected $dirPartida;

    /**
     * @var string
     *
     * @ORM\Column(name="razonSocialTransportista", type="string", length=255)
     */
    private $razonSocialTransportista;

    /**
     * @var string
     *
     * @ORM\Column(name="rucTransportista", type="string", length=13)
     */
    private $rucTransportista;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaIniTransporte", type="date")
     */
    protected $fechaIniTransporte;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaFinTransporte", type="date")
     */
    protected $fechaFinTransporte;

    /**
     * @var string
     *
     * @ORM\Column(name="placa", type="string", length=20)
     */
    private $placa;

    /**
     * @var string
     *
     * @ORM\Column(name="motivoTraslado", type="string", length=300)
     */
    protected $motivoTraslado;

    /**
     * @ORM\ManyToOne(targetEntity="Cliente", inversedBy="guias")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id", nullable=false)
     */
    protected $cliente;

    /**
     * @ORM\ManyToOne(targetEntity="Emisor", inversedBy="guias")
     * @ORM\JoinColumn(name="emisor_id", referencedColumnName="id", nullable=false)
     */
    protected $emisor;

    /**
     * @ORM\ManyToOne(targetEntity="Establecimiento", inversedBy="guias")
     * @ORM\JoinColumn(name="establecimiento_id", referencedColumnName="id", nullable=false)
     */
    protected $establecimiento;

    /**
     * @ORM\ManyToOne(targetEntity="PtoEmision", inversedBy="guias")
     * @ORM\JoinColumn(name="ptoEmision_id", referencedColumnName="id", nullable=false)
     */
    protected $ptoEmision;

    /**
     * @ORM\OneToMany(targetEntity="CampoAdicional", mappedBy="guia", cascade={"persist"})
     */
    protected $composAdic;

    /**
     * @ORM\OneToMany(targetEntity="GuiaHasProducto", mappedBy="guia", cascade={"persist"})
     */
    protected $guiasHasProducto;

    /**
     * @ORM\OneToMany(targetEntity="Mensaje", mappedBy="guia")
     */
    protected $mensajes;

    /**
     * @ORM\Column(name="firmado", type="boolean")
     */
    private $firmado = false;

    /**
     * @ORM\Column(name="enviarSiAutorizado", type="boolean")
     */
    private $enviarSiAutorizado = false;

    /**
     * Constructor
     */
    public function __construct() {
        $this->composAdic = new \Doctrine\Common\Collections\ArrayCollection();
        $this->guiasHasProducto = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mensajes = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set claveAcceso
     *
     * @param string $claveAcceso
     *
     * @return Guia
     */
    public function setClaveAcceso($claveAcceso) {
        $this->claveAcceso = $claveAcceso;

        return $this;
    }

    /**
     * Get claveAcceso
     *
     * @return string
     */
    public function getClaveAcceso() {
        return $this->claveAcceso;
    }

    /**
     * Set numeroAutorizacion
     *
     * @param string $numeroAutorizacion
     *
     * @return Guia
     */
    public function setNumeroAutorizacion($numeroAutorizacion) {
        $this->numeroAutorizacion = $numeroAutorizacion;

        return $this;
    }

    /**
     * Get numeroAutorizacion
     *
     * @return string
     */
    public function getNumeroAutorizacion() {
        return $this->numeroAutorizacion;
    }

    /**
     * Set fechaAutorizacion
     *
     * @param \DateTime $fechaAutorizacion
     *
     * @return Guia
     */
    public function setFechaAutorizacion($fechaAutorizacion) {
        $this->fechaAutorizacion = $fechaAutorizacion;

        return $this;
    }

    /**
     * Get fechaAutorizacion
     *
     * @return \DateTime
     */
    public function getFechaAutorizacion() {
        return $this->fechaAutorizacion;
    }

    /**
     * Set estado
     *
     * @param string $estado
     *
     * @return Guia
     */
    public function setEstado($estado) {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado() {
        return $this->estado;
    }

    /**
     * Set ambiente
     *
     * @param string $ambiente
     *
     * @return Guia
     */
    public function setAmbiente($ambiente) {
        $this->ambiente = $ambiente;

        return $this;
    }

    /**
     * Get ambiente
     *
     * @return string
     */
    public function getAmbiente() {
        return $this->ambiente;
    }

    /**
     * Set tipoEmision
     *
     * @param string $tipoEmision
     *
     * @return Guia
     */
    public function setTipoEmision($tipoEmision) {
        $this->tipoEmision = $tipoEmision;

        return $this;
    }

    /**
     * Get tipoEmision
     *
     * @return string
     */
    public function getTipoEmision() {
        return $this->tipoEmision;
    }

    /**
     * Set secuencial
     *
     * @param string $secuencial
     *
     * @return Guia
     */
    public function setSecuencial($secuencial) {
        $this->secuencial = $secuencial;

        return $this;
    }

    /**
     * Get secuencial
     *
     * @return string
     */
    public function getSecuencial() {
        return $this->secuencial;
    }

    /**
     * Set dirPartida
     *
     * @param string $dirPartida
     *
     * @return Guia
     */
    public function setDirPartida($dirPartida) {
        $this->dirPartida = $dirPartida;

        return $this;
    }

    /**
     * Get dirPartida
     *
     * @return string
     */
    public function getDirPartida() {
        return $this->dirPartida;
    }

    /**
     * Set razonSocialTransportista
     *
     * @param string $razonSocialTransportista
     *
     * @return Guia
     */
    public function setRazonSocialTransportista($razonSocialTransportista) {
        $this->razonSocialTransportista = $razonSocialTransportista;

        return $this;
    }

    /**
     * Get razonSocialTransportista
     *
     * @return string
     */
    public function getRazonSocialTransportista() {
        return $this->razonSocialTransportista;
    }

    /**
     * Set rucTransportista
     *
     * @param string $rucTransportista
     *
     * @return Guia
     */
    public function setRucTransportista($rucTransportista) {
        $this->rucTransportista = $rucTransportista;

        return $this;
    }

    /**
     * Get rucTransportista
     *
     * @return string
     */
    public function getRucTransportista() {
        return $this->rucTransportista;
    }

    /**
     * Set fechaIniTransporte
     *
     * @param \DateTime $fechaIniTransporte
     *
     * @return Guia
     */
    public function setFechaIniTransporte($fechaIniTransporte) {
        $this->fechaIniTransporte = $fechaIniTransporte;

        return $this;
    }

    /**
     * Get fechaIniTransporte
     *
     * @return \DateTime
     */
    public function getFechaIniTransporte() {
        return $this->fechaIniTransporte;
    }

    /**
     * Set placa
     *
     * @param string $placa
     *
     * @return Guia
     */
    public function setPlaca($placa) {
        $this->placa = $placa;

        return $this;
    }

    /**
     * Get placa
     *
     * @return string
     */
    public function getPlaca() {
        return $this->placa;
    }

    /**
     * Set motivoTraslado
     *
     * @param string $motivoTraslado
     *
     * @return Guia
     */
    public function setMotivoTraslado($motivoTraslado) {
        $this->motivoTraslado = $motivoTraslado;

        return $this;
    }

    /**
     * Get motivoTraslado
     *
     * @return string
     */
    public function getMotivoTraslado() {
        return $this->motivoTraslado;
    }

    /**
     * Set firmado
     *
     * @param boolean $firmado
     *
     * @return Guia
     */
    public function setFirmado($firmado) {
        $this->firmado = $firmado;

        return $this;
    }

    /**
     * Get firmado
     *
     * @return boolean
     */
    public function getFirmado() {
        return $this->firmado;
    }

    /**
     * Set enviarSiAutorizado
     *
     * @param boolean $enviarSiAutorizado
     *
     * @return Guia
     */
    public function setEnviarSiAutorizado($enviarSiAutorizado) {
        $this->enviarSiAutorizado = $enviarSiAutorizado;

        return $this;
    }

    /**
     * Get enviarSiAutorizado
     *
     * @return boolean
     */
    public function getEnviarSiAutorizado() {
        return $this->enviarSiAutorizado;
    }

    /**
     * Set cliente
     *
     * @param \FactelBundle\Entity\Cliente $cliente
     *
     * @return Guia
     */
    public function setCliente(\FactelBundle\Entity\Cliente $cliente) {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \FactelBundle\Entity\Cliente
     */
    public function getCliente() {
        return $this->cliente;
    }

    /**
     * Set emisor
     *
     * @param \FactelBundle\Entity\Emisor $emisor
     *
     * @return Guia
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
     * Set establecimiento
     *
     * @param \FactelBundle\Entity\Establecimiento $establecimiento
     *
     * @return Guia
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
     * Set ptoEmision
     *
     * @param \FactelBundle\Entity\PtoEmision $ptoEmision
     *
     * @return Guia
     */
    public function setPtoEmision(\FactelBundle\Entity\PtoEmision $ptoEmision) {
        $this->ptoEmision = $ptoEmision;

        return $this;
    }

    /**
     * Get ptoEmision
     *
     * @return \FactelBundle\Entity\PtoEmision
     */
    public function getPtoEmision() {
        return $this->ptoEmision;
    }

    /**
     * Add composAdic
     *
     * @param \FactelBundle\Entity\CampoAdicional $composAdic
     *
     * @return Guia
     */
    public function addComposAdic(\FactelBundle\Entity\CampoAdicional $composAdic) {
        $this->composAdic[] = $composAdic;

        return $this;
    }

    /**
     * Remove composAdic
     *
     * @param \FactelBundle\Entity\CampoAdicional $composAdic
     */
    public function removeComposAdic(\FactelBundle\Entity\CampoAdicional $composAdic) {
        $this->composAdic->removeElement($composAdic);
    }

    /**
     * Get composAdic
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComposAdic() {
        return $this->composAdic;
    }

    /**
     * Add guiasHasProducto
     *
     * @param \FactelBundle\Entity\GuiaHasProducto $guiasHasProducto
     *
     * @return Guia
     */
    public function addGuiasHasProducto(\FactelBundle\Entity\GuiaHasProducto $guiasHasProducto) {
        $this->guiasHasProducto[] = $guiasHasProducto;

        return $this;
    }

    /**
     * Remove guiasHasProducto
     *
     * @param \FactelBundle\Entity\GuiaHasProducto $guiasHasProducto
     */
    public function removeGuiasHasProducto(\FactelBundle\Entity\GuiaHasProducto $guiasHasProducto) {
        $this->guiasHasProducto->removeElement($guiasHasProducto);
    }

    /**
     * Get guiasHasProducto
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGuiasHasProducto() {
        return $this->guiasHasProducto;
    }

    /**
     * Add mensaje
     *
     * @param \FactelBundle\Entity\Mensaje $mensaje
     *
     * @return Guia
     */
    public function addMensaje(\FactelBundle\Entity\Mensaje $mensaje) {
        $this->mensajes[] = $mensaje;

        return $this;
    }

    /**
     * Remove mensaje
     *
     * @param \FactelBundle\Entity\Mensaje $mensaje
     */
    public function removeMensaje(\FactelBundle\Entity\Mensaje $mensaje) {
        $this->mensajes->removeElement($mensaje);
    }

    /**
     * Get mensajes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMensajes() {
        return $this->mensajes;
    }

    /**
     * Set fechaFinTransporte
     *
     * @param \DateTime $fechaFinTransporte
     *
     * @return Guia
     */
    public function setFechaFinTransporte($fechaFinTransporte) {
        $this->fechaFinTransporte = $fechaFinTransporte;

        return $this;
    }

    /**
     * Get fechaFinTransporte
     *
     * @return \DateTime
     */
    public function getFechaFinTransporte() {
        return $this->fechaFinTransporte;
    }

    /**
     * Set nombreArchivo
     *
     * @param string $nombreArchivo
     * @return Factura
     */
    public function setNombreArchivo($nombreArchivo) {
        $this->nombreArchivo = $nombreArchivo;

        return $this;
    }

    /**
     * Get nombreArchivo
     *
     * @return string 
     */
    public function getNombreArchivo() {
        return $this->nombreArchivo;
    }

}
