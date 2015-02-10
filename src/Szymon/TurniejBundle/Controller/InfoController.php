<?php

namespace Szymon\TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/info")
 * 
 * @author Szymon Habela
 */
class InfoController extends Controller
{
  /**
   * @Route("/instrukcje", name="instrukcje")
   * @Template()
   */
  public function instrukcjeAction()
  {
  	
    return array();    
  }

  /**
   * @Route("/przygotowanie", name="przygotowanie")
   * @Template()
   */
  public function przygotowanieAction()
  {
  	
    return array();    
  }

  /**
   * @Route("/oProgramie", name="o_programie")
   * @Template()
   */
  public function oProgramieAction()
  {
  	
    return array();    
  }
  
  /**
   * @Route("/regulamin", name="regulamin")
   * @Template()
   */
  public function regulaminAction()
  {
  	 
  	return array();
  }
}