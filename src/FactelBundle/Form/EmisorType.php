<?php

namespace FactelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmisorType extends AbstractType {

    private $securityContext;

    public function __construct($securityContext) {
        $this->securityContext = $securityContext;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('ruc', 'text', array(
                    'label' => 'RUC'
                ))
                ->add('razonSocial', 'text', array(
                    'label' => 'Razón Social'
                ))
                ->add('nombreComercial', 'text', array(
                    'label' => 'Nombre Comercial',
                    'required' => false,
                ))
                ->add('direccionMatriz', 'text', array(
                    'label' => 'Dirección Matriz'
                ))
                ->add('ambiente', 'choice', array(
                    'choices' => array(
                        '1' => 'Pruebas',
                        '2' => 'Producción'
                    ),
                    'required' => true,
                    'placeholder' => 'Seleccione el Ambiente',
                ))
                ->add('tipoEmision', 'choice', array(
                    'choices' => array(
                        '1' => 'Normal',
                        '2' => 'Indisponibilidad SRI',
                    ),
                    'required' => true,
                    'placeholder' => 'Seleccione el Tipo de Emisión',
                ))
                ->add('contribuyenteEspecial', 'text', array(
                    'label' => 'Contribuyente',
                    'required' => false,
                ))
                ->add('obligadoContabilidad', 'choice', array(
                    'choices' => array(
                        'SI' => 'SI',
                        'NO' => 'NO'
                    ),
                    'label' => 'Obligado Contabilidad',
                    'required' => true,
                    'placeholder' => 'Obligado Contabilidad?',
                ))
                ->add('passFirma', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'Las dos contraseñas deben coincidir',
                    'first_options' => array('label' => 'Contraseña Firma'),
                    'second_options' => array('label' => 'Re-Contraseña'),
                    'required' => true))
                ->add('servidorCorreo', 'text', array(
                    'label' => 'Servidor Correo'
                ))
                ->add('correoRemitente', 'email', array(
                    'label' => 'Correo Remitente'
                ))
                ->add('passCorreo', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'Las dos contraseñas deben coincidir',
                    'first_options' => array('label' => 'Contraseña Correo'),
                    'second_options' => array('label' => 'Re-Contraseña'),
                    'required' => true))
                ->add('puerto', 'text', array(
                    'label' => 'Puerto'
                ))
                ->add('SSL', 'checkbox', array(
                    'required' => false,
                    'label' => 'SSL?'
                ))->add('regimenRimpe', 'checkbox', array(
            'required' => false,
            'label' => 'Régimen RIMPE Popular?'
          ))->add('regimenRimpe1', 'checkbox', array(
      'required' => false,
      'label' => 'Régimen RIMPE Emprendedor?'
        ))->add('resolucionAgenteRetencion', 'text', array(
            'required' => false,
            'label' => 'Resolución Agente Retención'
        ));
        if ($this->securityContext->isGranted("ROLE_ADMIN") || $this->securityContext->isGranted("ROLE_EMISOR_ADMIN")) {
            $builder->add('logo', 'file', array(
                        'data_class' => 'Symfony\Component\HttpFoundation\File\File',
                        'property_path' => 'logo',
                        'required' => true
                    ))
                    ->add('firma', 'file', array(
                        'data_class' => 'Symfony\Component\HttpFoundation\File\File',
                        'property_path' => 'firma',
                        'required' => true
            ));
            if ($this->securityContext->isGranted("ROLE_ADMIN")) {
                $builder->add('activo', 'checkbox', array(
                    'required' => false,
                ))->add('dirDocAutorizados', 'text', array(
                    'label' => 'Ruta Autorizados'
                ));
            }
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'FactelBundle\Entity\Emisor'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'factelbundle_emisor';
    }

}
