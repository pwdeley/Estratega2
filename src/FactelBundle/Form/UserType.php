<?php

namespace FactelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class UserType extends AbstractType {

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
                ->add('username')
                ->add('nombre')
                ->add('apellidos')
                ->add('password', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'Las dos contraseñas deben coincidir',
                    'first_options' => array('label' => 'Contraseña'),
                    'second_options' => array('label' => 'Re-Contraseña'),
                    'required' => true))
                ->add('email', 'email', [
                    'label' => 'Email',
                ])
                ->add('isActive', 'checkbox', array(
                    'required' => false,
                    'label' => 'Activo'
                ))->add('copiarEmail', 'checkbox', array(
            'required' => false,
            'label' => 'Copia Email Comprobante'
        ));
        if ($this->securityContext->isGranted("ROLE_ADMIN")) {
            $builder->add('emisor');
            $builder->add('rol');
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
            $builder->add('rol', 'entity', array(
                'class' => 'FactelBundle:Role',
                'label' => 'Rol',
                'required' => true,
                'property' => 'name',
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->createQueryBuilder('role')
                                    ->andWhere('role.name != :roleName')
                                    ->setParameter("roleName", "ROLE_ADMIN");
                }));
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'FactelBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'factelbundle_user';
    }

}
