<?php

namespace FactelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Emisor
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Emisor {

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
     * @ORM\OneToMany(targetEntity="Establecimiento", mappedBy="emisor")
     */
    protected $establecimientos;

    /**
     * @ORM\OneToMany(targetEntity="Factura", mappedBy="emisor")
     */
    protected $facturas;

    /**
     * @ORM\OneToMany(targetEntity="Proforma", mappedBy="emisor")
     */
    protected $proformas;

    /**
     * @ORM\OneToMany(targetEntity="Compra", mappedBy="emisor")
     */
    protected $compras;

    /**
     * @ORM\OneToMany(targetEntity="LiquidacionCompra", mappedBy="emisor")
     */
    protected $liquidacionescompra;


    /**
     * @ORM\OneToMany(targetEntity="NotaCredito", mappedBy="emisor")
     */
    protected $notasCredito;

    /**
     * @ORM\OneToMany(targetEntity="NotaDebito", mappedBy="emisor")
     */
    protected $notasDebito;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="emisor")
     */
    protected $usuarios;

    /**
     * @ORM\OneToMany(targetEntity="Retencion", mappedBy="emisor")
     */
    protected $retencion;

    /**
     * @ORM\OneToMany(targetEntity="Guia", mappedBy="emisor")
     */
    protected $guias;

    /**
     * @ORM\OneToMany(targetEntity="Producto", mappedBy="emisor")
     */
    protected $productos;

    /**
     * @ORM\OneToMany(targetEntity="Cliente", mappedBy="emisor")
     */
    protected $clientes;

    /**
     * @ORM\OneToMany(targetEntity="CargaArchivo", mappedBy="emisor")
     */
    protected $cargaArchivos;


    /**
     * @ORM\ManyToOne(targetEntity="Plan", inversedBy="emisores")
     * @ORM\JoinColumn(name="plan_id", referencedColumnName="id")
     */
    protected $plan;

    /**
     * @var string
     *
     * @ORM\Column(name="ruc", type="string", length=13)
     */
    protected $ruc;

    /**
     * @var string
     *
     * @ORM\Column(name="ambiente", type="string", length=1)
     */
    protected $ambiente;

    /**
     * @var string
     *
     * @ORM\Column(name="tipoEmision", type="string", length=1)
     */
    protected $tipoEmision;

    /**
     * @var string
     * @ORM\Column(name="razonSocial", type="string", length=300)
     */
    protected $razonSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="nombreComercial", type="string", length=300, nullable=TRUE)
     */
    protected $nombreComercial;

    /**
     * @var string
     *
     * @ORM\Column(name="direccionMatriz", type="string", length=300)
     */
    protected $direccionMatriz;

    /**
     * @var string
     *
     * @ORM\Column(name="contribuyenteEspecial", type="string", length=13, nullable=TRUE)
     */
    protected $contribuyenteEspecial;

    /**
     * @var string
     *
     * @ORM\Column(name="obligadoContabilidad", type="string", length=2)
     */
    protected $obligadoContabilidad;

    /**
     * @Assert\File( maxSize = "1024k", mimeTypesMessage = "Favor subir un logo valido")
     * @var type
     */
    protected $logo;

    /**
     * @var string
     *
     * @ORM\Column(name="dirLogo", type="string", length=200)
     */
    protected $dirLogo;

    /**
     * @var string
     *
     * @ORM\Column(name="dirFirma", type="string", length=200)
     */
    protected $dirFirma;

    /**
     * @Assert\File( maxSize = "1024k", mimeTypesMessage = "Favor subir una firma valida")
     * @var type
     */
    protected $firma;

    /**
     * @var string
     *
     * @ORM\Column(name="dirDocAutorizados", type="string", length=200)
     */
    protected $dirDocAutorizados;

    /**
     * @var string
     *
     * @ORM\Column(name="passFirma", type="string")
     */
    protected $passFirma;

    /**
     * @var string
     *
     * @ORM\Column(name="servidorCorreo", type="string", length=200)
     */
    protected $servidorCorreo;

    /**
     * @var string
     *
     * @ORM\Column(name="correoRemitente", type="string", length=255)
     */
    protected $correoRemitente;

    /**
     * @var string
     *
     * @ORM\Column(name="passCorreo", type="string")
     */
    protected $passCorreo;

    /**
     * @var string
     *
     * @ORM\Column(name="puerto", type="integer")
     */
    protected $puerto;

    /**
     * @var boolean
     *
     * @ORM\Column(name="SSLHabilitado", type="boolean", nullable=TRUE)
     */
    protected $SSL;

    /**
     * @var boolean
     *
     * @ORM\Column(name="activo", type="boolean", nullable=TRUE)
     */
    protected $activo;

    /**
     * @var string
     *
     * @ORM\Column(name="cantComprobante", type="integer")
     */
    protected $cantComprobante = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaInicio", type="date", nullable=TRUE)
     */
    protected $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fechaFin", type="date", nullable=TRUE)
     */
    protected $fechaFin;

