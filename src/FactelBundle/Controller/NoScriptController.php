<?php

namespace FactelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FactelBundle\Entity\Cliente;
use FactelBundle\Form\ClienteType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cliente controller.
 *
 * @Route("/noscript")
 */
class NoScriptController extends Controller {

    /**
     * Lists all Cliente entities.
     *
     * @Route("/", name="no_script")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        return $this->render('FactelBundle:noScript:noScript.html.twig', array(
        ));
    }

}
