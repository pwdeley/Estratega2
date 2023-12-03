<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Impuesto
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class LiquidacionCompraReembolso {

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
     * @ORM\Column(name="tipoProveedorReembolso", type="string", length=4)
     */
    protected $tipoProveedorReembolso;
        
    
    /**
     * @var string
     *
     * @ORM\Column(name="tipoIdentificacionProveedorReembolso", type="string", length=2)
     */
    protected $tipoIdentificacionProveedorReembolso;
    
    /**
     * @var string
     *
     * @ORM\Column(name="identificacionProveedorReembolso", type="string", length=20)
     */
    protected $identificacionProveedorReembolso;
    
    /**
     * @var string
     *
     * @ORM\Column(name="estabDocReembolso", type="string", length=3)
     */
    protected $estabDocReembolso;
    
    /**
     * @var string
     *
     * @ORM\Column(name="ptoEmiDocReembolso", type="string", length=3)
     */
    protected $ptoEmiDocReembolso;
    
    /**
     * @var string
     *
     * @ORM\Column(name="secuencialDocReembolso", type="string", length=20)
     */
    protected $secuencialDocReembolso;
    
    /**
     * @var string
     *
     * @ORM\Column(name="fechaEmisionDocReembolso", type="date")
     */
    protected $fechaEmisionDocReembolso;
    
    /**
     * @var string
     *
     * @ORM\Column(name="numeroautorizacionDocReemb", type="string", length=49)
     */
    protected $numeroautorizacionDocReemb;
    
    /**
     * @var string
     *
     * @ORM\Column(name="codDocReembolso", type="string", length=4)
     */
    protected $codDocReembolso;

    /**
     * @var integer
     *
     * @ORM\Column(name="baseImponibleReembolso", type="decimal", scale=2)
     */
    protected $baseImponibleReembolso;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="impuestoReembolso", type="decimal", scale=2)
     */
    protected $impuestoReembolso;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="baseImponibleSinIvaReembolso", type="decimal", scale=2)
     */
    protected $baseImponibleSinIvaReembolso;
    /**
     * @ORM\ManyToOne(targetEntity="LiquidacionCompra", inversedBy="reembolsos")
     * @ORM\JoinColumn(name="liquidacionCompra_id", referencedColumnName="id", nullable=false)
     */
    protected $liquidacionCompra;

   


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
     * Set tipoProveedorReembolso
     *
     * @param string $tipoProveedorReembolso
     *
     * @return LiquidacionCompraReembolso
     */
    public function setTipoProveedorReembolso($tipoProveedorReembolso)
    {
        $this->tipoProveedorReembolso = $tipoProveedorReembolso;

        return $this;
    }

    /**
     * Get tipoProveedorReembolso
     *
     * @return string
     */
    public function getTipoProveedorReembolso()
    {
        return $this->tipoProveedorReembolso;
    }

    /**
     * Set tipoIdentificacionProveedorReembolso
     *
     * @param string $tipoIdentificacionProveedorReembolso
     *
     * @return LiquidacionCompraReembolso
     */
    public function setTipoIdentificacionProveedorReembolso($tipoIdentificacionProveedorReembolso)
    {
        $this->tipoIdentificacionProveedorReembolso = $tipoIdentificacionProveedorReembolso;

        return $this;
    }

    /**
     * Get tipoIdentificacionProveedorReembolso
     *
     * @return string
     */
    public function getTipoIdentificacionProveedorReembolso()
    {
        return $this->tipoIdentificacionProveedorReembolso;
    }

    /**
     * Set identificacionProveedorReembolso
     *
     * @param string $identificacionProveedorReembolso
     *
     * @return LiquidacionCompraReembolso
     */
    public function setIdentificacionProveedorReembolso($identificacionProveedorReembolso)
    {
        $this->identificacionProveedorReembolso = $identificacionProveedorReembolso;

        return $this;
    }

    /**
     * Get identificacionProveedorReembolso
     *
     * @return string
     */
    public function getIdentificacionProveedorReembolso()
    {
        return $this->identificacionProveedorReembolso;
    }

    /**
     * Set estabDocReembolso
     *
     * @param string $estabDocReembolso
     *
     * @return LiquidacionCompraReembolso
     */
    public function setEstabDocReembolso($estabDocReembolso)
    {
        $this->estabDocReembolso = $estabDocReembolso;

        return $this;
    }

    /**
     * Get estabDocReembolso
     *
     * @return string
     */
    public function getEstabDocReembolso()
    {
        return $this->estabDocReembolso;
    }

    /**
     * Set ptoEmiDocReembolso
     *
     * @param string $ptoEmiDocReembolso
     *
     * @return LiquidacionCompraReembolso
     */
    public function setPtoEmiDocReembolso($ptoEmiDocReembolso)
    {
        $this->ptoEmiDocReembolso = $ptoEmiDocReembolso;

        return $this;
    }

    /**
     * Get ptoEmiDocReembolso
     *
     * @return string
     */
    public function getPtoEmiDocReembolso()
    {
        return $this->ptoEmiDocReembolso;
    }

    /**
     * Set secuencialDocReembolso
     *
     * @param string $secuencialDocReembolso
     *
     * @return LiquidacionCompraReembolso
     */
    public function setSecuencialDocReembolso($secuencialDocReembolso)
    {
        $this->secuencialDocReembolso = $secuencialDocReembolso;

        return $this;
    }

    /**
     * Get secuencialDocReembolso
     *
     * @return string
     */
    public function getSecuencialDocReembolso()
    {
        return $this->secuencialDocReembolso;
    }

    /**
     * Set fechaEmisionDocReembolso
     *
     * @param \DateTime $fechaEmisionDocReembolso
     *
     * @return LiquidacionCompraReembolso
     */
    public function setFechaEmisionDocReembolso($fechaEmisionDocReembolso)
    {
        $this->fechaEmisionDocReembolso = $fechaEmisionDocReembolso;

        return $this;
    }

    /**
     * Get fechaEmisionDocReembolso
     *
     * @return \DateTime
     */
    public function getFechaEmisionDocReembolso()
    {
        return $this->fechaEmisionDocReembolso;
    }

    /**
     * Set numeroautorizacionDocReemb
     *
     * @param string $numeroautorizacionDocReemb
     *
     * @return LiquidacionCompraReembolso
     */
    public function setNumeroautorizacionDocReemb($numeroautorizacionDocReemb)
    {
        $this->numeroautorizacionDocReemb = $numeroautorizacionDocReemb;

        return $this;
    }

    /**
     * Get numeroautorizacionDocReemb
     *
     * @return string
     */
    public function getNumeroautorizacionDocReemb()
    {
        return $this->numeroautorizacionDocReemb;
    }

    /**
     * Set codDocReembolso
     *
     * @param string $codDocReembolso
     *
     * @return LiquidacionCompraReembolso
     */
    public function setCodDocReembolso($codDocReembolso)
    {
        $this->codDocReembolso = $codDocReembolso;

        return $this;
    }

    /**
     * Get codDocReembolso
     *
     * @return string
     */
    public function getCodDocReembolso()
    {
        return $this->codDocReembolso;
    }

    /**
     * Set baseImponibleReembolso
     *
     * @param string $baseImponibleReembolso
     *
     * @return LiquidacionCompraReembolso
     */
    public function setBaseImponibleReembolso($baseImponibleReembolso)
    {
        $this->baseImponibleReembolso = $baseImponibleReembolso;

        return $this;
    }

    /**
     * Get baseImponibleReembolso
     *
     * @return string
     */
    public function getBaseImponibleReembolso()
    {
        return $this->baseImponibleReembolso;
    }

    /**
     * Set impuestoReembolso
     *
     * @param string $impuestoReembolso
     *
     * @return LiquidacionCompraReembolso
     */
    public function setImpuestoReembolso($impuestoReembolso)
    {
        $this->impuestoReembolso = $impuestoReembolso;

        return $this;
    }

    /**
     * Get impuestoReembolso
     *
     * @return string
     */
    public function getImpuestoReembolso()
    {
        return $this->impuestoReembolso;
    }

    /**
     * Set baseImponibleSinIvaReembolso
     *
     * @param string $baseImponibleSinIvaReembolso
     *
     * @return LiquidacionCompraReembolso
     */
    public function setBaseImponibleSinIvaReembolso($baseImponibleSinIvaReembolso)
    {
        $this->baseImponibleSinIvaReembolso = $baseImponibleSinIvaReembolso;

        return $this;
    }

    /**
     * Get baseImponibleSinIvaReembolso
     *
     * @return string
     */
    public function getBaseImponibleSinIvaReembolso()
    {
        return $this->baseImponibleSinIvaReembolso;
    }

    /**
     * Set liquidacionCompra
     *
     * @param \FactelBundle\Entity\LiquidacionCompra $liquidacionCompra
     *
     * @return LiquidacionCompraReembolso
     */
    public function setLiquidacionCompra(\FactelBundle\Entity\LiquidacionCompra $liquidacionCompra)
    {
        $this->liquidacionCompra = $liquidacionCompra;

        return $this;
    }

    /**
     * Get liquidacionCompra
     *
     * @return \FactelBundle\Entity\LiquidacionCompra
     */
    public function getLiquidacionCompra()
    {
        return $this->liquidacionCompra;
    }
}
