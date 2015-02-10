<?php

namespace Szymon\TurniejBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * WynikRozgrywki
 *
 * @ORM\Table(name="wynik_rozgrywki")
 * @ORM\Entity(repositoryClass="Szymon\TurniejBundle\Entity\WynikRozgrywkiRepository")
 */
class WynikRozgrywki
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="nr_szachownicy", type="integer", nullable=true)
     */
    private $nrSzachownicy;

    /**
     * @var integer
     *
     * @ORM\Column(name="wynik", type="integer", nullable=true)
     */
    private $wynik;

    /**
    * @ORM\ManyToOne(targetEntity="Zawodnik", inversedBy="wynikRozgrywki1")
    */
    private $zawodnik1;
    
    /**
    * @ORM\ManyToOne(targetEntity="Zawodnik", inversedBy="wynikRozgrywki2")
    */
    private $zawodnik2;
    
    /**
     * @ORM\ManyToOne(targetEntity="Turniej", inversedBy="wynikiRozgrywki")
     */
    private $turniej;
    
    /**
     * @ORM\Column(name="runda", type="smallint", nullable=false)
     */
    private $runda;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set zawodnik1
     *
     * @param string $zawodnik1
     * @return WynikRozgrywki
     */
    public function setZawodnik1($zawodnik1)
    {
        $this->zawodnik1 = $zawodnik1;

        return $this;
    }

    /**
     * Get zawodnik1
     *
     * @return string 
     */
    public function getZawodnik1()
    {
        return $this->zawodnik1;
    }

    /**
     * Set nrSzachownicy
     *
     * @param integer $nrSzachownicy
     * @return WynikRozgrywki
     */
    public function setNrSzachownicy($nrSzachownicy)
    {
        $this->nrSzachownicy = $nrSzachownicy;

        return $this;
    }

    /**
     * Get nrSzachownicy
     *
     * @return integer 
     */
    public function getNrSzachownicy()
    {
        return $this->nrSzachownicy;
    }

    /**
     * Set zawodnik2
     *
     * @param \Szymon\TurniejBundle\Entity\Zawodnik $zawodnik2
     * @return WynikRozgrywki
     */
    public function setZawodnik2(\Szymon\TurniejBundle\Entity\Zawodnik $zawodnik2 = null)
    {
        $this->zawodnik2 = $zawodnik2;

        return $this;
    }

    /**
     * Get zawodnik2
     *
     * @return \Szymon\TurniejBundle\Entity\Zawodnik 
     */
    public function getZawodnik2()
    {
        return $this->zawodnik2;
    }

    /**
     * Set wynik
     *
     * @param integer $wynik
     * @return WynikRozgrywki
     */
    public function setWynik($wynik)
    {
        $this->wynik = $wynik;

        return $this;
    }

    /**
     * Get wynik
     *
     * @return integer 
     */
    public function getWynik()
    {
        return $this->wynik;
    }

    /**
     * Set turniej
     *
     * @param \Szymon\TurniejBundle\Entity\Turniej $turniej
     * @return WynikRozgrywki
     */
    public function setTurniej(\Szymon\TurniejBundle\Entity\Turniej $turniej = null)
    {
        $this->turniej = $turniej;

        return $this;
    }

    /**
     * Get turniej
     *
     * @return \Szymon\TurniejBundle\Entity\Turniej 
     */
    public function getTurniej()
    {
        return $this->turniej;
    }

    /**
     * Set runda
     *
     * @param integer $runda
     * @return WynikRozgrywki
     */
    public function setRunda($runda)
    {
        $this->runda = $runda;

        return $this;
    }

    /**
     * Get runda
     *
     * @return integer 
     */
    public function getRunda()
    {
        return $this->runda;
    }
    
    /**
     * Get punkty1
     * 
     * @return integer
     */
    public function getPunkty1()
    {
    	switch($this->wynik) {
    		case 0:
    			return 1;
    			break;
    		case 1:
    			return 3;
    			break;
    		case 2:
    			return 0;
    			break;
    		default:
    			return 0;
    			break;
    	}
    }
    
    /**
     * Get punkty2
     * 
     * @return integer
     */
    public function getPunkty2()
    {
    	switch($this->wynik) {
    		case 0:
    			return 1;
    			break;
    		case 1:
    			return 0;
    			break;
    		case 2:
    			return 3;
    			break;
    		default:
    			return 0;
    			break;
    	}
    }
}
