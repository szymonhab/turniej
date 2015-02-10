<?php

namespace Szymon\TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Szymon\TurniejBundle\Entity\Turniej;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/turniej")
 * 
 * @author Szymon Habela
 */
class TurniejController extends Controller
{
  /**
   * @Route("/nowy_turniej", name="nowy_turniej")
   * @Template()
   */
  public function nowyTurniejAction(Request $request)
  {
    $turniej = new Turniej();
    
    $form = $this->createFormBuilder($turniej)
        ->add('nazwaTurnieju', 'text', array('required' => 'true'))
        ->add('dataRozpoczecia', 'date', array('widget' => 'single_text', 
																	        		 'required' => 'true', 
																	        		 'attr' => array('class' => 'date')))
        ->add('zapisz', 'submit')
        ->getForm();
        
    $form->handleRequest($request);

    if ($form->isValid()) {
      $turniej->setRunda(Turniej::TURNIEJ_NIE_ROZPOCZETY);
      
      $em = $this->getDoctrine()->getManager();
      $em->persist($turniej);
      $em->flush();
      
      $session =  $this->get('session');
      $session->set('turniej_id', $turniej->getId());
      $session->set('nazwa_turnieju', $turniej->getNazwaTurnieju());
      $session->getFlashBag()->add('info', 'Dodano nowy turniej.');

      return $this->redirect($this->generateUrl('home'));
    }
    
    return array(
      'form' => $form->createView(),
    );    
  }
  
  /**
   * @Route("/wybierz_turniej", name="wybierz_turniej")
   * @Template()
   */
  public function wybierzTurniejAction(Request $request)
  {     
    $form = $this->createFormBuilder()
      ->add('Turniej', 'entity', array(
        'class' => 'SzymonTurniejBundle:Turniej',
        'property' => 'NazwaAndIdTurnieju',
        'query_builder' => function(\Szymon\TurniejBundle\Entity\TurniejRepository $repository) {
          return $repository->createQueryBuilder('t')
          ->orderBy('t.id', 'DESC');
          }
        )
      )
      ->add('zapisz', 'submit')
      ->getForm();
        
    $form->handleRequest($request);

    if ($form->isValid()) {         
      $session =  $this->get('session');
      $turniej = $form->get('Turniej')->getData();

      $session->set('turniej_id', $turniej->getId());
      $session->set('nazwa_turnieju', $turniej->getNazwaTurnieju());
      $session->getFlashBag()->add('info', 'Wybrano turniej: '.$turniej->getNazwaTurnieju().
      		', Data rozpoczęcia: '.$turniej->getDataRozpoczecia()->format('Y-m-d'));

      return $this->redirect($this->generateUrl('home'));
    }
    
    return array(
      'form' => $form->createView(),
    );  
  }
  
  /**
   * @Route("/usun_turniej", name="usun_turniej")
   * @Template()
   */
  public function usunTurniejAction(Request $request)
  {
    $form = $this->createFormBuilder()
      ->add('Turniej', 'entity', array(
        'class' => 'SzymonTurniejBundle:Turniej',
        'property' => 'NazwaAndIdTurnieju',
        'query_builder' => function(\Szymon\TurniejBundle\Entity\TurniejRepository $repository) {
          return $repository->createQueryBuilder('t')
          ->orderBy('t.id', 'DESC');
          }
        )
      )
      ->add('zapisz', 'submit')
      ->getForm();
        
    $form->handleRequest($request);

    if ($form->isValid()) {
      $session =  $this->get('session');
      $turniej = $form->get('Turniej')->getData();
      
      $nazwaTurnieju = $turniej->getNazwaTurnieju();
      $idTurnieju    = $turniej->getId();
      $em = $this->getDoctrine()->getManager();
      $em->remove($turniej);
      $em->flush();

      if($idTurnieju == $session->get('turniej_id')) {
	      $session->set('turniej_id', null);
	      $session->set('nazwa_turnieju', null);
      }
      
      $session->getFlashBag()->add('info', 'Usunięto turniej: '.$nazwaTurnieju);

      return $this->redirect($this->generateUrl('usun_turniej'));
    }
    
    return array(
      'form' => $form->createView(),
    );    
  }
}