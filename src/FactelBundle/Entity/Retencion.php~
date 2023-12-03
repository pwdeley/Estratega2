<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Factura
 * @ORM\Entity(repositoryClass="FactelBundle\Entity\Repository\RetencionRepository")
 */
class Retencion {

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
     * @var \DateTime
     *
     * @ORM\Column(name="fechaEmision", type="date")
     */
    protected $fechaEmision;

    /**
     * @var string
     *
     * @ORM\Column(name="periodoFiscal", type="string", length=200)
     */
    protected $periodoFiscal;

    /**
     * @var string
     *
     * @ORM\Column(name="nombreArchivo", type="string", length=200, nullable=true)
     */
    protected $nombreArchivo;

    /**
     * @ORM\ManyToOne(targetEntity="Cliente", inversedBy="retencion")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id", nullable=false)
     */
    protected $cliente;

    /**
     * @ORM\ManyToOne(targetEntity="Emisor", inversedBy="retencion")
     * @ORM\JoinColumn(name="emisor_id", referencedColumnName="id", nullable=false)
     */
    protected $emisor;

    /**
     * @ORM\ManyToOne(targetEntity="Establecimiento", inversedBy="retencion")
     * @ORM\JoinColumn(name="establecimiento_id", referencedColumnName="id", nullable=false)
     */
    protected $establecimiento;

    /**
     * @ORM\ManyToOne(targetEntity="PtoEmision", inversedBy="retencion")
     * @ORM\JoinColumn(name="ptoEmision_id", referencedColumnName="id", nullable=false)
     */
    protected $ptoEmision;

    /**
     * @ORM\OneToMany(targetEntity="Mensaje", mappedBy="retencion")
     */
    protected $mensajes;

    /**
     * @ORM\OneToMany(targetEntity="ImpuestoComprobanteRetencion", mappedBy="retencion" , cascade={"persist"})
     */
    protected $impuestos;

    /**
     * @ORM\OneToMany(targetEntity="CampoAdicional", mappedBy="retencion")
     */
    protected $composAdic;

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
        $this->mensajes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->impuestos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->composAdic = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Retencion
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
     * @return Retencion
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
     * @return Retencion
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
     * @return Retencion
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
     * @return Retencion
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
     * @return Retencion
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
     * @return Retencion
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
     * Set fechaEmision
     *
     * @param \DateTime $fechaEmision
     * @return Retencion
     */
    public function setFechaEmision($fechaEmision) {
        $this->fechaEmision = $fechaEmision;

        return $this;
    }

    /**
     * Get fechaEmision
     *
     * @return \DateTime 
     */
    public function getFechaEmision() {
        return $this->fechaEmision;
    }

    /**
     * Set periodoFiscal
     *
     * @param string $periodoFiscal
     * @return Retencion
     */
    public function setPeriodoFiscal($periodoFiscal) {
        $this->periodoFiscal = $periodoFiscal;

        return $this;
    }

    /**
     * Get periodoFiscal
     *
     * @return string 
     */
    public function getPeriodoFiscal() {
        return $this->periodoFiscal;
    }

    /**
     * Set nombreArchivo
     *
     * @param string $nombreArchivo
     * @return Retencion
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

    /**
     * Set cliente
     *
     * @param \FactelBundle\Entity\Cliente $cliente
     * @return Retencion
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
     * @return Retencion
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
     * @return Retencion
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
     * @return Retencion
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
     * Add mensajes
     *
     * @param \FactelBundle\Entity\Mensaje $mensajes
     * @return Retencion
     */
    public function addMensaje(\FactelBundle\Entity\Mensaje $mensajes) {
        $this->mensajes[] = $mensajes;

        return $this;
    }

    /**
     * Remove mensajes
     *
     * @param \FactelBundle\Entity\Mensaje $mensajes
     */
    public function removeMensaje(\FactelBundle\Entity\Mensaje $mensajes) {
        $this->mensajes->removeElement($mensajes);
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
     * Add impuestos
     *
     * @param \FactelBundle\Entity\ImpuestoComprobanteRetencion $impuestos
     * @return Retencion
     */
    public function addImpuesto(\FactelBundle\Entity\ImpuestoComprobanteRetencion $impuestos) {
        $this->impuestos[] = $impuestos;

        return $this;
    }

    /**
     * Remove impuestos
     *
     * @param \FactelBundle\Entity\ImpuestoComprobanteRetencion $impuestos
     */
    public function removeImpuesto(\FactelBundle\Entity\ImpuestoComprobanteRetencion $impuestos) {
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
     * Add composAdic
     *
     * @param \FactelBundle\Entity\CampoAdicional $composAdic
     * @return Retencion
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
     * Set firmado
     *
     * @param boolean $firmado
     * @return Retencion
     */
    public function setFirmado($firmado)
    {
        $this->firmado = $firmado;

        return $this;
    }

    /**
     * Get firmado
     *
     * @return boolean 
     */
    public function getFirmado()
    {
        return $this->firmado;
    }

    /**
     * Set enviarSiAutorizado
     *
     * @param boolean $enviarSiAutorizado
     * @return Retencion
     */
    public function setEnviarSiAutorizado($enviarSiAutorizado)
    {
        $this->enviarSiAutorizado = $enviarSiAutorizado;

        return $this;
    }

    /**
     * Get enviarSiAutorizado
     *
     * @return boolean 
     */
    public function getEnviarSiAutorizado()
    {
        return $this->enviarSiAutorizado;
    }
    
    public function getValorTotal(){
        $valor = 0.0;
        foreach($this->getImpuestos() as $impuesto){
            $valor += $impuesto->getValorRetenido();
        }
        
        return round($valor,2);
    }
}
