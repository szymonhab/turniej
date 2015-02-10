<?php

namespace Szymon\TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Szymon\TurniejBundle\Entity\WynikRozgrywki;
use Szymon\TurniejBundle\Entity\Turniej;
use Szymon\TurniejBundle\Form\SzachowniceType;
use Szymon\TurniejBundle\Form\NastepnaRundaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\Range;
use Szymon\TurniejBundle\Entity\Grupa;
use Szymon\TurniejBundle\Entity\Zawodnik;

/**
 * @Route("/rozgrywka")
 * 
 * @author Szymon Habela
 */
class RozgrywkaController extends Controller
{
	/**
	 * Funkcja AJAX dla podliczania punktów
	 * 
	 * @Route("/podlicz_wyniki/{runda}", name="podlicz_wyniki")
	 * 
	 * return JsonResponse $wyniki
	 */
	public function pokazPunktacje(Request $request, $runda)
	{
		$turniej = $this->get('turniej')->getCurrentTurniej();
		if($turniej == null) {
			return $this->forward('SzymonTurniejBundle:Turniej:wybierzTurniej');
		}
		$jsArray = array();
		$grupy   = $this->getDoctrine()->getRepository('SzymonTurniejBundle:Grupa')->findAllRunda($turniej, $runda);
		
		for($i = 0; $i < count($grupy); $i++) {
			$grupa       = $grupy[$i];
			if($grupa->getRunda() == Turniej::TURNIEJ_ROZGRYWKI_FINALOWE) {
				$zawodnicy = $grupa->getZawodnicyFinalowi();
			} else {
				$zawodnicy = $grupa->getZawodnicy();
			}
			$wyniki      = $this->obliczWynikiZawodnikow($turniej, $zawodnicy);
			$jsArray[$i] = array();
			
			//Zamień tablice asocjacyjną na bardziej przyjazną dla javascriptu
			foreach($wyniki as $wynik) {
				$jsArray[$i][] = array(
					$wynik['zawodnik'],
					$wynik['punkty'],
					$wynik['punkty_pomocnicze'],
					$wynik['grupa'],
					$wynik['czyUsuniety']
				);
			}
			
			usort($jsArray[$i], array($this, 'compare_punkty'));
		}
		
		$result = array();
		foreach($jsArray as $jsRow) {
			foreach($jsRow as $wynik) {
				$result[] = $wynik;
			}
		}
		
		return new JsonResponse($result);
	}
	
  /**
  * @Route("/prowadz", name="prowadz_rozgrywke")
  * @Template("SzymonTurniejBundle:Rozgrywka:rozpocznij.html.twig")
  */
  public function rozpocznijAction(Request $request)
  {
  	$turniej = $this->get('turniej')->getCurrentTurniej();
  	if($turniej == null) {
  		return $this->forward('SzymonTurniejBundle:Turniej:wybierzTurniej');
  	}
  	 	
  	if($turniej->getRunda() == 0) {		
  		$form = $this->createForm(new SzachowniceType(), array('szachownice' => 8));
  		$form->handleRequest($request);
  		$arrayForRozpocznij = array('turniej' => $turniej, 'form' => $form->createView());

  		if ($form->isValid()) {
  			//Jeżeli są zawodnicy bez grup
  			if(count($this->getDoctrine()->getRepository('SzymonTurniejBundle:Zawodnik')->findZawodnicyBezGrupy($turniej)) > 0) {
  				$this->get('session')->getFlashBag()->add('info', 'Nie można rozpocząć turnieju, nie wszyscy zawodnicy mają przydzielone grupy');
  				return $arrayForRozpocznij;
  			}
  			if(count($this->getDoctrine()->getRepository('SzymonTurniejBundle:Grupa')->findBy(array('turniej' => $turniej))) == 0){
  				$this->get('session')->getFlashBag()->add('info', 'Nie można rozpocząć turnieju, grupy nie zostały stworzone');
  				return $arrayForRozpocznij;
  			}
  			
	  		$data = $form->getData();
	  		$this->rozpocznijTurniej($data, $turniej);
  		} else {
  			
	  		return $arrayForRozpocznij;
  		}
  	}
  	
  	$rozgrywki = $this->getDoctrine()->getRepository('SzymonTurniejBundle:WynikRozgrywki')->findByJeszczeNieZakonczona($turniej);
  	if (count ( $rozgrywki ) == 0) {
  		if($turniej->getRunda() == Turniej::TURNIEJ_ROZGRYWKI_FINALOWE) {
  			return $this->forward('SzymonTurniejBundle:Rozgrywka:zakonczTurniej');
  		}
  		if($turniej->getRunda() == Turniej::TURNIEJ_ZAKONCZONY) {
  			return $this->forward('SzymonTurniejBundle:Rozgrywka:wynikiOstateczne');
  		}
  		return $this->redirect($this->generateUrl('nastepna_runda'));
  	}
  	
		return $this->render('SzymonTurniejBundle:Rozgrywka:prowadz.html.twig', $this->getProwadzArray($turniej));
  }
  
