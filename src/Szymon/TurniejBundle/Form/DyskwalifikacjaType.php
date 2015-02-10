<?php

namespace Szymon\TurniejBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Formularz dyskwalifikowania zawodnika
 * 
 * @author Szymon Habela
 */
class DyskwalifikacjaType extends AbstractType
{
	private $zawodnicy;
	
	/**
	 *
	 * @param FormBuilderInterface $builder        	
	 * @param array $options        	
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {

		$builder
			->add('zawodnicy', 'choice', 
				array (
					'choices' => $this->zawodnicy,
					'label' => 'Zawodnik: ' 
				)
			)
			->add('save', 'submit', array(
				'label' => 'Zatwierdź'
			))		
		;
	}
	
	/**
	 *
	 * @param OptionsResolverInterface $resolver        	
	 */
	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		
		return array(
      'zawodnicy' => true
    );
	}
	
	/**
	 *
	 * @return string
	 */
	public function getName() {
		return 'szymon_turniejbundle_szachownice';
	}
	
	public function __construct($zawodnicy)
	{
		$this->zawodnicy = $zawodnicy;
	}
}
