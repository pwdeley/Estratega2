<?php

namespace FactelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class EstablecimientoType extends AbstractType {

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
                ->add('codigo')
                ->add('urlweb')
                ->add('nombreComercial')
                ->add('direccion')
                ->add('emailCopia',null, array(
                    'label'=>'Email Copia Oculta'
                ))
                ->add('activo');
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
        $builder->add('logo', 'file', array(
            'data_class' => 'Symfony\Component\HttpFoundation\File\File',
            'property_path' => 'logo',
            'required' => false
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'FactelBundle\Entity\Establecimiento'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'factelbundle_establecimiento';
    }

}
