<?php

namespace FactelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RetencionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('claveAcceso')
            ->add('numeroAutorizacion')
            ->add('fechaAutorizacion')
            ->add('estado')
            ->add('ambiente')
            ->add('tipoEmision')
            ->add('secuencial')
            ->add('fechaEmision')
            ->add('periodoFiscal')
            ->add('nombreArchivo')
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
            'data_class' => 'FactelBundle\Entity\Retencion'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'factelbundle_retencion';
    }
}
