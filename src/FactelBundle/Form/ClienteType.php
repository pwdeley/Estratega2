<?php

namespace FactelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClienteType extends AbstractType {

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
                ->add('nombre')
                ->add('tipoIdentificacion', 'choice', array(
                     'placeholder' => 'Selecciona una opción',
                    'choices' => array(
                        '04' => 'RUC', 
                        '05' => 'CEDULA',
                        '06' => 'PASAPORTE',
                        '07' => 'CONSUMIDOR FINAL',
                        '08' => 'IDENTIFICACION DEL EXTERIOR',
                        '09' => 'PLACA',
                        ),
                    'required' => true,
                ))
                ->add('identificacion')
                ->add('direccion')
                ->add('celular')
                ->add('correoElectronico', 'email', [
                    'label' => 'Correo Electrónico',
                    'required' => false,
                ])
        ;
        if ($this->securityContext->isGranted("ROLE_ADMIN")) {
            $builder->add('emisor');
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'FactelBundle\Entity\Cliente'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'factelbundle_cliente';
    }

}
