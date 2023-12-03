<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * CargaArchivo
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CargaArchivo {

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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="dirArchivo", type="string", length=200)
     */
    protected $dirArchivo;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", length=100)
     */
    protected $estado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="procesoAutomatico", type="boolean", nullable=TRUE)
     */
    protected $procesoAutomatico = false;

    /**
     * @var string
     *
     * @ORM\Column(name="inicioProcesamiento", type="datetime", nullable=TRUE)
     */
    protected $inicioProcesamiento;

    /**
     * @var string
     *
     * @ORM\Column(name="finProcesamiento", type="datetime", nullable=TRUE)
     */
    protected $finProcesamiento;

    /**
     * @ORM\OneToMany(targetEntity="CargaError", mappedBy="cargaArchivo")
     */
    protected $errors;
    
    /**
     * @ORM\ManyToOne(targetEntity="Emisor", inversedBy="cargaArchivos")
     * @ORM\JoinColumn(name="emisor_id", referencedColumnName="id", nullable=false)
     */
    protected $emisor;
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return CargaArchivo
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set dirArchivo
     *
     * @param string $dirArchivo
     *
     * @return CargaArchivo
     */
    public function setDirArchivo($dirArchivo) {
        $this->dirArchivo = $dirArchivo;

        return $this;
    }

    /**
     * Get dirArchivo
     *
     * @return string
     */
    public function getDirArchivo() {
        return $this->dirArchivo;
    }

    /**
     * Set estado
     *
     * @param string $estado
     *
     * @return CargaArchivo
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
     * Set procesoAutomatico
     *
     * @param boolean $procesoAutomatico
     *
     * @return CargaArchivo
     */
    public function setProcesoAutomatico($procesoAutomatico) {
        $this->procesoAutomatico = $procesoAutomatico;

        return $this;
    }

    /**
     * Get procesoAutomatico
     *
     * @return boolean
     */
    public function getProcesoAutomatico() {
        return $this->procesoAutomatico;
    }


    /**
     * Set inicioProcesamiento
     *
     * @param \DateTime $inicioProcesamiento
     *
     * @return CargaArchivo
     */
    public function setInicioProcesamiento($inicioProcesamiento)
    {
        $this->inicioProcesamiento = $inicioProcesamiento;

        return $this;
    }

    /**
     * Get inicioProcesamiento
     *
     * @return \DateTime
     */
    public function getInicioProcesamiento()
    {
        return $this->inicioProcesamiento;
    }

    /**
     * Set finProcesamiento
     *
     * @param \DateTime $finProcesamiento
     *
     * @return CargaArchivo
     */
    public function setFinProcesamiento($finProcesamiento)
    {
        $this->finProcesamiento = $finProcesamiento;

        return $this;
    }

    /**
     * Get finProcesamiento
     *
     * @return \DateTime
     */
    public function getFinProcesamiento()
    {
        return $this->finProcesamiento;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->errors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add error
     *
     * @param \FactelBundle\Entity\CargaError $error
     *
     * @return CargaArchivo
     */
    public function addError(\FactelBundle\Entity\CargaError $error)
    {
        $this->errors[] = $error;

        return $this;
    }

    /**
     * Remove error
     *
     * @param \FactelBundle\Entity\CargaError $error
     */
    public function removeError(\FactelBundle\Entity\CargaError $error)
    {
        $this->errors->removeElement($error);
    }

    /**
     * Get errors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set emisor
     *
     * @param \FactelBundle\Entity\Emisor $emisor
     *
     * @return CargaArchivo
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
}
