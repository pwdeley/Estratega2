<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * CargaError
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CargaError {

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
     * @ORM\Column(name="message", type="string", length=400, nullable=TRUE)
     */
    protected $message;

    /**
     * @ORM\ManyToOne(targetEntity="CargaArchivo", inversedBy="errors")
     * @ORM\JoinColumn(name="carga_archivo_id", referencedColumnName="id", nullable=false)
     */
    protected $cargaArchivo;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }


    /**
     * Set message
     *
     * @param string $message
     *
     * @return CargaError
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set cargaArchivo
     *
     * @param \FactelBundle\Entity\CargaArchivo $cargaArchivo
     *
     * @return CargaError
     */
    public function setCargaArchivo(\FactelBundle\Entity\CargaArchivo $cargaArchivo)
    {
        $this->cargaArchivo = $cargaArchivo;

        return $this;
    }

    /**
     * Get cargaArchivo
     *
     * @return \FactelBundle\Entity\CargaArchivo
     */
    public function getCargaArchivo()
    {
        return $this->cargaArchivo;
    }
}