  /**
   * @Route("/prowadz/rozpocznij_rozgrywke", name="rozpocznij_rozgrywke")
   * @Template("SzymonTurniejBundle:Rozgrywka:prowadz.html.twig")
   */
  public function rozpocznijRozgrywke(Request $request) {
  	$turniej = $this->get('turniej')->getCurrentTurniej();
  	if($turniej == null) {
  		return $this->forward('SzymonTurniejBundle:Turniej:wybierzTurniej');
  	}
  	
  	if($request->isMethod('POST')) {
  		$idRozgrywki = $request->request->get('id_wybranej_rozgrywki');

			if($idRozgrywki >= 0) {
	  		$wynikRozgrywki = $this->getDoctrine()->getRepository('SzymonTurniejBundle:WynikRozgrywki')
	  			->find($idRozgrywki);
	  		
	  		$wynikRozgrywki->setNrSzachownicy($request->request->get('id_wybranej_szachownicy'));
	  		$this->getDoctrine()->getManager()->flush($wynikRozgrywki);
			}
  	}
  	
  	$rozgrywki = $this->getDoctrine()->getRepository('SzymonTurniejBundle:WynikRozgrywki')->findByJeszczeNieZakonczona($turniej);
  	if (count ( $rozgrywki ) == 0) {
  		if($turniej->getRunda() == Turniej::TURNIEJ_ROZGRYWKI_FINALOWE) {
  			return $this->forward('SzymonTurniejBundle:Rozgrywka:zakonczTurniej');
  		}
  		if($turniej->getRunda() == Turniej::TURNIEJ_ZAKONCZONY) {
  			return $this->forward('SzymonTurniejBundle:Rozgrywka:wynikiOstateczne');
  		}
  		return $this->redirect($this->generateUrl('nastepna_runda'));
  	}
  	
  	return $this->getProwadzArray($turniej);
  }
  
  /**
   * @Route("/prowadz/zatwierdz_wynik", name="zatwierdz_wynik")
   * @Template("SzymonTurniejBundle:Rozgrywka:prowadz.html.twig")
   */
  public function zatwierdzWynik(Request $request) {
  	$turniej = $this->get('turniej')->getCurrentTurniej();
  	if($turniej == null) {
  		return $this->forward('SzymonTurniejBundle:Turniej:wybierzTurniej');
  	}
  	
  	if($request->isMethod('POST')) {
  		$ktoWygral = $request->request->get('kto_wygral');
  		$idRozgrywki = $request->request->get('id_rozgrywki');
  		
  		$rozgrywka = $this->getDoctrine()->getRepository('SzymonTurniejBundle:WynikRozgrywki')->find($idRozgrywki);
  		$rozgrywka->setWynik($ktoWygral);
  		$this->getDoctrine()->getManager()->flush($rozgrywka);
  	}
  	
  	$rozgrywki = $this->getDoctrine()->getRepository('SzymonTurniejBundle:WynikRozgrywki')->findByJeszczeNieZakonczona($turniej);
  	if (count ( $rozgrywki ) == 0) {
  		if($turniej->getRunda() == Turniej::TURNIEJ_ROZGRYWKI_FINALOWE) {
  			return $this->forward('SzymonTurniejBundle:Rozgrywka:zakonczTurniej');
  		}
  		if($turniej->getRunda() == Turniej::TURNIEJ_ZAKONCZONY) {
  			return $this->forward('SzymonTurniejBundle:Rozgrywka:wynikiOstateczne');
  		}
  		return $this->redirect($this->generateUrl('nastepna_runda'));
  	}
  			
		return $this->getProwadzArray ( $turniej );
	}
  
