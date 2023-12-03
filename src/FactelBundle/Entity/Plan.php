<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Plan
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FactelBundle\Entity\Repository\PlanRepository")
 */
class Plan
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
     * @ORM\OneToMany(targetEntity="Emisor", mappedBy="plan")
     */
    protected $emisores;
    /**
     * @var integer
     *
     * @ORM\Column(name="cantComprobante", type="integer", nullable=false)
     */
    private $cantComprobante;

    /**
     * @ORM\Column(name="precio", type="decimal", scale=2, nullable=false)
     */
    protected $precio;
    
    /**
     * @var string
     *
     * @ORM\Column(name="periodo", type="string", length=49, nullable=false)
     */
    protected $periodo; 
    
    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="text")
     */
    private $observaciones;


    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable=TRUE)
     */
    protected $activo;
    
    
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
     * Set cantComprobante
     *
     * @param integer $cantComprobante
     *
     * @return Plan
     */
    public function setCantComprobante($cantComprobante)
    {
        $this->cantComprobante = $cantComprobante;

        return $this;
    }

    /**
     * Get cantComprobante
     *
     * @return integer
     */
    public function getCantComprobante()
    {
        return $this->cantComprobante;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return Plan
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set precio
     *
     * @param string $precio
     *
     * @return Plan
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio
     *
     * @return string
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set periodo
     *
     * @param string $periodo
     *
     * @return Plan
     */
    public function setPeriodo($periodo)
    {
        $this->periodo = $periodo;

        return $this;
    }

    /**
     * Get periodo
     *
     * @return string
     */
    public function getPeriodo()
    {
        return $this->periodo;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     *
     * @return Plan
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean
     */
    public function getActivo()
    {
        return $this->activo;
    }
    
    public function getNombre(){
        return $this->cantComprobante . " Doc. $" . $this->precio . " " .$this->periodo;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->emisores = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add emisore
     *
     * @param \FactelBundle\Entity\Emisor $emisore
     *
     * @return Plan
     */
    public function addEmisore(\FactelBundle\Entity\Emisor $emisore)
    {
        $this->emisores[] = $emisore;

        return $this;
    }

    /**
     * Remove emisore
     *
     * @param \FactelBundle\Entity\Emisor $emisore
     */
    public function removeEmisore(\FactelBundle\Entity\Emisor $emisore)
    {
        $this->emisores->removeElement($emisore);
    }

    /**
     * Get emisores
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEmisores()
    {
        return $this->emisores;
    }
}
