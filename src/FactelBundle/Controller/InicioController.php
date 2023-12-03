<?php

namespace FactelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FactelBundle\Entity\Emisor;

class InicioController extends Controller {

    /**
     * @Route("/", name="home")
     */
    public function inicioAction() {
        $em = $this->getDoctrine()->getManager();
        $emisorId = null;
        $idPtoEmision = null;
        if ($this->get("security.context")->isGranted("ROLE_EMISOR")) {
            if ($this->get("security.context")->isGranted("ROLE_EMISOR_ADMIN")) {
                $emisorId = $em->getRepository('FactelBundle:User')->findEmisorId($this->get("security.context")->gettoken()->getuser()->getId());
            } else {
                $idPtoEmision = $em->getRepository('FactelBundle:PtoEmision')->findIdPtoEmisionByUsuario($this->get("security.context")->gettoken()->getuser()->getId());
            }
            $registrados = 0;
            $autorizados = 0;
            $procesandose = 0;
            $noAutorizados = 0;
            $amo = date("Y");
            $amoanterior = $amo-1;


            $facturas = $em->getRepository('FactelBundle:Factura')->cantidadFacturasEstados($idPtoEmision, $emisorId);
            $ventasActual = $em->getRepository('FactelBundle:Factura')->ventaTotal($idPtoEmision, $emisorId, $amo);

            $ventaTotalActual = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            foreach ($ventasActual as $venta) {
                $ventaTotalActual[intval($venta['mes']) - 1] = floatval($venta['ventaTotal']);
            }

            $ventaAnnoAnterior = $em->getRepository('FactelBundle:Factura')->ventaTotal($idPtoEmision, $emisorId, $amoanterior);
 
            $ventaTotalAnnoAnterior = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            foreach ($ventaAnnoAnterior as $venta) {
                $ventaTotalAnnoAnterior[intval($venta['mes']) - 1] = floatval($venta['ventaTotal']);
            }
           
            $ventasXDia = $em->getRepository('FactelBundle:Factura')->ventaTotalXDia($idPtoEmision, $emisorId);
            $ventaXDiaTotal = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
            foreach ($ventasXDia as $venta) {
                $ventaXDiaTotal[intval($venta['dia']) - 1] = floatval($venta['ventaTotal']);
            }
            
            foreach ($facturas as $obj) {
                $registrados += intval($obj['cantidad']);
                if ($obj['estado'] == "AUTORIZADO") {
                    $autorizados += intval($obj['cantidad']);
                } else if ($obj['estado'] == "PORCESANDOSE") {
                    $procesandose += intval($obj['cantidad']);
                } else if ($obj['estado'] == "DEVUELTA" || $obj['estado'] == "NO AUTORIZADO") {
                    $noAutorizados += intval($obj['cantidad']);
                }
            }

            $notaCredito = $em->getRepository('FactelBundle:NotaCredito')->cantidadNotasCreditoEstados($idPtoEmision, $emisorId);
            foreach ($notaCredito as $obj) {
                $registrados += intval($obj['cantidad']);
                if ($obj['estado'] == "AUTORIZADO") {
                    $autorizados += intval($obj['cantidad']);
                } else if ($obj['estado'] == "PORCESANDOSE") {
                    $procesandose += intval($obj['cantidad']);
                } else if ($obj['estado'] == "DEVUELTA" || $obj['estado'] == "NO AUTORIZADO") {
                    $noAutorizados += intval($obj['cantidad']);
                }
            }

            $notaDebito = $em->getRepository('FactelBundle:NotaDebito')->cantidadNotasDebitoEstados($idPtoEmision, $emisorId);
            foreach ($notaDebito as $obj) {
                $registrados += intval($obj['cantidad']);
                if ($obj['estado'] == "AUTORIZADO") {
                    $autorizados += intval($obj['cantidad']);
                } else if ($obj['estado'] == "PORCESANDOSE") {
                    $procesandose += intval($obj['cantidad']);
                } else if ($obj['estado'] == "DEVUELTA" || $obj['estado'] == "NO AUTORIZADO") {
                    $noAutorizados += intval($obj['cantidad']);
                }
            }

            $retenciones = $em->getRepository('FactelBundle:Retencion')->cantidadRetencionesEstados($idPtoEmision, $emisorId);
            foreach ($retenciones as $obj) {
                $registrados += intval($obj['cantidad']);
                if ($obj['estado'] == "AUTORIZADO") {
                    $autorizados += intval($obj['cantidad']);
                } else if ($obj['estado'] == "PORCESANDOSE") {
                    $procesandose += intval($obj['cantidad']);
                } else if ($obj['estado'] == "DEVUELTA" || $obj['estado'] == "NO AUTORIZADO") {
                    $noAutorizados += intval($obj['cantidad']);
                }
            }
            $guias = $em->getRepository('FactelBundle:Guia')->cantidadGuiasEstados($idPtoEmision, $emisorId);
            foreach ($guias as $obj) {
                $registrados += intval($obj['cantidad']);
                if ($obj['estado'] == "AUTORIZADO") {
                    $autorizados += intval($obj['cantidad']);
                } else if ($obj['estado'] == "PORCESANDOSE") {
                    $procesandose += intval($obj['cantidad']);
                } else if ($obj['estado'] == "DEVUELTA" || $obj['estado'] == "NO AUTORIZADO") {
                    $noAutorizados += intval($obj['cantidad']);
                }
            }
            $liquidaciones = $em->getRepository('FactelBundle:LiquidacionCompra')->cantidadLiquidacionesEstados($idPtoEmision, $emisorId);
            foreach ($liquidaciones as $obj) {
                $registrados += intval($obj['cantidad']);
                if ($obj['estado'] == "AUTORIZADO") {
                    $autorizados += intval($obj['cantidad']);
                } else if ($obj['estado'] == "PORCESANDOSE") {
                    $procesandose += intval($obj['cantidad']);
                } else if ($obj['estado'] == "DEVUELTA" || $obj['estado'] == "NO AUTORIZADO") {
                    $noAutorizados += intval($obj['cantidad']);
                }
            }
            
            return $this->render("FactelBundle:Inicio:inicio.html.twig", array(
                        'registrados' => $registrados,
                        'autorizados' => $autorizados,
                        'procesandose' => $procesandose,
                        'noAutorizados' => $noAutorizados,
              			'amo' => $amo,
              			'amoanterior' => $amoanterior,
                        'ventaTotalActual' => implode(",", $ventaTotalActual),
                        'ventaTotalAnnoAnterior' => implode(",", $ventaTotalAnnoAnterior),
                'ventaXDiaTotal' =>implode(",", $ventaXDiaTotal)
            ));
        } else {
            return $this->render("FactelBundle:Inicio:inicio.html.twig", array()
            );
        }
    }

}
