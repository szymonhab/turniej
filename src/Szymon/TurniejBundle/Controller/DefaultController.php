<?php

namespace Szymon\TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * 
 * @author Szymon Habela
 */
class DefaultController extends Controller
{
  /**
   * @Route("/", name="home")
   * @Template()
   */
  public function indexAction()
  {
  	
    return array();
  }
}