/**
     * @var boolean
     *
     * @ORM\Column(name="regimenRimpe", type="boolean", nullable=TRUE)
     */
    protected $regimenRimpe = false;

    /**
         * @var boolean
         *
         * @ORM\Column(name="regimenRimpe1", type="boolean", nullable=TRUE)
         */
        protected $regimenRimpe1 = false;

     /**
     * @var string
     *
     * @ORM\Column(name="resolucionAgenteRetencion", type="string", nullable=TRUE)
     */
    protected $resolucionAgenteRetencion;


    public function __toString() {
        return $this->razonSocial;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set ruc
     *
     * @param string $ruc
     * @return Emisor
     */
    public function setRuc($ruc) {
        $this->ruc = $ruc;

        return $this;
    }

    /**
     * Get ruc
     *
     * @return string
     */
    public function getRuc() {
        return $this->ruc;
    }

    /**
     * Set razonSocial
     *
     * @param string $razonSocial
     * @return Emisor
     */
    public function setRazonSocial($razonSocial) {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    /**
     * Get razonSocial
     *
     * @return string
     */
    public function getRazonSocial() {
        return $this->razonSocial;
    }

    /**
     * Set nombreComercial
     *
     * @param string $nombreComercial
     * @return Emisor
     */
    public function setNombreComercial($nombreComercial) {
        $this->nombreComercial = $nombreComercial;

        return $this;
    }

    /**
     * Get nombreComercial
     *
     * @return string
     */
    public function getNombreComercial() {
        return $this->nombreComercial;
    }

    /**
     * Set direccionMatriz
     *
     * @param string $direccionMatriz
     * @return Emisor
     */
    public function setDireccionMatriz($direccionMatriz) {
        $this->direccionMatriz = $direccionMatriz;

        return $this;
    }

    /**
     * Get direccionMatriz
     *
     * @return string
     */
    public function getDireccionMatriz() {
        return $this->direccionMatriz;
    }

    /**
     * Set contribuyenteEspecial
     *
     * @param string $contribuyenteEspecial
     * @return Emisor
     */
    public function setContribuyenteEspecial($contribuyenteEspecial) {
        $this->contribuyenteEspecial = $contribuyenteEspecial;

        return $this;
    }

    /**
     * Get contribuyenteEspecial
     *
     * @return string
     */
    public function getContribuyenteEspecial() {
        return $this->contribuyenteEspecial;
    }

    /**
     * Set obligadoContabilidad
     *
     * @param string $obligadoContabilidad
     * @return Emisor
     */
    public function setObligadoContabilidad($obligadoContabilidad) {
        $this->obligadoContabilidad = $obligadoContabilidad;

        return $this;
    }

    /**
     * Get obligadoContabilidad
     *
     * @return string
     */
    public function getObligadoContabilidad() {
        return $this->obligadoContabilidad;
    }

    /**
     * Set dirLogo
     *
     * @param string $dirLogo
     * @return Temp
     */
    public function setDirLogo($dirLogo) {
        $this->dirLogo = $dirLogo;

        return $this;
    }

    /**
     * Get dirLogo
     *
     * @return string
     */
    public function getDirLogo() {
        return $this->dirLogo;
    }

    /**
     * Set dirFirma
     *
     * @param string $dirFirma
     * @return Temp
     */
    public function setDirFirma($dirFirma) {
        $this->dirFirma = $dirFirma;

        return $this;
    }

    /**
     * Get dirFirma
     *
     * @return string
     */
    public function getDirFirma() {
        return $this->dirFirma;
    }

    /**
     * Set dirDocAutorizados
     *
     * @param string $dirDocAutorizados
     * @return Temp
     */
    public function setDirDocAutorizados($dirDocAutorizados) {
        $this->dirDocAutorizados = $dirDocAutorizados;

        return $this;
    }

    /**
     * Get dirDocAutorizados
     *
     * @return string
     */
    public function getDirDocAutorizados() {
        return $this->dirDocAutorizados;
    }

    /**
     * Set passFirma
     *
     * @param string $passFirma
     * @return Temp
     */
    public function setPassFirma($passFirma) {
        $this->passFirma = $passFirma;

        return $this;
    }

    /**
     * Get passFirma
     *
     * @return string
     */
    public function getPassFirma() {
        return $this->passFirma;
    }

    /**
     * Set servidorCorreo
     *
     * @param string $servidorCorreo
     * @return Temp
     */
    public function setServidorCorreo($servidorCorreo) {
        $this->servidorCorreo = $servidorCorreo;

        return $this;
    }

    /**
     * Get servidorCorreo
     *
     * @return string
     */
    public function getServidorCorreo() {
        return $this->servidorCorreo;
    }

    /**
     * Set correoRemitente
     *
     * @param string $correoRemitente
     * @return Temp
     */
    public function setCorreoRemitente($correoRemitente) {
        $this->correoRemitente = $correoRemitente;

        return $this;
    }

    /**
     * Get servidorCorreo
     *
     * @return string
     */
    public function getCorreoRemitente() {
        return $this->correoRemitente;
    }

    /**
     * Set passCorreo
     *
     * @param string $passCorreo
     * @return Temp
     */
    public function setPassCorreo($passCorreo) {
        $this->passCorreo = $passCorreo;

        return $this;
    }

    /**
     * Get passCorreo
     *
     * @return string
     */
    public function getPassCorreo() {
        return $this->passCorreo;
    }

    /**
     * Set puerto
     *
     * @param integer $puerto
     * @return Temp
     */
    public function setPuerto($puerto) {
        $this->puerto = $puerto;

        return $this;
    }

    /**
     * Get puerto
     *
     * @return integer
     */
    public function getPuerto() {
        return $this->puerto;
    }

    /**
     * Set SSL
     *
     * @param boolean $sSL
     * @return Temp
     */
    public function setSSL($sSL) {
        $this->SSL = $sSL;

        return $this;
    }

    /**
     * Get SSL
     *
     * @return boolean
     */
    public function getSSL() {
        return $this->SSL;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->establecimientos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->facturas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->compras = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notasCredito = new \Doctrine\Common\Collections\ArrayCollection();
        $this->notasDebito = new \Doctrine\Common\Collections\ArrayCollection();
        $this->clientes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->guias = new \Doctrine\Common\Collections\ArrayCollection();
        $this->liquidacionescompra = new \Doctrine\Common\Collections\ArrayCollection();
        $this->proformas = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add establecimientos
     *
     * @param \FactelBundle\Entity\Establecimiento $establecimientos
     * @return Emisor
     */
    public function addEstablecimiento(\FactelBundle\Entity\Establecimiento $establecimientos) {
        $this->establecimientos[] = $establecimientos;

        return $this;
    }

    /**
     * Remove establecimientos
     *
     * @param \FactelBundle\Entity\Establecimiento $establecimientos
     */
    public function removeEstablecimiento(\FactelBundle\Entity\Establecimiento $establecimientos) {
        $this->establecimientos->removeElement($establecimientos);
    }

    /**
     * Get establecimientos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEstablecimientos() {
        return $this->establecimientos;
    }

    /**
     * Set activo
     *
     * @param boolean $activo
     * @return Emisor
     */
    public function setActivo($activo) {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo
     *
     * @return boolean
     */
    public function getActivo() {
        return $this->activo;
    }

    /**
     * Add usuarios
     *
     * @param \FactelBundle\Entity\User $usuarios
     * @return Emisor
     */
    public function addUsuario(\FactelBundle\Entity\User $usuarios) {
        $this->usuarios[] = $usuarios;

        return $this;
    }

    /**
     * Remove usuarios
     *
     * @param \FactelBundle\Entity\User $usuarios
     */
    public function removeUsuario(\FactelBundle\Entity\User $usuarios) {
        $this->usuarios->removeElement($usuarios);
    }

    /**
     * Get usuarios
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuarios() {
        return $this->usuarios;
    }

    /**
     * Add productos
     *
     * @param \FactelBundle\Entity\Producto $productos
     * @return Emisor
     */
    public function addProducto(\FactelBundle\Entity\Producto $productos) {
        $this->productos[] = $productos;

        return $this;
    }

    /**
     * Remove productos
     *
     * @param \FactelBundle\Entity\Producto $productos
     */
    public function removeProducto(\FactelBundle\Entity\Producto $productos) {
        $this->productos->removeElement($productos);
    }

    /**
     * Get productos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductos() {
        return $this->productos;
    }

    /**
     * Add facturas
     *
     * @param \FactelBundle\Entity\Factura $facturas
     * @return Emisor
     */
    public function addFactura(\FactelBundle\Entity\Factura $facturas) {
        $this->facturas[] = $facturas;

        return $this;
    }

    /**
     * Remove facturas
     *
     * @param \FactelBundle\Entity\Factura $facturas
     */
    public function removeFactura(\FactelBundle\Entity\Factura $facturas) {
        $this->facturas->removeElement($facturas);
    }

    /**
     * Get facturas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFacturas() {
        return $this->facturas;
    }

    /**
     * Set ambiente
     *
     * @param string $ambiente
     * @return Emisor
     */
    public function setAmbiente($ambiente) {
        $this->ambiente = $ambiente;

        return $this;
    }

    /**
     * Get ambiente
     *
     * @return string
     */
    public function getAmbiente() {
        return $this->ambiente;
    }

    /**
     * Set tipoEmision
     *
     * @param string $tipoEmision
     * @return Emisor
     */
    public function setTipoEmision($tipoEmision) {
        $this->tipoEmision = $tipoEmision;

        return $this;
    }

    /**
     * Get tipoEmision
     *
     * @return string
     */
    public function getTipoEmision() {
        return $this->tipoEmision;
    }

    /**
     * Add notasCredito
     *
     * @param \FactelBundle\Entity\NotaCredito $notasCredito
     * @return Emisor
     */
    public function addNotasCredito(\FactelBundle\Entity\NotaCredito $notasCredito) {
        $this->notasCredito[] = $notasCredito;

        return $this;
    }

    /**
     * Remove notasCredito
     *
     * @param \FactelBundle\Entity\NotaCredito $notasCredito
     */
    public function removeNotasCredito(\FactelBundle\Entity\NotaCredito $notasCredito) {
        $this->notasCredito->removeElement($notasCredito);
    }

    /**
     * Get notasCredito
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotasCredito() {
        return $this->notasCredito;
    }

    /**
     * Add notasDebito
     *
     * @param \FactelBundle\Entity\NotaDebito $notasDebito
     * @return Emisor
     */
    public function addNotasDebito(\FactelBundle\Entity\NotaDebito $notasDebito) {
        $this->notasDebito[] = $notasDebito;

        return $this;
    }

    /**
     * Remove notasDebito
     *
     * @param \FactelBundle\Entity\NotaDebito $notasDebito
     */
    public function removeNotasDebito(\FactelBundle\Entity\NotaDebito $notasDebito) {
        $this->notasDebito->removeElement($notasDebito);
    }

    /**
     * Get notasDebito
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotasDebito() {
        return $this->notasDebito;
    }

    /**
     * Add retencion
     *
     * @param \FactelBundle\Entity\Retencion $retencion
     * @return Emisor
     */
    public function addRetencion(\FactelBundle\Entity\Retencion $retencion) {
        $this->retencion[] = $retencion;

        return $this;
    }

    /**
     * Remove retencion
     *
     * @param \FactelBundle\Entity\Retencion $retencion
     */
    public function removeRetencion(\FactelBundle\Entity\Retencion $retencion) {
        $this->retencion->removeElement($retencion);
    }

    /**
     * Get retencion
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRetencion() {
        return $this->retencion;
    }

    /**
     * Add clientes
     *
     * @param \FactelBundle\Entity\Cliente $clientes
     * @return Emisor
     */
    public function addCliente(\FactelBundle\Entity\Cliente $clientes) {
        $this->clientes[] = $clientes;

        return $this;
    }

    /**
     * Remove clientes
     *
     * @param \FactelBundle\Entity\Cliente $clientes
     */
    public function removeCliente(\FactelBundle\Entity\Cliente $clientes) {
        $this->clientes->removeElement($clientes);
    }

    /**
     * Get clientes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClientes() {
        return $this->clientes;
    }

    /**
     * Get fotoPerfil
     *
     * @return string
     */
    public function getLogo() {
        return $this->logo;
    }

    /**
     * Set fotoPerfil
     *
     * @param string $fotoPerfil
     *
     * @return User
     */
    public function setLogo($logo) {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get fotoPerfil
     *
     * @return string
     */
    public function getFirma() {
        return $this->firma;
    }

    /**
     * Set fotoPerfil
     *
     * @param string $fotoPerfil
     *
     * @return User
     */
    public function setFirma($firma) {
        $this->firma = $firma;

        return $this;
    }


    /**
     * Add guia
     *
     * @param \FactelBundle\Entity\Guia $guia
     *
     * @return Emisor
     */
    public function addGuia(\FactelBundle\Entity\Guia $guia)
    {
        $this->guias[] = $guia;

        return $this;
    }

    /**
     * Remove guia
     *
     * @param \FactelBundle\Entity\Guia $guia
     */
    public function removeGuia(\FactelBundle\Entity\Guia $guia)
    {
        $this->guias->removeElement($guia);
    }

    /**
     * Get guias
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGuias()
    {
        return $this->guias;
    }

     /**
     * Set plan
     *
     * @param \FactelBundle\Entity\Plan $plan
     *
     * @return Emisor
     */
    public function setPlan(\FactelBundle\Entity\Plan $plan) {
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get plan
     *
     * @return \FactelBundle\Entity\Plan
     */
    public function getPlan() {
        return $this->plan;
    }


    /**
     * Set cantComprobante
     *
     * @param integer $cantComprobante
     *
     * @return Emisor
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
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     *
     * @return Emisor
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     *
     * @return Emisor
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * Add cargaArchivo
     *
     * @param \FactelBundle\Entity\CargaArchivo $cargaArchivo
     *
     * @return Emisor
     */
    public function addCargaArchivo(\FactelBundle\Entity\CargaArchivo $cargaArchivo)
    {
        $this->cargaArchivos[] = $cargaArchivo;

        return $this;
    }

    /**
     * Remove cargaArchivo
     *
     * @param \FactelBundle\Entity\CargaArchivo $cargaArchivo
     */
    public function removeCargaArchivo(\FactelBundle\Entity\CargaArchivo $cargaArchivo)
    {
        $this->cargaArchivos->removeElement($cargaArchivo);
    }

    /**
     * Get cargaArchivos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCargaArchivos()
    {
        return $this->cargaArchivos;
    }

    /**
     * Add liquidacionescompra
     *
     * @param \FactelBundle\Entity\LiquidacionCompra $liquidacionescompra
     *
     * @return Emisor
     */
    public function addLiquidacionescompra(\FactelBundle\Entity\LiquidacionCompra $liquidacionescompra)
    {
        $this->liquidacionescompra[] = $liquidacionescompra;

        return $this;
    }

    /**
     * Remove liquidacionescompra
     *
     * @param \FactelBundle\Entity\LiquidacionCompra $liquidacionescompra
     */
    public function removeLiquidacionescompra(\FactelBundle\Entity\LiquidacionCompra $liquidacionescompra)
    {
        $this->liquidacionescompra->removeElement($liquidacionescompra);
    }

    /**
     * Get liquidacionescompra
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLiquidacionescompra()
    {
        return $this->liquidacionescompra;
    }

    /**
     * Add compra
     *
     * @param \FactelBundle\Entity\Compra $compra
     *
     * @return Emisor
     */
    public function addCompra(\FactelBundle\Entity\Compra $compra)
    {
        $this->compras[] = $compra;

        return $this;
    }

    /**
     * Remove compra
     *
     * @param \FactelBundle\Entity\Compra $compra
     */
    public function removeCompra(\FactelBundle\Entity\Compra $compra)
    {
        $this->compras->removeElement($compra);
    }

    /**
     * Get compras
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCompras()
    {
        return $this->compras;
    }

    /**
     * Add proforma
     *
     * @param \FactelBundle\Entity\Proforma $proforma
     *
     * @return Emisor
     */
    public function addProforma(\FactelBundle\Entity\Proforma $proforma)
    {
        $this->proformas[] = $proforma;

        return $this;
    }

    /**
     * Remove proforma
     *
     * @param \FactelBundle\Entity\Proforma $proforma
     */
    public function removeProforma(\FactelBundle\Entity\Proforma $proforma)
    {
        $this->proformas->removeElement($proforma);
    }

    /**
     * Get proformas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProformas()
    {
        return $this->proformas;
    }


    /**
     * Set regimenRimpe
     *
     * @param boolean $regimenRimpe
     *
     * @return Emisor
     */
    public function setRegimenRimpe($regimenRimpe)
    {
        $this->regimenRimpe = $regimenRimpe;

        return $this;
    }
    /**
     * Get regimenRimpe
     *
     * @return boolean
     */
    public function getRegimenRimpe()
    {
        return $this->regimenRimpe;
    }

    /**
     * Set regimenRimpe1
     *
     * @param boolean $regimenRimpe1
     *
     * @return Emisor
     */
    public function setRegimenRimpe1($regimenRimpe1)
    {
        $this->regimenRimpe1 = $regimenRimpe1;

        return $this;
    }

    /**
     * Get regimenMiRimpe1
     *
     * @return boolean
     */
    public function getRegimenRimpe1()
    {
        return $this->regimenRimpe1;
    }

    /**
     * Set resolucionAgenteRetencion
     *
     * @param string $resolucionAgenteRetencion
     *
     * @return Emisor
     */
    public function setResolucionAgenteRetencion($resolucionAgenteRetencion)
    {
        $this->resolucionAgenteRetencion = $resolucionAgenteRetencion;

        return $this;
    }

    /**
     * Get resolucionAgenteRetencion
     *
     * @return string
     */
    public function getResolucionAgenteRetencion()
    {
        return $this->resolucionAgenteRetencion;
    }
}
