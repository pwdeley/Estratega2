<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
/**
 * Producto
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="FactelBundle\Entity\Repository\ProductoRepository")
 */
class Producto
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
     * @ORM\ManyToOne(targetEntity="Emisor", inversedBy="productos")
     * @ORM\JoinColumn(name="emisor_id", referencedColumnName="id", nullable=false)
     */
    protected $emisor;
    
    /**
     * @ORM\ManyToOne(targetEntity="ImpuestoIVA", inversedBy="productos")
     * @ORM\JoinColumn(name="impuesto_iva_id", referencedColumnName="id", nullable=false)
     */
    protected $impuestoIVA;
    
    /**
     * @ORM\ManyToOne(targetEntity="ImpuestoICE", inversedBy="productos")
     * @ORM\JoinColumn(name="impuesto_ice_id", referencedColumnName="id", nullable=true)
     */
    protected $impuestoICE;
    
    /**
     * @ORM\ManyToOne(targetEntity="ImpuestoIRBPNR", inversedBy="productos")
     * @ORM\JoinColumn(name="impuesto_irbpnr_id", referencedColumnName="id", nullable=true)
     */
    protected $impuestoIRBPNR;
    
    
    /**
     * @ORM\OneToMany(targetEntity="FacturaHasProducto", mappedBy="producto")
     */
    protected $facturasHasProducto;
    
    /**
     * @ORM\OneToMany(targetEntity="ProformaHasProducto", mappedBy="producto")
     */
    protected $proformasHasProducto;
    
    /**
     * @ORM\OneToMany(targetEntity="LiquidacionCompraHasProducto", mappedBy="liquidacionCompra")
     */
    protected $liquidacionesCompraHasProducto;
    
    /**
     * @ORM\OneToMany(targetEntity="GuiaHasProducto", mappedBy="producto")
     */
    protected $guiasHasProducto;
    
    /**
     * @ORM\OneToMany(targetEntity="NotaCreditoHasProducto", mappedBy="producto")
     */
    protected $notaCreditoHasProducto;
    
    /**
     * @var string
     *
     * @ORM\Column(name="codigoPrincipal", type="string", length=25)
     */
    protected $codigoPrincipal;

    /**
     * @var string
     *
     * @ORM\Column(name="codigoAuxiliar", type="string", length=25, nullable=TRUE)
     */
    protected $codigoAuxiliar;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=300)
     */
    protected $nombre;

    /**
     * @var integer
     *
     * @ORM\Column(name="precioUnitario", type="decimal", scale=2)
     */
    protected $precioUnitario;
   
    /**
     * @ORM\Column(name="tieneSubsidio", type="boolean", nullable=true)
     */
    private $tieneSubsidio;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="precioSinSubsidio", type="decimal", scale=2, nullable=true)
     */
    protected $precioSinSubsidio;
    
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->facturasHasProducto = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notaCreditoHasProducto = new \Doctrine\Common\Collections\ArrayCollection();
        $this->guiasHasProducto = new \Doctrine\Common\Collections\ArrayCollection();
        $this->liquidacionesCompraHasProducto = new \Doctrine\Common\Collections\ArrayCollection();
        $this->proformasHasProducto = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set codigoPrincipal
     *
     * @param string $codigoPrincipal
     * @return Producto
     */
    public function setCodigoPrincipal($codigoPrincipal)
    {
        $this->codigoPrincipal = $codigoPrincipal;

        return $this;
    }

    /**
     * Get codigoPrincipal
     *
     * @return string 
     */
    public function getCodigoPrincipal()
    {
        return $this->codigoPrincipal;
    }

    /**
     * Set codigoAuxiliar
     *
     * @param string $codigoAuxiliar
     * @return Producto
     */
    public function setCodigoAuxiliar($codigoAuxiliar)
    {
        $this->codigoAuxiliar = $codigoAuxiliar;

        return $this;
    }

    /**
     * Get codigoAuxiliar
     *
     * @return string 
     */
    public function getCodigoAuxiliar()
    {
        return $this->codigoAuxiliar;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Producto
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set precioUnitario
     *
     * @param float $precioUnitario
     * @return Producto
     */
    public function setPrecioUnitario($precioUnitario)
    {
        $this->precioUnitario = $precioUnitario;

        return $this;
    }

    /**
     * Get precioUnitario
     *
     * @return float 
     */
    public function getPrecioUnitario()
    {
        return $this->precioUnitario;
    }

    /**
     * Set emisor
     *
     * @param \FactelBundle\Entity\Emisor $emisor
     * @return Producto
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
     * Set impuestoIVA
     *
     * @param \FactelBundle\Entity\ImpuestoIVA $impuestoIVA
     * @return Producto
     */
    public function setImpuestoIVA(\FactelBundle\Entity\ImpuestoIVA $impuestoIVA)
    {
        $this->impuestoIVA = $impuestoIVA;

        return $this;
    }

    /**
     * Get impuestoIVA
     *
     * @return \FactelBundle\Entity\ImpuestoIVA 
     */
    public function getImpuestoIVA()
    {
        return $this->impuestoIVA;
    }

    /**
     * Set impuestoICE
     *
     * @param \FactelBundle\Entity\ImpuestoICE $impuestoICE
     * @return Producto
     */
    public function setImpuestoICE(\FactelBundle\Entity\ImpuestoICE $impuestoICE = null)
    {
        $this->impuestoICE = $impuestoICE;

        return $this;
    }

    /**
     * Get impuestoICE
     *
     * @return \FactelBundle\Entity\ImpuestoICE 
     */
    public function getImpuestoICE()
    {
        return $this->impuestoICE;
    }

    /**
     * Set impuestoIRBPNR
     *
     * @param \FactelBundle\Entity\ImpuestoIRBPNR $impuestoIRBPNR
     * @return Producto
     */
    public function setImpuestoIRBPNR(\FactelBundle\Entity\ImpuestoIRBPNR $impuestoIRBPNR = null)
    {
        $this->impuestoIRBPNR = $impuestoIRBPNR;

        return $this;
    }

    /**
     * Get impuestoIRBPNR
     *
     * @return \FactelBundle\Entity\ImpuestoIRBPNR 
     */
    public function getImpuestoIRBPNR()
    {
        return $this->impuestoIRBPNR;
    }

    /**
     * Add facturasHasProducto
     *
     * @param \FactelBundle\Entity\FacturaHasProducto $facturasHasProducto
     * @return Producto
     */
    public function addFacturasHasProducto(\FactelBundle\Entity\FacturaHasProducto $facturasHasProducto)
    {
        $this->facturasHasProducto[] = $facturasHasProducto;

        return $this;
    }

    /**
     * Remove facturasHasProducto
     *
     * @param \FactelBundle\Entity\FacturaHasProducto $facturasHasProducto
     */
    public function removeFacturasHasProducto(\FactelBundle\Entity\FacturaHasProducto $facturasHasProducto)
    {
        $this->facturasHasProducto->removeElement($facturasHasProducto);
    }

    /**
     * Get facturasHasProducto
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFacturasHasProducto()
    {
        return $this->facturasHasProducto;
    }

    /**
     * Add notaCreditoHasProducto
     *
     * @param \FactelBundle\Entity\NotaCreditoHasProducto $notaCreditoHasProducto
     * @return Producto
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
     * Add guiasHasProducto
     *
     * @param \FactelBundle\Entity\GuiaHasProducto $guiasHasProducto
     *
     * @return Producto
     */
    public function addGuiasHasProducto(\FactelBundle\Entity\GuiaHasProducto $guiasHasProducto)
    {
        $this->guiasHasProducto[] = $guiasHasProducto;

        return $this;
    }

    /**
     * Remove guiasHasProducto
     *
     * @param \FactelBundle\Entity\GuiaHasProducto $guiasHasProducto
     */
    public function removeGuiasHasProducto(\FactelBundle\Entity\GuiaHasProducto $guiasHasProducto)
    {
        $this->guiasHasProducto->removeElement($guiasHasProducto);
    }

    /**
     * Get guiasHasProducto
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGuiasHasProducto()
    {
        return $this->guiasHasProducto;
    }

    /**
     * Add liquidacionesCompraHasProducto
     *
     * @param \FactelBundle\Entity\LiquidacionCompraHasProducto $liquidacionesCompraHasProducto
     *
     * @return Producto
     */
    public function addLiquidacionesCompraHasProducto(\FactelBundle\Entity\LiquidacionCompraHasProducto $liquidacionesCompraHasProducto)
    {
        $this->liquidacionesCompraHasProducto[] = $liquidacionesCompraHasProducto;

        return $this;
    }

    /**
     * Remove liquidacionesCompraHasProducto
     *
     * @param \FactelBundle\Entity\LiquidacionCompraHasProducto $liquidacionesCompraHasProducto
     */
    public function removeLiquidacionesCompraHasProducto(\FactelBundle\Entity\LiquidacionCompraHasProducto $liquidacionesCompraHasProducto)
    {
        $this->liquidacionesCompraHasProducto->removeElement($liquidacionesCompraHasProducto);
    }

    /**
     * Get liquidacionesCompraHasProducto
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLiquidacionesCompraHasProducto()
    {
        return $this->liquidacionesCompraHasProducto;
    }

    /**
     * Set tieneSubsidio
     *
     * @param boolean $tieneSubsidio
     *
     * @return Producto
     */
    public function setTieneSubsidio($tieneSubsidio)
    {
        $this->tieneSubsidio = $tieneSubsidio;

        return $this;
    }

    /**
     * Get tieneSubsidio
     *
     * @return boolean
     */
    public function getTieneSubsidio()
    {
        return $this->tieneSubsidio;
    }

    /**
     * Set precioSinSubsidio
     *
     * @param string $precioSinSubsidio
     *
     * @return Producto
     */
    public function setPrecioSinSubsidio($precioSinSubsidio)
    {
        $this->precioSinSubsidio = $precioSinSubsidio;

        return $this;
    }

    /**
     * Get precioSinSubsidio
     *
     * @return string
     */
    public function getPrecioSinSubsidio()
    {
        return $this->precioSinSubsidio;
    }

    /**
     * Add proformasHasProducto
     *
     * @param \FactelBundle\Entity\ProformaHasProducto $proformasHasProducto
     *
     * @return Producto
     */
    public function addProformasHasProducto(\FactelBundle\Entity\ProformaHasProducto $proformasHasProducto)
    {
        $this->proformasHasProducto[] = $proformasHasProducto;

        return $this;
    }

    /**
     * Remove proformasHasProducto
     *
     * @param \FactelBundle\Entity\ProformaHasProducto $proformasHasProducto
     */
    public function removeProformasHasProducto(\FactelBundle\Entity\ProformaHasProducto $proformasHasProducto)
    {
        $this->proformasHasProducto->removeElement($proformasHasProducto);
    }

    /**
     * Get proformasHasProducto
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProformasHasProducto()
    {
        return $this->proformasHasProducto;
    }
}
