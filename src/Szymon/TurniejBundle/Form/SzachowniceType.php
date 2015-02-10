<?php

namespace Szymon\TurniejBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Formularz wybierania liczby szachownic
 * 
 * @author Szymon Habela
 */
class SzachowniceType extends AbstractType
{
	/**
	 *
	 * @param FormBuilderInterface $builder        	
	 * @param array $options        	
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
	
		$builder->add ( 'szachownice', 'integer', 
			array (
				'constraints' => array (
					new NotBlank(),
					new Range(array(
						'min' 		   => 1,
						'max' 			 => 20,
						'minMessage' => 'Nie można prowadzić turnieju na mniej niż jednej szachownicy',
						'maxMessage' => 'Program obsługuje maksymalnie dwadzieścia szachownic'
					))
				),
				'attr'        => array(
						'min'         => 1,
						'max'         => 20
				),
				'label' => 'Ilość szachownic: ' 
			) 
		);
	}
	
	/**
	 *
	 * @param OptionsResolverInterface $resolver        	
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
	}
	
	/**
	 *
	 * @return string
	 */
	public function getName() {
		return 'szymon_turniejbundle_szachownice';
	}
}