  /**
   * @Route("/prowadz/nastepna_runda", name="nastepna_runda")
   * @Template("SzymonTurniejBundle:Rozgrywka:nastepnaRunda.html.twig")
   */
  public function nastepnaRundaAction(Request $request) {
  	$turniej = $this->get('turniej')->getCurrentTurniej();
  	$rozgrywki = $this->getDoctrine()->getRepository('SzymonTurniejBundle:WynikRozgrywki')->findByJeszczeNieZakonczona($turniej);
  	
  	if (count ( $rozgrywki ) > 0) {
  		return $this->redirect($this->generateUrl('prowadz_rozgrywke'));
  	}
  	
  	$formDataArray = array(
  		'liczbaZawodnikow' => 1,
  		'liczbaSzachownic' => 4  			
  	);
  	
 		$form = $this->createForm(new NastepnaRundaType(), $formDataArray);
  	$form->handleRequest($request);
  	
  	if ($form->isValid()) {
  		$data             = $form->getData();
  		$liczbaZawodnikow = $data['liczbaZawodnikow'];
			$grupa            = $this->stworzFinalowaGrupe($turniej);
  		$zawodnicy        = $this->getFinalowiZawodnicy($turniej, $liczbaZawodnikow);
  		$em               = $this->getDoctrine()->getManager();

			foreach($zawodnicy as $zawodnik) {	
				$zawodnik->setGrupaFinalowa($grupa);
			}
			$turniej->setRunda(Turniej::TURNIEJ_ROZGRYWKI_FINALOWE);
			$insertCount = 0;
			$this->generujRozgrywkiGrupy($turniej, $zawodnicy, $insertCount);
			$em->flush();
  	
  		return $this->redirect($this->generateUrl('prowadz_rozgrywke'));
  	}
  	
  	return array(
  		'turniej' => $turniej,
  		'form'    => $form->createView()
  	);
  }
  
  /**
   * @Route("/rozgrywki_odbyte", name="rozgrywki_odbyte")
   * @Template()
   */
  public function rozgrywkiOdbyteAction(Request $request) {
  	$turniej = $this->get('turniej')->getCurrentTurniej();
  	if($turniej == null) {
  		return $this->forward('SzymonTurniejBundle:Turniej:wybierzTurniej');
  	}
  	$grupyChoices = $this->getDoctrine()->getRepository('SzymonTurniejBundle:Grupa')->getGrupyChoices($turniej);
  	
  	$form = $this->createFormBuilder(array('runda' => $turniej->getRunda()))
      ->add('runda', 'choice', array(
      		'choices' => array(Turniej::TURNIEJ_ROZGRYWKI => 'Runda pierwsza', Turniej::TURNIEJ_ROZGRYWKI_FINALOWE => 'Runda finałowa'),
      		'label'   => 'Wybierz rundę: '
      	)
      )
      ->add('grupa', 'choice', array(
      		'choices' => $grupyChoices,
      		'label'   => 'Wybierz grupę: '
      	)
      )
      ->add('save', 'submit', array('label' => 'Filtruj'))
      ->getForm();
      
    $form->handleRequest($request);
      
    if ($form->isValid()) {
    	$data  = $form->getData();
    	$runda = $data['runda'];
    	
    	$rozgrywki = $this->getDoctrine()->getRepository('SzymonTurniejBundle:WynikRozgrywki')
    		->findAllForHistoria($turniej, $runda, $data['grupa']);
    } else {
    	$runda = $turniej->getRunda();
    	
    	$rozgrywki = $this->getDoctrine()->getRepository('SzymonTurniejBundle:WynikRozgrywki')
    		->findAllForHistoria($turniej, $turniej->getRunda());
    }	
  	
  	if($request->isMethod('POST')) {
  		$ktoWygral = $request->request->get('kto_wygral');
  		$idRozgrywki = $request->request->get('id_rozgrywki');
  		
  		if($ktoWygral != null && $idRozgrywki != null) {
	  		$rozgrywka = $this->getDoctrine()->getRepository('SzymonTurniejBundle:WynikRozgrywki')->find($idRozgrywki);
	  		
	  		if($rozgrywka->getRunda() == $turniej->getRunda()) {
		  		$rozgrywka->setWynik($ktoWygral);
		  		$this->getDoctrine()->getManager()->flush($rozgrywka);
	  		} else {
	  			$this->get('session')->getFlashBag()->add('info', 'Nie można poprawiać rozgrywek dla zakończonej rundy');
	  		}
  		}
  	}
  	
  	return array(
  		'turniej'   => $turniej,
  		'rozgrywki' => $rozgrywki,
  		'runda'     => $runda,
  		'form'      => $form->createView(),
  	);
  }
  
