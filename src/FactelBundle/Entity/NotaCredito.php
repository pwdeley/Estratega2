<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Factura
 * @ORM\Entity(repositoryClass="FactelBundle\Entity\Repository\NotaCreditoRepository")
 */
class NotaCredito 
{
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
     * @ORM\Column(name="tipoDocMod", type="string", length=2)
     */
    protected $tipoDocMod;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaEmisionDocMod", type="date")
     */
    protected $fechaEmisionDocMod;

    /**
     * @var string
     *
     * @ORM\Column(name="nroDocMod", type="string", length=20)
     */
    protected $nroDocMod;
    
    /**
     * @var string
     *
     * @ORM\Column(name="motivo", type="string", length=300)
     */
    protected $motivo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nombreArchivo", type="string", length=200, nullable=true)
     */
    protected $nombreArchivo;
    
    /**
     * @ORM\ManyToOne(targetEntity="Cliente", inversedBy="notasCredito")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id", nullable=false)
     */
    protected $cliente;
    
    /**
     * @ORM\ManyToOne(targetEntity="Emisor", inversedBy="notasCredito")
     * @ORM\JoinColumn(name="emisor_id", referencedColumnName="id", nullable=false)
     */
    protected $emisor;
    
    /**
     * @ORM\ManyToOne(targetEntity="Establecimiento", inversedBy="notasCredito")
     * @ORM\JoinColumn(name="establecimiento_id", referencedColumnName="id", nullable=false)
     */
    protected $establecimiento;
    
    /**
     * @ORM\ManyToOne(targetEntity="PtoEmision", inversedBy="notasCredito")
     * @ORM\JoinColumn(name="ptoEmision_id", referencedColumnName="id", nullable=false)
     */
    protected $ptoEmision;
    
    /**
     * @ORM\OneToMany(targetEntity="Mensaje", mappedBy="notaCredito")
     */
    protected $mensajes;
    
   /**
     * @ORM\OneToMany(targetEntity="CampoAdicional", mappedBy="notaCredito")
     */
    protected $composAdic;
    

