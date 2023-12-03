<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CampoAdicional
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Motivo
{

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
     * @ORM\Column(name="nombre", type="string", length=60)
     */
    protected $razon;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="decimal", scale=2)
     */
    protected $valor;
    
    /**
     * @ORM\ManyToOne(targetEntity="NotaDebito", inversedBy="motivos")
     * @ORM\JoinColumn(name="dotaDebito_id", referencedColumnName="id")
     */
    protected $notaDebito;



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
     * Set razon
     *
     * @param string $razon
     * @return Motivo
     */
    public function setRazon($razon)
    {
        $this->razon = $razon;

        return $this;
    }

    /**
     * Get razon
     *
     * @return string 
     */
    public function getRazon()
    {
        return $this->razon;
    }

    /**
     * Set valor
     *
     * @param string $valor
     * @return Motivo
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
     * Set notaDebito
     *
     * @param \FactelBundle\Entity\NotaDebito $notaDebito
     * @return Motivo
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
}
