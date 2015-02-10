<?php

namespace Szymon\TurniejBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Szymon\TurniejBundle\Entity\Zawodnik;
use Szymon\TurniejBundle\Entity\Turniej;
use Szymon\TurniejBundle\Form\ZawodnikType;
use Szymon\TurniejBundle\Form\DyskwalifikacjaType;

/**
 * Zawodnik controller.
 *
 * @Route("/zawodnik")
 * 
 * @author Szymon Habela
 */
class ZawodnikController extends Controller
{

    /**
     * Lista zawodników
     *
     * @Route("/", name="zawodnik")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
    		$turniej = $this->get('turniej')->getCurrentTurniej();
        if($turniej == null) {
    			return $this->forward('SzymonTurniejBundle:Turniej:wybierzTurniej');
    		}
        //Zapytanie dla zawodników z sortowaniem
        $sort = $request->query->get('sort');
        $direction = $request->query->get('direction');
        $entities = $this->getDoctrine()->getManager()->getRepository('SzymonTurniejBundle:Zawodnik')->
        	findByTurniejAndSort($turniej, $sort, $direction);
        
        if(strtoupper($direction) == 'ASC') {
        	$descSort = $sort;
        } else {
        	$descSort = '';
        }

        return array(
            'entities' => $entities,
        		'descSort' => $descSort
        );
    }
    
    /**
     * Akcja tworzenia
     *
     * @Route("/", name="zawodnik_create")
     * @Method("POST")
     * @Template("SzymonTurniejBundle:Zawodnik:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $zawodnik = new Zawodnik();
        $form = $this->createCreateForm($zawodnik);
        $form->handleRequest($request);

        if ($form->isValid()) {
        	$turniej = $this->get('turniej')->getCurrentTurniej();
          if($turniej == null) {
    				return $this->forward('SzymonTurniejBundle:Turniej:wybierzTurniej');
    			}
        	if($turniej->getRunda() == 0) {
        		$em = $this->getDoctrine()->getManager();
        		$zawodnik->setTurniej($em->getReference('SzymonTurniejBundle:Turniej', $this->get('session')->get('turniej_id')));
            
        		//Sprawdzenie czy jest już zawodnik o takim samym imieniu i nazwisku
        		if($this->getDoctrine()->getRepository('SzymonTurniejBundle:Zawodnik')->findSimilar($turniej, $zawodnik)) {
        			$this->get('session')->getFlashBag()->add('info', 
        					'Uwaga, w systemie istnieje już zawodnik o takim samym 
        					imieniu i nazwisku! '.$zawodnik->getImie().' '.$zawodnik->getNazwisko()
       				);
        		}
        		
            $em->persist($zawodnik);
            $em->flush();

            return $this->redirect($this->generateUrl('zawodnik_show', array('id' => $zawodnik->getId())));
          } else {
            $this->get('session')->getFlashBag()->add('info', 'Nie można dodać zawodnika, turniej jest już w toku.');
          }
        }

        return array(
            'entity' => $zawodnik,
            'form'   => $form->createView(),
        );
    }

    /**
     * Formularz nowego zawodnika
     *
     * @param Zawodnik $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Zawodnik $entity)
    {
        $form = $this->createForm(new ZawodnikType(), $entity, array(
            'action' => $this->generateUrl('zawodnik_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Akcja nowy zawodnik - formularz
     *
     * @Route("/new", name="zawodnik_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Zawodnik();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Wyświetlenie zawodnika
     *
     * @Route("/show/{id}", name="zawodnik_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SzymonTurniejBundle:Zawodnik')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Zawodnik entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edycja zawodnika
     *
     * @Route("/edit/{id}", name="zawodnik_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SzymonTurniejBundle:Zawodnik')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Zawodnik entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Formularz tworzenia zawodnika
    *
    * @param Zawodnik $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Zawodnik $entity)
    {
        $form = $this->createForm(new ZawodnikType(), $entity, array(
            'action' => $this->generateUrl('zawodnik_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    /**
     * Edycja zawodnika
     *
     * @Route("/update/{id}", name="zawodnik_update")
     * @Method("PUT")
     * @Template("SzymonTurniejBundle:Zawodnik:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('SzymonTurniejBundle:Zawodnik')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Zawodnik entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('zawodnik_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    
    /**
     * Usuwanie zawodnika
     *
     * @Route("/delete/{id}", name="zawodnik_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $turniej = $this->get('turniej')->getCurrentTurniej();
        if($turniej == null) {
    			return $this->forward('SzymonTurniejBundle:Turniej:wybierzTurniej');
    		}
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
        	if($turniej->getRunda() == Turniej::TURNIEJ_NIE_ROZPOCZETY) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('SzymonTurniejBundle:Zawodnik')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Nie można znaleźć takiego zawodnika');
            }

            $em->remove($entity);
            $em->flush();
        	} else {
        		$this->get('session')->getFlashBag()->add('info', 'Nie można usunąć zawodnika, turniej jest już w toku.');
        	}
        }

        return $this->redirect($this->generateUrl('zawodnik'));
    }
    
    /**
     * Deletes a Zawodnik entity.
     *
     * @Route("/dyskwalifikacja", name="dyskwalifikacja")
     * @Template()
     */
    public function dyskwalifikacjaAction(Request $request)
    {
    	$turniej = $this->get('turniej')->getCurrentTurniej();
    	if($turniej == null) {
    		return $this->forward('SzymonTurniejBundle:Turniej:wybierzTurniej');
    	}
    	
    	if($turniej->getRunda() == Turniej::TURNIEJ_NIE_ROZPOCZETY) {
				$this->get('session')->getFlashBag()->add('info', 'Turniej jeszcze nie rozpoczęty, można normalnie usunąć zawodnika');
				
				return $this->redirect($this->generateUrl('zawodnik'));
    	}
    	
    	$zawodnicyRepository = $this->getDoctrine()->getRepository('SzymonTurniejBundle:Zawodnik');
    	$zawodnicy           = $zawodnicyRepository->findListForDyskwalifikacja($turniej);
    	$form                = $this->createForm(new DyskwalifikacjaType($zawodnicy), null);
    	$form->handleRequest($request);
    	
    	if ($form->isValid()) {
    		$data = $form->getData();
    		$zawodnikId = $data['zawodnicy'];
    		
    		$zawodnik = $zawodnicyRepository->find($zawodnikId);
    		$zawodnik->setCzyUsuniety(1);
    		$rozgrywki = array_merge($zawodnik->getWynikRozgrywki1()->toArray(), $zawodnik->getWynikRozgrywki2()->toArray());
    		foreach($rozgrywki as $rozgrywka) {
    			if($rozgrywka->getWynik() == null) {
    				if($rozgrywka->getZawodnik1() == $zawodnik) {
    					$rozgrywka->setWynik(2);
    				} else {
    					$rozgrywka->setWynik(1);
    				}
    			}
    		}
    		
    		$this->getDoctrine()->getManager()->flush();
    		
    		$this->get('session')->getFlashBag()->add('info', 'Zawodnik został zdyskwalifikowany! 
    				Wszystkie nie rozegrane do tej pory rozgrywki przez tego zawodnika zostały uzupełnione.');
    		return $this->redirect($this->generateUrl('zawodnik'));
    	}
    	
    	return array(
    		'form' => $form->createView()
    	);
    }
    
    /**
     * Formularz usuwania zawodnika
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('zawodnik_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Usuń', 'attr'=> array('class' => 'hidden')))
            ->getForm()
        ;
    }
}