  /**
   * @Route("/zakoncz_turniej", name="zakoncz_turniej")
   * @Template()
   */
  public function zakonczTurniejAction(Request $request) {
  	$turniej = $this->get('turniej')->getCurrentTurniej();
  	if($turniej == null) {
  		return $this->forward('SzymonTurniejBundle:Turniej:wybierzTurniej');
  	}
  	
  	$rozgrywki = $this->getDoctrine()->getRepository('SzymonTurniejBundle:WynikRozgrywki')->findByJeszczeNieZakonczona($turniej);
  	if(count($rozgrywki) == 0 && $turniej->getRunda() == Turniej::TURNIEJ_ROZGRYWKI_FINALOWE) {
		
  	} else {
  		return $this->forward('SzymonTurniejBundle:Turniej:wybierzTurniej');
  	}
  	
  	$form = $this->createFormBuilder()
  		->add('save', 'submit', array('label' => 'Zakończ'))
  		->getForm();
  	$form->handleRequest($request);
  	
  	if ($form->isValid()) {
  		$turniej->setRunda(Turniej::TURNIEJ_ZAKONCZONY);
  		$turniej->setDataZakonczenia(new \DateTime('now'));
  		$this->getDoctrine()->getManager()->flush($turniej);
  		
  		return $this->redirect($this->generateUrl('wyniki_ostateczne'));
  	}
  	
  	return array(
  		'turniej' => $turniej,
  		'form'    => $form->createView()
  	);
  }
  
  /**
   * @Route("wyniki_ostateczne", name="wyniki_ostateczne")
   * @Template()
   */
  public function wynikiOstateczneAction(Request $request) {
  	$turniej = $this->get('turniej')->getCurrentTurniej();
  	if($turniej == null || (!$turniej->getRunda() == Turniej::TURNIEJ_ZAKONCZONY)) {
  		return $this->forward('SzymonTurniejBundle:Turniej:wybierzTurniej');
  	}

  	$wyniki = $this->obliczWynikiZawodnikow($turniej, $turniej->getZawodnicy());
  	usort($wyniki, array($this, 'compare_punkty_grupa'));
  	
		return array(
			'wyniki'  => $wyniki,
			'turniej' => $turniej
		);
  }
  
  private function getProwadzArray($turniej) {
  	$aktualneRozgrywki = $this->getDoctrine()->getRepository('SzymonTurniejBundle:WynikRozgrywki')
  	->findAllByTurniejRozgrywane($turniej);
  	 
  	$dostepneRozgrywki = $this->getDoctrine()->getRepository('SzymonTurniejBundle:WynikRozgrywki')
  	->findDostepneRozgrywki($turniej, $aktualneRozgrywki);
  	 
  	return array(
  			'turniej' 	        => $turniej,
  			'rozgrywki'         => $aktualneRozgrywki,
  			'dostepneRozgrywki' => $dostepneRozgrywki,
  			'random'            => rand(1, count($dostepneRozgrywki))
  	);
  }
  
