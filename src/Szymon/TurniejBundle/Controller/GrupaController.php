<?php

namespace Szymon\TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Szymon\TurniejBundle\Entity\Grupa;
use Szymon\TurniejBundle\Entity\Turniej;
use Szymon\TurniejBundle\Lib\NazwyGrup;

/**
 * @Route("/grupy")
 * 
 * @author Szymon Habela
 */
class GrupaController extends Controller
{
	/**
	 * @Route("/", name="grupy")
	 * @Template()
	 */
	public function grupyAction()
	{
		$turniej = $this->get('turniej')->getCurrentTurniej();
		if($turniej == null) {
			return $this->redirect($this->generateUrl('SzymonTurniejBudnle:Turniej:wybierzTurniej'));
		}
		
		return array(
			'turniej' => $turniej
		);
	}
	
	/**
	 * @Route("/reset", name="reset")
	 * @Template("SzymonTurniejBundle:Grupa:grupy.html.twig")
	 */
	public function resetAction(Request $request)
	{
		$turniej = $this->get('turniej')->getCurrentTurniej();
		if($turniej == null) {
			return $this->forward('SzymonTurniejBundle:Turniej:wybierzTurniej');
		}
		if($turniej == null) {
			return $this->redirect($this->generateUrl('SzymonTurniejBudnle:Turniej:wybierzTurniej'));
		}
		
		if($request->isMethod('post')) {
			$em = $this->getDoctrine()->getManager();
			$grupy = $turniej->getGrupy();
			$turniej->setSposobPrzyporzadkowania(null);
			
			foreach($grupy as $grupa) {
				$em->remove($grupa);
			}
			$em->flush();
		}
		
		return array(
			'turniej' => $turniej
		);
	}
	
	/**
	 * @Route("/recznie", name="recznie")
	 * @Template("SzymonTurniejBundle:Grupa:rozmiesc.html.twig")
	 */
	public function recznieAction(Request $request) {
		$turniej = $this->get('turniej')->getCurrentTurniej();
		if($turniej == null) {
			return $this->forward('SzymonTurniejBundle:Turniej:wybierzTurniej');
		}
	
		if($request->isMethod('post')) {
			if($turniej->getRunda() == 0) {
				$grupa = $request->request->get('grupa');
					
				$em = $this->getDoctrine()->getManager();
				$zr = $this->getDoctrine()->getRepository('SzymonTurniejBundle:Zawodnik');
				foreach($grupa as $zawodnikId => $grupaId) {
					$zawodnik = $zr->find($zawodnikId);
					$zawodnik->setGrupa($em->getReference('SzymonTurniejBundle:Grupa', $grupaId));
				}
				$em->flush();
			} else {
				$this->get('session')->getFlashBag()->add('info', 'Nie można poprawić grup ponieważ turniej jest już w toku.');
			}
		}
	
		return array(
				'turniej' => $turniej
		);
	}
	
	/**
	 * @Route("/rozmiesc", name="rozmiesc")
	 * @Template("SzymonTurniejBundle:Grupa:rozmiesc.html.twig")
	 */
	public function rozmiescAction(Request $request)
	{
		$turniej = $this->get('turniej')->getCurrentTurniej();
		if($turniej == null) {
			return $this->forward('SzymonTurniejBundle:Turniej:wybierzTurniej');
		}
		
		if($request->isMethod('post')) {
			$iloscGrup = $request->get('group_amount');
			
			if($request->get('sort') == 'kat_szachowa') {
			  $this->rozmiescWgKategoriiSzachowej($turniej, $iloscGrup);
			} else if($request->get('sort') == 'wiek') {
				$this->rozmiescWgWieku($turniej, $iloscGrup);
			} else if($request->get('sort') == 'recznie') {
				$turniej->setSposobPrzyporzadkowania(3);
				$this->stworzGrupy($iloscGrup, $turniej);
			} else if($request->get('sort') == 'losowo') {
				$this->rozmiescLosowo($turniej, $iloscGrup);
			}
		}
	
		return array(
				'turniej' => $turniej
		);
	}
	
	private function rozmiescWgKategoriiSzachowej($turniej, $iloscGrup, $runda = 0) {
		$em = $this->getDoctrine()->getManager();
		$zawodnicy = $this->getDoctrine()->getRepository('SzymonTurniejBundle:Zawodnik')->findByTurniejOrderByKategoria($turniej);
		
		$grupy = $this->stworzGrupy($iloscGrup, $turniej);
		$turniej->setSposobPrzyporzadkowania(1);
		
		//Rozmieść zawodników
		$tmp = 0;
		foreach($zawodnicy as $zawodnik) {
			$zawodnik->setGrupa($grupy[$tmp]);
			
			($tmp < $iloscGrup - 1) ? $tmp++:	$tmp = 0;
		}
		$em->flush();
	}
	
	private function rozmiescWgWieku($turniej, $iloscGrup, $runda = 0) {
		$em = $this->getDoctrine()->getManager();
		$zawodnicy = $this->getDoctrine()->getRepository('SzymonTurniejBundle:Zawodnik')->findByTurniejOrderByRokUrodzenia($turniej);
	
		$grupy = $this->stworzGrupy($iloscGrup, $turniej);
		$turniej->setSposobPrzyporzadkowania(2);
		$zawodnicyLength = count($zawodnicy);
	
		//Rozmieść zawodników	
		$tmp = 0;
		$g = 0;
		for($i = 0; $i < $zawodnicyLength; $i++) {
			if($tmp >= $zawodnicyLength/$iloscGrup) {
				if($g < $iloscGrup-1) {
					$g++;
					$tmp = 0;
				}
			}
			$zawodnicy[$i]->setGrupa($grupy[$g]);
			$tmp++;
		}
		
		$em->flush();
	}
	
	private function rozmiescLosowo($turniej, $iloscGrup, $runda = 0) {
		$em = $this->getDoctrine()->getManager();
		$zawodnicy = $this->getDoctrine()->getRepository('SzymonTurniejBundle:Zawodnik')->findByTurniej($turniej);
		shuffle($zawodnicy);
	
		$grupy = $this->stworzGrupy($iloscGrup, $turniej);
		$turniej->setSposobPrzyporzadkowania(4);
	
		//Rozmieść zawodników
		$tmp = 0;
		foreach($zawodnicy as $zawodnik) {
			$zawodnik->setGrupa($grupy[$tmp]);
				
			($tmp < $iloscGrup - 1) ? $tmp++:	$tmp = 0;
		}
		$em->flush();
	}
	
	private function stworzGrupy($ilosc, $turniej) {
		$em = $this->getDoctrine()->getManager();
		$grupy = array();
		
		for($i = 0; $i < $ilosc; $i++) {
			$grupa = new Grupa();
			$grupa->setRunda(Turniej::TURNIEJ_ROZGRYWKI);
			$grupa->setNumerGrupy($i+1);
			$grupa->setTurniej($turniej);
			$grupa->setNazwaGrupy(NazwyGrup::getNazwaGrupy($i + 1));
			$grupy[] = $grupa;
			$em->persist($grupa);
		}
		
		return $grupy;
	}
}