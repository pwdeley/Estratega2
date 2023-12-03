<?php

namespace FactelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ProductoType extends AbstractType {

    private $securityContext;

    public function __construct($securityContext) {
        $this->securityContext = $securityContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('nombre')
                ->add('codigoPrincipal')
                ->add('codigoAuxiliar')
                ->add('precioUnitario');
        if ($this->securityContext->isGranted("ROLE_ADMIN")) {
            $builder->add('emisor');
        } else {

            $builder->add('emisor', 'entity', array(
                'class' => 'FactelBundle:Emisor',
                'label' => 'Emisor',
                'required' => true,
                'property' => 'razonSocial',
                'query_builder' => function (EntityRepository $repo) {
            return $repo->createQueryBuilder('e')
                            ->select('emisor')
                            ->from('FactelBundle:Emisor', 'emisor')
                            ->join('emisor.usuarios', 'users')
                            ->andWhere('users.id = :userId')
                            ->setParameter("userId", $this->securityContext->gettoken()->getuser()->getId());
        }));
        }
        $builder->add('impuestoIVA', 'entity', array(
                    'class' => 'FactelBundle:ImpuestoIVA',
                    'label' => 'Impuesto IVA',
                ))
                ->add('impuestoICE', 'entity', array(
                    'class' => 'FactelBundle:ImpuestoICE',
                    'label' => 'Impuesto ICE',
                    'required' => false,
                ))
                ->add('impuestoIRBPNR', 'entity', array(
                    'class' => 'FactelBundle:ImpuestoIRBPNR',
                    'label' => 'Impuesto IRBPNR',
                    'required' => false,
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'FactelBundle\Entity\Producto'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'factelbundle_producto';
    }

}
