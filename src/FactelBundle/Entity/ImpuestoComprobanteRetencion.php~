<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImpuestoComprobanteRetencion
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ImpuestoComprobanteRetencion
{
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
     * @ORM\Column(name="codigo", type="string", length=1)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="codigoRetencion", type="string", length=5)
     */
    private $codigoRetencion;

    /**
     * @var float
     *
     * @ORM\Column(name="baseImponible", type="decimal", scale=2)
     */
    protected $baseImponible;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="porcentajeRetener", type="integer")
     */
    protected $porcentajeRetener;
    
     /**
     * @var float
     *
     * @ORM\Column(name="valorRetenido", type="decimal", scale=2)
     */
    protected $valorRetenido;
    
    /**
     * @var string
     *
     * @ORM\Column(name="codDocSustento", type="string", length=2)
     */
    private $codDocSustento;

    /**
     * @var string
     *
     * @ORM\Column(name="numDocSustento", type="string", length=15)
     */
    private $numDocSustento;
    
     /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaEmisionDocSustento", type="date")
     */
    protected $fechaEmisionDocSustento;
    
    
     /**
     * @ORM\ManyToOne(targetEntity="Retencion", inversedBy="impuestos")
     * @ORM\JoinColumn(name="retencion_id", referencedColumnName="id", nullable=false)
     */
    protected $retencion;
    
    
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
     * Set codigo
     *
     * @param string $codigo
     * @return ImpuestoComprobanteRetencion
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string 
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set codigoRetencion
     *
     * @param string $codigoRetencion
     * @return ImpuestoComprobanteRetencion
     */
    public function setCodigoRetencion($codigoRetencion)
    {
        $this->codigoRetencion = $codigoRetencion;

        return $this;
    }

    /**
     * Get codigoRetencion
     *
     * @return string 
     */
    public function getCodigoRetencion()
    {
        return $this->codigoRetencion;
    }

    /**
     * Set baseImponible
     *
     * @param string $baseImponible
     * @return ImpuestoComprobanteRetencion
     */
    public function setBaseImponible($baseImponible)
    {
        $this->baseImponible = $baseImponible;

        return $this;
    }

    /**
     * Get baseImponible
     *
     * @return string 
     */
    public function getBaseImponible()
    {
        return $this->baseImponible;
    }

    /**
     * Set porcentajeRetener
     *
     * @param integer $porcentajeRetener
     * @return ImpuestoComprobanteRetencion
     */
    public function setPorcentajeRetener($porcentajeRetener)
    {
        $this->porcentajeRetener = $porcentajeRetener;

        return $this;
    }

    /**
     * Get porcentajeRetener
     *
     * @return integer 
     */
    public function getPorcentajeRetener()
    {
        return $this->porcentajeRetener;
    }

    /**
     * Set valorRetenido
     *
     * @param string $valorRetenido
     * @return ImpuestoComprobanteRetencion
     */
    public function setValorRetenido($valorRetenido)
    {
        $this->valorRetenido = $valorRetenido;

        return $this;
    }

    /**
     * Get valorRetenido
     *
     * @return string 
     */
    public function getValorRetenido()
    {
        return $this->valorRetenido;
    }

    /**
     * Set codDocSustento
     *
     * @param string $codDocSustento
     * @return ImpuestoComprobanteRetencion
     */
    public function setCodDocSustento($codDocSustento)
    {
        $this->codDocSustento = $codDocSustento;

        return $this;
    }

    /**
     * Get codDocSustento
     *
     * @return string 
     */
    public function getCodDocSustento()
    {
        return $this->codDocSustento;
    }

    /**
     * Set numDocSustento
     *
     * @param string $numDocSustento
     * @return ImpuestoComprobanteRetencion
     */
    public function setNumDocSustento($numDocSustento)
    {
        $this->numDocSustento = $numDocSustento;

        return $this;
    }

    /**
     * Get numDocSustento
     *
     * @return string 
     */
    public function getNumDocSustento()
    {
        return $this->numDocSustento;
    }

    /**
     * Set fechaEmisionDocSustento
     *
     * @param \DateTime $fechaEmisionDocSustento
     * @return ImpuestoComprobanteRetencion
     */
    public function setFechaEmisionDocSustento($fechaEmisionDocSustento)
    {
        $this->fechaEmisionDocSustento = $fechaEmisionDocSustento;

        return $this;
    }

    /**
     * Get fechaEmisionDocSustento
     *
     * @return \DateTime 
     */
    public function getFechaEmisionDocSustento()
    {
        return $this->fechaEmisionDocSustento;
    }

    /**
     * Set retencion
     *
     * @param \FactelBundle\Entity\Retencion $retencion
     * @return ImpuestoComprobanteRetencion
     */
    public function setRetencion(\FactelBundle\Entity\Retencion $retencion)
    {
        $this->retencion = $retencion;

        return $this;
    }

    /**
     * Get retencion
     *
     * @return \FactelBundle\Entity\Retencion 
     */
    public function getRetencion()
    {
        return $this->retencion;
    }
}