  private function rozpocznijTurniej($data, $turniej) {
  	$turniej->setRunda(Turniej::TURNIEJ_ROZGRYWKI);
  	$this->generujRozgrywki($turniej);
  	$turniej->setIloscSzachownic($data['szachownice']);
  	$this->getDoctrine()->getManager()->flush($turniej);
  }

	private function generujRozgrywki($turniej) 
	{
		$grupy = $turniej->getGrupy();
		$insertCount = 0;
		
		foreach($grupy as $grupa) {
			$zawodnicy = $grupa->getZawodnicy();
			
			$this->generujRozgrywkiGrupy($turniej, $zawodnicy, $insertCount);
		}
	}
	
	private function generujRozgrywkiGrupy($turniej, $zawodnicy, &$insertCount)
	{
		$em        = $this->getDoctrine()->getManager();
		$runda     = $turniej->getRunda();
		$wynikRepo = $this->getDoctrine()->getRepository('SzymonTurniejBundle:WynikRozgrywki');
		
		$kolorBialy = 0;
		for($i = 0; $i < count($zawodnicy); $i++) {
			for($j = $i+1; $j < count($zawodnicy); $j++) {
				if(empty($wynikRepo->findRozgrywka($zawodnicy[$i], $zawodnicy[$j]))) {
					$wynikRozgrywki = new WynikRozgrywki();
					if(($kolorBialy%2) == 0) {
						$zawodnik1 = $zawodnicy[$i];
						$zawodnik2 = $zawodnicy[$j];
					} else {
						$zawodnik1 = $zawodnicy[$j];
						$zawodnik2 = $zawodnicy[$i];
					}
					$wynikRozgrywki->setZawodnik1($zawodnik1);
					$wynikRozgrywki->setZawodnik2($zawodnik2);
					$wynikRozgrywki->setTurniej($turniej);
					$wynikRozgrywki->setRunda($runda);
					
					$em->persist($wynikRozgrywki);
					$insertCount++;
					$kolorBialy++;
					
					//Insert do bazy co 200 rekordów - czyszczenie pamięci ram
					if($insertCount > 200) {
						$em->flush();
						$insertCount = 0;
					}
				}
			}
		}
	}
	
	private function stworzFinalowaGrupe($turniej)
	{
		$grupa = $this->getDoctrine()->getRepository('SzymonTurniejBundle:Grupa')
			->findOneBy(array('runda' => Turniej::TURNIEJ_ROZGRYWKI_FINALOWE, 'turniej' => $turniej));
		
		if($grupa == null) {
			$em = $this->getDoctrine()->getManager();
			
			$grupa = new Grupa();
			$grupa->setRunda(Turniej::TURNIEJ_ROZGRYWKI_FINALOWE);
			$grupa->setNumerGrupy(1);
			$grupa->setTurniej($turniej);
			$grupa->setNazwaGrupy('Grupa finałowa');
			$em->persist($grupa);
			$em->flush($grupa);
		}
		
		return $grupa;
	}
	
