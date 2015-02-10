<?php

namespace Szymon\TurniejBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Grupa
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Szymon\TurniejBundle\Entity\GrupaRepository")
 */
class Grupa
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
     * @var string
     *
     * @ORM\Column(name="nazwa_grupy", type="string", length=255, nullable=true)
     */
    private $nazwaGrupy;

    /**
     * @var integer
     *
     * @ORM\Column(name="runda", type="integer")
     */
    private $runda;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="numer_grupy", type="integer")
     */
    private $numerGrupy;

    /**
    * @ORM\OneToMany(targetEntity="Zawodnik", mappedBy="grupa")
    */
    protected $zawodnicy;
    
    /**
     * @ORM\OneToMany(targetEntity="Zawodnik", mappedBy="grupaFinalowa")
     */
    protected $zawodnicyFinalowi;
    
    /**
     * @ORM\ManyToOne(targetEntity="Turniej", inversedBy="grupy")
     * @ORM\JoinColumn(name="turniej_id", referencedColumnName="id", nullable=false)
     */
    private $turniej;
    
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
     * Set nazwaGrupy
     *
     * @param string $nazwaGrupy
     * @return Grupa
     */
    public function setNazwaGrupy($nazwaGrupy)
    {
        $this->nazwaGrupy = $nazwaGrupy;

        return $this;
    }

    /**
     * Get nazwaGrupy
     *
     * @return string 
     */
    public function getNazwaGrupy()
    {
        return $this->nazwaGrupy;
    }

    /**
     * Set runda
     *
     * @param integer $runda
     * @return Grupa
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
     * Constructor
     */
    public function __construct()
    {
        $this->zawodnicy = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add zawodnicy
     *
     * @param \Szymon\TurniejBundle\Entity\Zawodnik $zawodnicy
     * @return Grupa
     */
    public function addZawodnicy(\Szymon\TurniejBundle\Entity\Zawodnik $zawodnicy)
    {
        $this->zawodnicy[] = $zawodnicy;

        return $this;
    }

    /**
     * Remove zawodnicy
     *
     * @param \Szymon\TurniejBundle\Entity\Zawodnik $zawodnicy
     */
    public function removeZawodnicy(\Szymon\TurniejBundle\Entity\Zawodnik $zawodnicy)
    {
        $this->zawodnicy->removeElement($zawodnicy);
    }

    /**
     * Get zawodnicy
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getZawodnicy()
    {
        return $this->zawodnicy;
    }

    /**
     * Set numerGrupy
     *
     * @param integer $numerGrupy
     * @return Grupa
     */
    public function setNumerGrupy($numerGrupy)
    {
        $this->numerGrupy = $numerGrupy;

        return $this;
    }

    /**
     * Get numerGrupy
     *
     * @return integer 
     */
    public function getNumerGrupy()
    {
        return $this->numerGrupy;
    }

    /**
     * Set turniej
     *
     * @param \Szymon\TurniejBundle\Entity\Turniej $turniej
     * @return Grupa
     */
    public function setTurniej(\Szymon\TurniejBundle\Entity\Turniej $turniej)
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
     * Add zawodnicyFinalowi
     *
     * @param \Szymon\TurniejBundle\Entity\Zawodnik $zawodnicyFinalowi
     * @return Grupa
     */
    public function addZawodnicyFinalowi(\Szymon\TurniejBundle\Entity\Zawodnik $zawodnicyFinalowi)
    {
        $this->zawodnicyFinalowi[] = $zawodnicyFinalowi;

        return $this;
    }

    /**
     * Remove zawodnicyFinalowi
     *
     * @param \Szymon\TurniejBundle\Entity\Zawodnik $zawodnicyFinalowi
     */
    public function removeZawodnicyFinalowi(\Szymon\TurniejBundle\Entity\Zawodnik $zawodnicyFinalowi)
    {
        $this->zawodnicyFinalowi->removeElement($zawodnicyFinalowi);
    }

    /**
     * Get zawodnicyFinalowi
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getZawodnicyFinalowi()
    {
        return $this->zawodnicyFinalowi;
    }
}
