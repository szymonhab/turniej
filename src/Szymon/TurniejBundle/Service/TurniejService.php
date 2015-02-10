<?php

namespace Szymon\TurniejBundle\Service;

class TurniejService
{
	protected $session;
	protected $doctrine;
	
	public function getCurrentTurniej() {
		$turniej = $this->doctrine->getRepository('SzymonTurniejBundle:Turniej')
			->findOneBy(array('id' => $this->session->get('turniej_id')));
		if($turniej != null) {
			
			return $turniej;
		} else {
			$this->session->getFlashBag()->add('info', 'Nie wybrano turnieju!');

			return null;
		}
	}

	public function __construct($session, $doctrine)
	{
		$this->session = $session;
		$this->doctrine = $doctrine;
	}
}