	private function getFinalowiZawodnicy($turniej, $liczbaZawodnikow)
	{
		$grupy  = $this->getDoctrine()->getRepository('SzymonTurniejBundle:Grupa')->findAllRunda($turniej, Turniej::TURNIEJ_ROZGRYWKI);
		$result = array();
		
		foreach($grupy as $grupa) {
			$wyniki = $this->obliczWynikiZawodnikow($turniej, $grupa->getZawodnicy());
			usort($wyniki, array($this, 'compare_punkty_grupa'));
			
			for($i = 0; $i < $liczbaZawodnikow; $i++) {
				if(count($wyniki) >= $liczbaZawodnikow) {
					if($wyniki[$i]['zawodnikObj']->getCzyUsuniety() != 1) {
						$result[] = $wyniki[$i]['zawodnikObj'];
					} else {
						$liczbaZawodnikow++;
					}
				} else {
					$this->get('session')->getFlashBag()->add('info', 'Nie ma tylu zawodników w grupach ile zostało wskazane do przeniesienia do finału! 
							Zostali przeniesieni wszyscy zawodnicy w grupie');
				}
			}
		}

		return $result;
	}
	
	/**
	 * Funkcja ta liczy punkty zawodników wg punktacji Sonneberga-Bergera
	 * 
	 * @param Turniej $turniej
	 */
	private function obliczWynikiZawodnikow($turniej, $zawodnicy)
	{
		//Zaiinicjuj tablice
		$wyniki          = array();
		$zawodnikIds     = array();
		foreach($zawodnicy as $zawodnik) {
			$zawodnikIds[] = $zawodnik->getId();
		}
		$rozgrywkiOdbyte = $this->getDoctrine()->getRepository('SzymonTurniejBundle:WynikRozgrywki')
			->findAllForZawodnicy($turniej, $zawodnikIds);
		
		foreach($zawodnicy as $zawodnik) {
			$wyniki[$zawodnik->getId()] = array(
					'zawodnik'          => $zawodnik->getImie() . ' ' . $zawodnik->getNazwisko(),
					'punkty'            => 0,
					'punkty_pomocnicze' => 0,
					'grupa'             => $zawodnik->getGrupa()->getNazwaGrupy(),
					'zawodnikObj'       => $zawodnik,
					'czyUsuniety'       => $zawodnik->getCzyUsuniety()
			);
		}
		
		//Podlicz punkty
		foreach($rozgrywkiOdbyte as $rozgrywka) {
			$wyniki[$rozgrywka->getZawodnik1()->getId()]['punkty'] += $rozgrywka->getPunkty1();
			$wyniki[$rozgrywka->getZawodnik2()->getId()]['punkty'] += $rozgrywka->getPunkty2();
		}
		
		//Podlicz punkty dodatkowe
		foreach($rozgrywkiOdbyte as $rozgrywka) {
			switch($rozgrywka->getWynik()) {
				case 1:
					$wyniki[$rozgrywka->getZawodnik1()->getId()]['punkty_pomocnicze'] += $wyniki[$rozgrywka->getZawodnik2()->getId()]['punkty'];
					break;
				case 2:
					$wyniki[$rozgrywka->getZawodnik2()->getId()]['punkty_pomocnicze'] += $wyniki[$rozgrywka->getZawodnik1()->getId()]['punkty'];
					break;
				default:
					$wyniki[$rozgrywka->getZawodnik1()->getId()]['punkty_pomocnicze'] += 0.5*$wyniki[$rozgrywka->getZawodnik2()->getId()]['punkty'];
					$wyniki[$rozgrywka->getZawodnik2()->getId()]['punkty_pomocnicze'] += 0.5*$wyniki[$rozgrywka->getZawodnik1()->getId()]['punkty'];
					break;
			}
		}
		
		return $wyniki;
	}
	
	/**
	 * Funkcja pomocnicza do sortowania tablic
	 * 
	 * @param int $a
	 * @param int $b
	 * @return int
	 */
	function compare_punkty($a, $b) {
		if($a[1] == $b[1]) {
			if($a[2] == $b[2]) {
				return 0;
			}
			return ($a[2] < $b[2]) ? 1 : -1;
		}
		return ($a[1] < $b[1]) ? 1 : -1;
	}
	
	/**
	 * Funkcja pomocnicza do sortowania tablic
	 *
	 * @param int $a
	 * @param int $b
	 * @return int
	 */
	function compare_punkty_grupa($a, $b) {
		if($a['punkty'] == $b['punkty']) {
			if($a['punkty_pomocnicze'] == $b['punkty_pomocnicze']) {
				return 0;
			}
			return ($a['punkty_pomocnicze'] < $b['punkty_pomocnicze']) ? 1 : -1;
		}
		return ($a['punkty'] < $b['punkty']) ? 1 : -1;
	}
}