<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Factura
 * @ORM\Entity(repositoryClass="FactelBundle\Entity\Repository\NotaDebitoRepository")
 */
class NotaDebito 
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
     * @ORM\Column(name="nombreArchivo", type="string", length=200, nullable=true)
     */
    protected $nombreArchivo;
    
    /**
     * @ORM\ManyToOne(targetEntity="Cliente", inversedBy="notasDebito")
     * @ORM\JoinColumn(name="cliente_id", referencedColumnName="id", nullable=false)
     */
    protected $cliente;
    
    /**
     * @ORM\ManyToOne(targetEntity="Emisor", inversedBy="notasDebito")
     * @ORM\JoinColumn(name="emisor_id", referencedColumnName="id", nullable=false)
     */
    protected $emisor;
    
    /**
     * @ORM\ManyToOne(targetEntity="Establecimiento", inversedBy="notasDebito")
     * @ORM\JoinColumn(name="establecimiento_id", referencedColumnName="id", nullable=false)
     */
    protected $establecimiento;
    
    /**
     * @ORM\ManyToOne(targetEntity="PtoEmision", inversedBy="notasDebito")
     * @ORM\JoinColumn(name="ptoEmision_id", referencedColumnName="id", nullable=false)
     */
    protected $ptoEmision;
    
   /**
     * @ORM\OneToMany(targetEntity="CampoAdicional", mappedBy="notaDebito")
     */
    protected $composAdic;
    
    /**
     * @ORM\OneToMany(targetEntity="Mensaje", mappedBy="notaDebito")
     */
    protected $mensajes;
    
    /**
     * @ORM\OneToMany(targetEntity="Impuesto", mappedBy="notaDebito", cascade={"persist"})
     */
    protected $impuestos;

    /**
     * @ORM\OneToMany(targetEntity="Motivo", mappedBy="notaDebito", cascade={"persist"})
     */
    protected $motivos;
    
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
     * @ORM\Column(name="iva12", type="decimal", scale=2)
     */
    protected $iva12;
    


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
        $this->motivos = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set claveAcceso
     *
     * @param string $claveAcceso
     * @return NotaDebito
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
     * @return NotaDebito
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
     * @return NotaDebito
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
     * @return NotaDebito
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
     * @return NotaDebito
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
     * @return NotaDebito
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
     * Set tipoDocMod
     *
     * @param string $tipoDocMod
     * @return NotaDebito
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
     * @return NotaDebito
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
     * @return NotaDebito
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
     * Set totalSinImpuestos
     *
     * @param string $totalSinImpuestos
     * @return NotaDebito
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
     * @return NotaDebito
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
     * @return NotaDebito
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
     * @return NotaDebito
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
     * @return NotaDebito
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
     * @return NotaDebito
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
     * Set iva12
     *
     * @param string $iva12
     * @return NotaDebito
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
     * Set valorTotal
     *
     * @param string $valorTotal
     * @return NotaDebito
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
     * @return NotaDebito
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
     * @return NotaDebito
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
     * @return NotaDebito
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
     * @return NotaDebito
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
     * @return NotaDebito
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
     * Add motivos
     *
     * @param \FactelBundle\Entity\Motivo $motivos
     * @return NotaDebito
     */
    public function addMotivo(\FactelBundle\Entity\Motivo $motivos)
    {
        $this->motivos[] = $motivos;

        return $this;
    }

    /**
     * Remove motivos
     *
     * @param \FactelBundle\Entity\Motivo $motivos
     */
    public function removeMotivo(\FactelBundle\Entity\Motivo $motivos)
    {
        $this->motivos->removeElement($motivos);
    }

    /**
     * Get motivos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMotivos()
    {
        return $this->motivos;
    }

    /**
     * Add impuestos
     *
     * @param \FactelBundle\Entity\Impuesto $impuestos
     * @return NotaDebito
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
     * Set numeroAutorizacion
     *
     * @param string $numeroAutorizacion
     * @return NotaDebito
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
     * Set fechaAutorizacion
     *
     * @param \DateTime $fechaAutorizacion
     * @return NotaDebito
     */
    public function setFechaAutorizacion($fechaAutorizacion)
    {
        $this->fechaAutorizacion = $fechaAutorizacion;

        return $this;
    }

    /**
     * Get fechaAutorizacion
     *
     * @return \DateTime 
     */
    public function getFechaAutorizacion()
    {
        return $this->fechaAutorizacion;
    }

    /**
     * Set nombreArchivo
     *
     * @param string $nombreArchivo
     * @return NotaDebito
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
     * Add mensajes
     *
     * @param \FactelBundle\Entity\Mensaje $mensajes
     * @return NotaDebito
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
     * Set firmado
     *
     * @param boolean $firmado
     * @return NotaDebito
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
     * @return NotaDebito
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
