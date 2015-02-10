<?php

namespace Szymon\TurniejBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Formularz ustalania następnej rundy
 *
 * @author Szymon Habela
 */
class NastepnaRundaType extends AbstractType
{
	
	/**
	 *
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('liczbaZawodnikow', 'integer', 
				array(
					'label'       => 'Liczba zawodników: ',
					'attr'        => array(
						'min'         => 1,
						'max'         => 4
					),
					'constraints' => array(
							new NotBlank(),
							new Range(array(
									'min' 		   => 1,
									'max' 			 => 4,
									'minMessage' => 'Do kolejnej rundy nie może przejść mniej niż jeden zawodnik',
									'maxMessage' => 'Do kolejnej rundy nie może przejść więcej niż czterech zawodników'
							)
						)
					)
				)
			)
			->add('save', 'submit', array('label' => 'Zatwierdź'))
			->getForm();
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
		return 'szymon_turniejbundle_nastepnaRunda';
	}
}
