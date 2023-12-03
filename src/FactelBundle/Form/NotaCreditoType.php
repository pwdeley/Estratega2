<?php

namespace FactelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NotaCreditoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('claveAcceso')
            ->add('estado')
            ->add('ambiente')
            ->add('tipoEmision')
            ->add('secuencial')
            ->add('fechaEmision')
            ->add('totalSinImpuestos')
            ->add('subtotal12')
            ->add('subtotal0')
            ->add('subtotalNoIVA')
            ->add('subtotalExentoIVA')
            ->add('valorICE')
            ->add('valorIRBPNR')
            ->add('iva12')
            ->add('totalDescuento')
            ->add('propina')
            ->add('valorTotal')
            ->add('cliente')
            ->add('emisor')
            ->add('establecimiento')
            ->add('ptoEmision')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FactelBundle\Entity\NotaCredito'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'factelbundle_notacredito';
    }
}