    /**
     * @ORM\OneToMany(targetEntity="NotaCreditoHasProducto", mappedBy="notaCredito", cascade={"persist"})
     */
    protected $notaCreditoHasProducto;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="totalSinImpuestos", type="decimal", scale=2)
     */
    protected $totalSinImpuestos;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="subtotal12", type="decimal", scale=2)
     */
    protected $subtotal12;
    
    /**
     * @var decimal
     *
     * @ORM\Column(name="subtotal0", type="decimal", scale=2)
     */
    protected $subtotal0;
    
    /**
     * @var float
     *
     * @ORM\Column(name="subtotalNoIVA", type="decimal", scale=2)
     */
    protected $subtotalNoIVA;
    
    /**
     * @var float
     *
     * @ORM\Column(name="subtotalExentoIVA", type="decimal", scale=2)
     */
    protected $subtotalExentoIVA;
    
    /**
     * @var float
     *
     * @ORM\Column(name="valorICE", type="decimal", scale=2)
     */
    protected $valorICE;
    
    /**
     * @var float
     *
     * @ORM\Column(name="valorIRBPNR", type="decimal", scale=2)
     */
    protected $valorIRBPNR;
    
    /**
     * @var float
     *
     * @ORM\Column(name="iva12", type="decimal", scale=2)
     */
    protected $iva12;
    
    

    /**
     * @var float
     *
     * @ORM\Column(name="totalDescuento", type="decimal", scale=2)
     */
    protected $totalDescuento;

    

    /**
     * @var float
     *
     * @ORM\Column(name="valorTotal", type="decimal", scale=2)
     */
    protected $valorTotal;

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
    public function __construct()
    {
        $this->composAdic = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notaCreditoHasProducto = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set claveAcceso
     *
     * @param string $claveAcceso
     * @return NotaCredito
     */
    public function setClaveAcceso($claveAcceso)
    {
        $this->claveAcceso = $claveAcceso;

        return $this;
    }

    /**
     * Get claveAcceso
     *
     * @return string 
     */
    public function getClaveAcceso()
    {
        return $this->claveAcceso;
    }

    /**
     * Set estado
     *
     * @param string $estado
     * @return NotaCredito
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string 
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set ambiente
     *
     * @param string $ambiente
     * @return NotaCredito
     */
    public function setAmbiente($ambiente)
    {
        $this->ambiente = $ambiente;

        return $this;
    }

    /**
     * Get ambiente
     *
     * @return string 
     */
    public function getAmbiente()
    {
        return $this->ambiente;
    }

    /**
     * Set tipoEmision
     *
     * @param string $tipoEmision
     * @return NotaCredito
     */
    public function setTipoEmision($tipoEmision)
    {
        $this->tipoEmision = $tipoEmision;

        return $this;
    }

    /**
     * Get tipoEmision
     *
     * @return string 
     */
    public function getTipoEmision()
    {
        return $this->tipoEmision;
    }

    /**
     * Set secuencial
     *
     * @param string $secuencial
     * @return NotaCredito
     */
    public function setSecuencial($secuencial)
    {
        $this->secuencial = $secuencial;

        return $this;
    }

    /**
     * Get secuencial
     *
     * @return string 
     */
    public function getSecuencial()
    {
        return $this->secuencial;
    }

    /**
     * Set fechaEmision
     *
     * @param \DateTime $fechaEmision
     * @return NotaCredito
     */
    public function setFechaEmision($fechaEmision)
    {
        $this->fechaEmision = $fechaEmision;

        return $this;
    }

    /**
     * Get fechaEmision
     *
     * @return \DateTime 
     */
    public function getFechaEmision()
    {
        return $this->fechaEmision;
    }

    /**
     * Set totalSinImpuestos
     *
     * @param string $totalSinImpuestos
     * @return NotaCredito
     */
    public function setTotalSinImpuestos($totalSinImpuestos)
    {
        $this->totalSinImpuestos = $totalSinImpuestos;

        return $this;
    }

    /**
     * Get totalSinImpuestos
     *
     * @return string 
     */
    public function getTotalSinImpuestos()
    {
        return $this->totalSinImpuestos;
    }

    /**
     * Set subtotal12
     *
     * @param string $subtotal12
     * @return NotaCredito
     */
    public function setSubtotal12($subtotal12)
    {
        $this->subtotal12 = $subtotal12;

        return $this;
    }

    /**
     * Get subtotal12
     *
     * @return string 
     */
    public function getSubtotal12()
    {
        return $this->subtotal12;
    }

    /**
     * Set subtotal0
     *
     * @param string $subtotal0
     * @return NotaCredito
     */
    public function setSubtotal0($subtotal0)
    {
        $this->subtotal0 = $subtotal0;

        return $this;
    }

    /**
     * Get subtotal0
     *
     * @return string 
     */
    public function getSubtotal0()
    {
        return $this->subtotal0;
    }

    /**
     * Set subtotalNoIVA
     *
     * @param string $subtotalNoIVA
     * @return NotaCredito
     */
    public function setSubtotalNoIVA($subtotalNoIVA)
    {
        $this->subtotalNoIVA = $subtotalNoIVA;

        return $this;
    }

    /**
     * Get subtotalNoIVA
     *
     * @return string 
     */
    public function getSubtotalNoIVA()
    {
        return $this->subtotalNoIVA;
    }

    /**
     * Set subtotalExentoIVA
     *
     * @param string $subtotalExentoIVA
     * @return NotaCredito
     */
    public function setSubtotalExentoIVA($subtotalExentoIVA)
    {
        $this->subtotalExentoIVA = $subtotalExentoIVA;

        return $this;
    }

    /**
     * Get subtotalExentoIVA
     *
     * @return string 
     */
    public function getSubtotalExentoIVA()
    {
        return $this->subtotalExentoIVA;
    }

    /**
     * Set valorICE
     *
     * @param string $valorICE
     * @return NotaCredito
     */
    public function setValorICE($valorICE)
    {
        $this->valorICE = $valorICE;

        return $this;
    }

    /**
     * Get valorICE
     *
     * @return string 
     */
    public function getValorICE()
    {
        return $this->valorICE;
    }

    /**
     * Set valorIRBPNR
     *
     * @param string $valorIRBPNR
     * @return NotaCredito
     */
    public function setValorIRBPNR($valorIRBPNR)
    {
        $this->valorIRBPNR = $valorIRBPNR;

        return $this;
    }

    /**
     * Get valorIRBPNR
     *
     * @return string 
     */
    public function getValorIRBPNR()
    {
        return $this->valorIRBPNR;
    }

    /**
     * Set iva12
     *
     * @param string $iva12
     * @return NotaCredito
     */
    public function setIva12($iva12)
    {
        $this->iva12 = $iva12;

        return $this;
    }

    /**
     * Get iva12
     *
     * @return string 
     */
    public function getIva12()
    {
        return $this->iva12;
    }

    /**
     * Set totalDescuento
     *
     * @param string $totalDescuento
     * @return NotaCredito
     */
    public function setTotalDescuento($totalDescuento)
    {
        $this->totalDescuento = $totalDescuento;

        return $this;
    }

    /**
     * Get totalDescuento
     *
     * @return string 
     */
    public function getTotalDescuento()
    {
        return $this->totalDescuento;
    }

    
    /**
     * Set valorTotal
     *
     * @param string $valorTotal
     * @return NotaCredito
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
     * Set cliente
     *
     * @param \FactelBundle\Entity\Cliente $cliente
     * @return NotaCredito
     */
    public function setCliente(\FactelBundle\Entity\Cliente $cliente)
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * Get cliente
     *
     * @return \FactelBundle\Entity\Cliente 
     */
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * Set emisor
     *
     * @param \FactelBundle\Entity\Emisor $emisor
     * @return NotaCredito
     */
    public function setEmisor(\FactelBundle\Entity\Emisor $emisor)
    {
        $this->emisor = $emisor;

        return $this;
    }

    /**
     * Get emisor
     *
     * @return \FactelBundle\Entity\Emisor 
     */
    public function getEmisor()
    {
        return $this->emisor;
    }

    /**
     * Set establecimiento
     *
     * @param \FactelBundle\Entity\Establecimiento $establecimiento
     * @return NotaCredito
     */
    public function setEstablecimiento(\FactelBundle\Entity\Establecimiento $establecimiento)
    {
        $this->establecimiento = $establecimiento;

        return $this;
    }

    /**
     * Get establecimiento
     *
     * @return \FactelBundle\Entity\Establecimiento 
     */
    public function getEstablecimiento()
    {
        return $this->establecimiento;
    }

    /**
     * Set ptoEmision
     *
     * @param \FactelBundle\Entity\PtoEmision $ptoEmision
     * @return NotaCredito
     */
    public function setPtoEmision(\FactelBundle\Entity\PtoEmision $ptoEmision)
    {
        $this->ptoEmision = $ptoEmision;

        return $this;
    }

    /**
     * Get ptoEmision
     *
     * @return \FactelBundle\Entity\PtoEmision 
     */
    public function getPtoEmision()
    {
        return $this->ptoEmision;
    }

    /**
     * Add composAdic
     *
     * @param \FactelBundle\Entity\CampoAdicional $composAdic
     * @return NotaCredito
     */
    public function addComposAdic(\FactelBundle\Entity\CampoAdicional $composAdic)
    {
        $this->composAdic[] = $composAdic;

        return $this;
    }

    /**
     * Remove composAdic
     *
     * @param \FactelBundle\Entity\CampoAdicional $composAdic
     */
    public function removeComposAdic(\FactelBundle\Entity\CampoAdicional $composAdic)
    {
        $this->composAdic->removeElement($composAdic);
    }

    /**
     * Get composAdic
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComposAdic()
    {
        return $this->composAdic;
    }

    /**
     * Add notaCreditoHasProducto
     *
     * @param \FactelBundle\Entity\NotaCreditoHasProducto $notaCreditoHasProducto
     * @return NotaCredito
     */
    public function addNotaCreditoHasProducto(\FactelBundle\Entity\NotaCreditoHasProducto $notaCreditoHasProducto)
    {
        $this->notaCreditoHasProducto[] = $notaCreditoHasProducto;

        return $this;
    }

    /**
     * Remove notaCreditoHasProducto
     *
     * @param \FactelBundle\Entity\NotaCreditoHasProducto $notaCreditoHasProducto
     */
    public function removeNotaCreditoHasProducto(\FactelBundle\Entity\NotaCreditoHasProducto $notaCreditoHasProducto)
    {
        $this->notaCreditoHasProducto->removeElement($notaCreditoHasProducto);
    }

    /**
     * Get notaCreditoHasProducto
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotaCreditoHasProducto()
    {
        return $this->notaCreditoHasProducto;
    }

    /**
     * Set tipoDocMod
     *
     * @param string $tipoDocMod
     * @return NotaCredito
     */
    public function setTipoDocMod($tipoDocMod)
    {
        $this->tipoDocMod = $tipoDocMod;

        return $this;
    }

    /**
     * Get tipoDocMod
     *
     * @return string 
     */
    public function getTipoDocMod()
    {
        return $this->tipoDocMod;
    }

    /**
     * Set fechaEmisionDocMod
     *
     * @param \DateTime $fechaEmisionDocMod
     * @return NotaCredito
     */
    public function setFechaEmisionDocMod($fechaEmisionDocMod)
    {
        $this->fechaEmisionDocMod = $fechaEmisionDocMod;

        return $this;
    }

    /**
     * Get fechaEmisionDocMod
     *
     * @return \DateTime 
     */
    public function getFechaEmisionDocMod()
    {
        return $this->fechaEmisionDocMod;
    }

    /**
     * Set nroDocMod
     *
     * @param string $nroDocMod
     * @return NotaCredito
     */
    public function setNroDocMod($nroDocMod)
    {
        $this->nroDocMod = $nroDocMod;

        return $this;
    }

    /**
     * Get nroDocMod
     *
     * @return string 
     */
    public function getNroDocMod()
    {
        return $this->nroDocMod;
    }

    /**
     * Set motivo
     *
     * @param string $motivo
     * @return NotaCredito
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;

        return $this;
    }

    /**
     * Get motivo
     *
     * @return string 
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Set numeroAutorizacion
     *
     * @param string $numeroAutorizacion
     * @return NotaCredito
     */
    public function setNumeroAutorizacion($numeroAutorizacion)
    {
        $this->numeroAutorizacion = $numeroAutorizacion;

        return $this;
    }

    /**
     * Get numeroAutorizacion
     *
     * @return string 
     */
    public function getNumeroAutorizacion()
    {
        return $this->numeroAutorizacion;
    }

    /**
     * Add mensajes
     *
     * @param \FactelBundle\Entity\Mensaje $mensajes
     * @return NotaCredito
     */
    public function addMensaje(\FactelBundle\Entity\Mensaje $mensajes)
    {
        $this->mensajes[] = $mensajes;

        return $this;
    }

    /**
     * Remove mensajes
     *
     * @param \FactelBundle\Entity\Mensaje $mensajes
     */
    public function removeMensaje(\FactelBundle\Entity\Mensaje $mensajes)
    {
        $this->mensajes->removeElement($mensajes);
    }

    /**
     * Get mensajes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMensajes()
    {
        return $this->mensajes;
    }

    /**
     * Set fechaAutorizacion
     *
     * @param \dateTime $fechaAutorizacion
     * @return NotaCredito
     */
    public function setFechaAutorizacion(\dateTime $fechaAutorizacion)
    {
        $this->fechaAutorizacion = $fechaAutorizacion;

        return $this;
    }

    /**
     * Get fechaAutorizacion
     *
     * @return \dateTime 
     */
    public function getFechaAutorizacion()
    {
        return $this->fechaAutorizacion;
    }

    /**
     * Set nombreArchivo
     *
     * @param string $nombreArchivo
     * @return NotaCredito
     */
    public function setNombreArchivo($nombreArchivo)
    {
        $this->nombreArchivo = $nombreArchivo;

        return $this;
    }

    /**
     * Get nombreArchivo
     *
     * @return string 
     */
    public function getNombreArchivo()
    {
        return $this->nombreArchivo;
    }

    /**
     * Set firmado
     *
     * @param boolean $firmado
     * @return NotaCredito
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
     * @return NotaCredito
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
}
