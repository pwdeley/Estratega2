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
class Impuesto {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="FacturaHasProducto", inversedBy="impuestos")
     * @ORM\JoinColumn(name="facturaHasProducto_id", referencedColumnName="id")
     */
    protected $facturaHasProducto;
    
    /**
     * @ORM\ManyToOne(targetEntity="ProformaHasProducto", inversedBy="impuestos")
     * @ORM\JoinColumn(name="proformaHasProducto_id", referencedColumnName="id")
     */
    protected $proformaHasProducto;
    
    /**
     * @ORM\ManyToOne(targetEntity="LiquidacionCompraHasProducto", inversedBy="impuestos")
     * @ORM\JoinColumn(name="liquidacionHasProducto_id", referencedColumnName="id")
     */
    protected $liquidacionCompraHasProducto;
    
    /**
     * @ORM\ManyToOne(targetEntity="NotaCreditoHasProducto", inversedBy="impuestos")
     * @ORM\JoinColumn(name="notaCreditoHasProducto_id", referencedColumnName="id")
     */
    protected $notaCreditoHasProducto;
    
    /**
     * @ORM\ManyToOne(targetEntity="NotaDebito", inversedBy="impuestos")
     * @ORM\JoinColumn(name="notaDebito_id", referencedColumnName="id")
     */
    protected $notaDebito;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=1)
     */
    protected $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="codigoPorcentaje", type="string", length=4)
     */
    protected $codigoPorcentaje;

    /**
     * @var string
     *
     * @ORM\Column(name="tarifa", type="string", length=5, nullable=TRUE)
     */
    protected $tarifa;

    /**
     * @var string
     *
     * @ORM\Column(name="baseImponible", type="decimal", scale=2)
     */
    protected $baseImponible;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="decimal", scale=2)
     */
    protected $valor;

    

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
     * @return Impuesto
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
     * Set codigoPorcentaje
     *
     * @param string $codigoPorcentaje
     * @return Impuesto
     */
    public function setCodigoPorcentaje($codigoPorcentaje)
    {
        $this->codigoPorcentaje = $codigoPorcentaje;

        return $this;
    }

    /**
     * Get codigoPorcentaje
     *
     * @return string 
     */
    public function getCodigoPorcentaje()
    {
        return $this->codigoPorcentaje;
    }

    /**
     * Set tarifa
     *
     * @param string $tarifa
     * @return Impuesto
     */
    public function setTarifa($tarifa)
    {
        $this->tarifa = $tarifa;

        return $this;
    }

    /**
     * Get tarifa
     *
     * @return string 
     */
    public function getTarifa()
    {
        return $this->tarifa;
    }

    /**
     * Set baseImponible
     *
     * @param string $baseImponible
     * @return Impuesto
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
     * Set valor
     *
     * @param string $valor
     * @return Impuesto
     */
    public function setValor($valor)
    {
        $this->valor = $valor;

        return $this;
    }

    /**
     * Get valor
     *
     * @return string 
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * Set facturaHasProducto
     *
     * @param \FactelBundle\Entity\FacturaHasProducto $facturaHasProducto
     * @return Impuesto
     */
    public function setFacturaHasProducto(\FactelBundle\Entity\FacturaHasProducto $facturaHasProducto = null)
    {
        $this->facturaHasProducto = $facturaHasProducto;

        return $this;
    }

    /**
     * Get facturaHasProducto
     *
     * @return \FactelBundle\Entity\FacturaHasProducto 
     */
    public function getFacturaHasProducto()
    {
        return $this->facturaHasProducto;
    }

    /**
     * Set notaCreditoHasProducto
     *
     * @param \FactelBundle\Entity\NotaCreditoHasProducto $notaCreditoHasProducto
     * @return Impuesto
     */
    public function setNotaCreditoHasProducto(\FactelBundle\Entity\NotaCreditoHasProducto $notaCreditoHasProducto = null)
    {
        $this->notaCreditoHasProducto = $notaCreditoHasProducto;

        return $this;
    }

    /**
     * Get notaCreditoHasProducto
     *
     * @return \FactelBundle\Entity\NotaCreditoHasProducto 
     */
    public function getNotaCreditoHasProducto()
    {
        return $this->notaCreditoHasProducto;
    }

    /**
     * Set notaDebito
     *
     * @param \FactelBundle\Entity\NotaDebito $notaDebito
     * @return Impuesto
     */
    public function setNotaDebito(\FactelBundle\Entity\NotaDebito $notaDebito = null)
    {
        $this->notaDebito = $notaDebito;

        return $this;
    }

    /**
     * Get notaDebito
     *
     * @return \FactelBundle\Entity\NotaDebito 
     */
    public function getNotaDebito()
    {
        return $this->notaDebito;
    }

    /**
     * Set liquidacionCompraHasProducto
     *
     * @param \FactelBundle\Entity\LiquidacionCompraHasProducto $liquidacionCompraHasProducto
     *
     * @return Impuesto
     */
    public function setLiquidacionCompraHasProducto(\FactelBundle\Entity\LiquidacionCompraHasProducto $liquidacionCompraHasProducto = null)
    {
        $this->liquidacionCompraHasProducto = $liquidacionCompraHasProducto;

        return $this;
    }

    /**
     * Get liquidacionCompraHasProducto
     *
     * @return \FactelBundle\Entity\LiquidacionCompraHasProducto
     */
    public function getLiquidacionCompraHasProducto()
    {
        return $this->liquidacionCompraHasProducto;
    }

    /**
     * Set proformaHasProducto
     *
     * @param \FactelBundle\Entity\ProformaHasProducto $proformaHasProducto
     *
     * @return Impuesto
     */
    public function setProformaHasProducto(\FactelBundle\Entity\ProformaHasProducto $proformaHasProducto = null)
    {
        $this->proformaHasProducto = $proformaHasProducto;

        return $this;
    }

    /**
     * Get proformaHasProducto
     *
     * @return \FactelBundle\Entity\ProformaHasProducto
     */
    public function getProformaHasProducto()
    {
        return $this->proformaHasProducto;
    }
}
