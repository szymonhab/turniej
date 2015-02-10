<?php

namespace Szymon\TurniejBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ZawodnikType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imie')
            ->add('nazwisko')
            ->add('rokUrodzenia', 'text', (array('required' => false)))
            ->add('katSzachowa', 'text')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Szymon\TurniejBundle\Entity\Zawodnik'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'szymon_turniejbundle_zawodnik';
    }
}
