<?php

namespace FactelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PlanType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('cantComprobante', 'number', array(
                    'label' => 'Cant Comprobante',
                    'required' => true,
                    'invalid_message' => 'Solo puede ingresar valores numericos'
                ))
                ->add('precio')
                ->add('periodo', 'choice', array(
                    'choices' => array(
                        'Mensual' => 'Mensual',
                        'Anual' => 'Anual',
                    ),
                    'required' => true,
                    'placeholder' => 'Seleccione el periodo',
                ))
                ->add('observaciones', 'text', array(
                    'label' => 'Observaciones'
                ))
                 ->add('activo')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'FactelBundle\Entity\Plan'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'factelbundle_plan';
    }

}
