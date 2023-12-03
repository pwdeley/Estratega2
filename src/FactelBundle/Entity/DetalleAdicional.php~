<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetalleAdicional
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class DetalleAdicional
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
     * @ORM\ManyToOne(targetEntity="FacturaHasProducto", inversedBy="detallesAdicionales")
     * @ORM\JoinColumn(name="facturaHasProducto_id", referencedColumnName="id")
     */
    protected $facturaHasProducto;
    
     /**
     * @ORM\ManyToOne(targetEntity="NotaCreditoHasProducto", inversedBy="detallesAdicionales")
     * @ORM\JoinColumn(name="notaCreditoHasProducto_id", referencedColumnName="id")
     */
    protected $notaCreditoHasProducto;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=60)
     */
    protected $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="valor", type="string", length=255)
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
     * Set nombre
     *
     * @param string $nombre
     * @return DetalleAdicional
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
     * Set valor
     *
     * @param string $valor
     * @return DetalleAdicional
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
     * @return DetalleAdicional
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
     * @return DetalleAdicional
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
}
