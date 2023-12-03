<?php

namespace FactelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class PtoEmisionType extends AbstractType {

    private $securityContext;
    private $ptoEditadoId;

    public function __construct($securityContext, $ptoEditadoId = null) {
        $this->securityContext = $securityContext;
        $this->ptoEditadoId = $ptoEditadoId;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('nombre')
                ->add('codigo')
                ->add('secuencialFactura', 'number', array(
                    'label' => 'Secuencial Factura',
                    'required' => true,
                    'invalid_message' => 'Solo puede ingresar valores numericos'
                ))
                ->add('secuencialLiquidacionCompra', 'number', array(
                    'label' => 'Secuencial Liquidacion Compra',
                    'required' => true,
                    'invalid_message' => 'Solo puede ingresar valores numericos'
                ))
                ->add('secuencialNotaCredito', 'number', array(
                    'label' => 'Secuencial Nota Credito',
                    'required' => true,
                    'invalid_message' => 'Solo puede ingresar valores numericos'
                ))
                ->add('secuencialNotaDebito', 'number', array(
                    'label' => 'Secuencial NotaDebito',
                    'required' => true,
                    'invalid_message' => 'Solo puede ingresar valores numericos'
                ))
                ->add('secuencialGuiaRemision', 'number', array(
                    'label' => 'Secuencial Guia',
                    'required' => true,
                    'invalid_message' => 'Solo puede ingresar valores numericos'
                ))
                ->add('secuencialRetencion', 'number', array(
                    'label' => 'Secuencial Retencion',
                    'required' => true,
                    'invalid_message' => 'Solo puede ingresar valores numericos'
                ))
                ->add('activo');
        if ($this->securityContext->isGranted("ROLE_ADMIN")) {
            $builder->add('establecimiento', 'entity', array(
                'class' => 'FactelBundle:Establecimiento',
                'label' => 'Establecimiento',
                'required' => true,
                'query_builder' => function (EntityRepository $repo) {
            return $repo->createQueryBuilder('e')
                            ->select('estab, emisor')
                            ->from('FactelBundle:Establecimiento', 'estab')
                            ->join('estab.emisor', 'emisor');
        }));
        } else {

            $builder->add('establecimiento', 'entity', array(
                'class' => 'FactelBundle:Establecimiento',
                'label' => 'Establecimiento',
                'required' => true,
                'property' => 'nombre',
                'query_builder' => function (EntityRepository $repo) {
            return $repo->createQueryBuilder('e')
                            ->select('estab')
                            ->from('FactelBundle:Establecimiento', 'estab')
                            ->join('estab.emisor', 'emisor')
                            ->join('emisor.usuarios', 'users')
                            ->andWhere('users.id = :userId')
                            ->setParameter("userId", $this->securityContext->gettoken()->getuser()->getId());
        }));
        }

        if ($this->securityContext->isGranted("ROLE_EMISOR_ADMIN")) {
            $builder->add('usuario', 'entity', array(
                'class' => 'FactelBundle:User',
                'label' => 'Asignar a:',
                'required' => false,
                'property' => 'nombreCompleto',
                'placeholder' => 'Seleccione un Usuario',
                'query_builder' => function (EntityRepository $repo) {
            $qb = $repo->createQueryBuilder('up')
                    ->select('userPtoEmison')
                    ->from('FactelBundle:User', 'userPtoEmison')
                    ->join('userPtoEmison.ptoEmision', "ptoEmision")
            ;
            if ($this->ptoEditadoId) {
                $qb->andWhere('ptoEmision.id != :ptoEditadoId');
            }
            $query = $repo->createQueryBuilder('u')
                    ->select('user')
                    ->from('FactelBundle:User', 'user')
                    ->join('user.rol', 'rol')
                    ->join('user.emisor', 'emisor')
                    ->where('emisor.id = :emisorId')
                    ->andwhere('rol.name != :nombre')
                    ->andWhere($qb->expr()->notIn('user', $qb->getDql()))
                    ->setParameter('emisorId', $this->securityContext->gettoken()->getuser()->getEmisor()->getId())
                    ->setParameter('nombre', 'ROLE_ADMIN');
            if ($this->ptoEditadoId) {
                $query->setParameter('ptoEditadoId', $this->ptoEditadoId);
            }
            return $query;
        }));
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'FactelBundle\Entity\PtoEmision'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'factelbundle_ptoemision';
    }

}
