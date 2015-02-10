<?php

namespace Szymon\TurniejBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Zawodnik
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Szymon\TurniejBundle\Entity\ZawodnikRepository")
 */
class Zawodnik
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
     * @ORM\Column(name="imie", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $imie;

    /**
     * @var string
     *
     * @ORM\Column(name="nazwisko", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nazwisko;

    /**
     * @var integer
     *
     * @ORM\Column(name="rok_urodzenia", type="integer", nullable=true)
     * @Assert\Range(
     *      min = 1900,
     *      max = 2015,
     *      minMessage = "Rok poniżej {{ limit }} nie jest poprawnym rokiem urodzenia",
     *      maxMessage = "Rok powyżej {{ limit }} nie jest poprawnym rokiem urodzenia"
     * )
     */
    private $rokUrodzenia;

    /**
     * @var integer
     *
     * @ORM\Column(name="kat_szachowa", type="integer", nullable=true)
     * @Assert\Range(
     *      min = 0,
     *      max = 10,
     *      minMessage = "Kategoria szachowa poniżej {{ limit }} nie jest poprawną kategorią szachową",
     *      maxMessage = "Kategoria szachowa powyżej {{ limit }} nie jest poprawną kategorią szachową"
     * )
     */
    private $katSzachowa;

    /**
    * @ORM\ManyToOne(targetEntity="Turniej", inversedBy="zawodnicy")
    * @ORM\JoinColumn(name="turniej_id", referencedColumnName="id", nullable=false)
    */
    private $turniej;
    
    /**
    * @ORM\ManyToOne(targetEntity="Grupa", inversedBy="zawodnicy")
    * @ORM\JoinColumn(name="grupa_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
    */
    private $grupa;
    
    /**
     * @ORM\ManyToOne(targetEntity="Grupa", inversedBy="zawodnicyFinalowi")
     * @ORM\JoinColumn(name="grupa_finalowa_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $grupaFinalowa;
    
    /**
    * @ORM\OneToMany(targetEntity="WynikRozgrywki", mappedBy="zawodnik1")
    */
    protected $wynikRozgrywki1;    
    
    /**
    * @ORM\OneToMany(targetEntity="WynikRozgrywki", mappedBy="zawodnik2")
    */
    protected $wynikRozgrywki2;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="czy_usuniety", type="smallint", nullable=true)
     */
    protected $czyUsuniety;

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
     * Set imie
     *
     * @param string $imie
     * @return Zawodnik
     */
    public function setImie($imie)
    {
        $this->imie = $imie;

        return $this;
    }

    /**
     * Get imie
     *
     * @return string 
     */
    public function getImie()
    {
        return $this->imie;
    }

    /**
     * Set nazwisko
     *
     * @param string $nazwisko
     * @return Zawodnik
     */
    public function setNazwisko($nazwisko)
    {
        $this->nazwisko = $nazwisko;

        return $this;
    }

    /**
     * Get nazwisko
     *
     * @return string 
     */
    public function getNazwisko()
    {
        return $this->nazwisko;
    }

    /**
     * Set rokUrodzenia
     *
     * @param integer $rokUrodzenia
     * @return Zawodnik
     */
    public function setRokUrodzenia($rokUrodzenia)
    {
        $this->rokUrodzenia = $rokUrodzenia;

        return $this;
    }

    /**
     * Get rokUrodzenia
     *
     * @return integer 
     */
    public function getRokUrodzenia()
    {
        return $this->rokUrodzenia;
    }

    /**
     * Set katSzachowa
     *
     * @param integer $katSzachowa
     * @return Zawodnik
     */
    public function setKatSzachowa($katSzachowa)
    {
        $this->katSzachowa = $katSzachowa;

        return $this;
    }

    /**
     * Get katSzachowa
     *
     * @return integer 
     */
    public function getKatSzachowa()
    {
        return $this->katSzachowa;
    }

    /**
     * Set turniej
     *
     * @param \Szymon\TurniejBundle\Entity\Turniej $turniej
     * @return Zawodnik
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
     * Set grupa
     *
     * @param \Szymon\TurniejBundle\Entity\Grupa $grupa
     * @return Zawodnik
     */
    public function setGrupa(\Szymon\TurniejBundle\Entity\Grupa $grupa = null)
    {
        $this->grupa = $grupa;

        return $this;
    }

    /**
     * Get grupa
     *
     * @return \Szymon\TurniejBundle\Entity\Grupa 
     */
    public function getGrupa()
    {
        return $this->grupa;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rozgrywki1 = new \Doctrine\Common\Collections\ArrayCollection();
        $this->rozgrywki2 = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set grupaFinalowa
     *
     * @param \Szymon\TurniejBundle\Entity\Grupa $grupaFinalowa
     * @return Zawodnik
     */
    public function setGrupaFinalowa(\Szymon\TurniejBundle\Entity\Grupa $grupaFinalowa = null)
    {
        $this->grupaFinalowa = $grupaFinalowa;

        return $this;
    }

    /**
     * Get grupaFinalowa
     *
     * @return \Szymon\TurniejBundle\Entity\Grupa 
     */
    public function getGrupaFinalowa()
    {
        return $this->grupaFinalowa;
    }

    /**
     * Add wynikRozgrywki1
     *
     * @param \Szymon\TurniejBundle\Entity\WynikRozgrywki $wynikRozgrywki1
     * @return Zawodnik
     */
    public function addWynikRozgrywki1(\Szymon\TurniejBundle\Entity\WynikRozgrywki $wynikRozgrywki1)
    {
        $this->wynikRozgrywki1[] = $wynikRozgrywki1;

        return $this;
    }

    /**
     * Remove wynikRozgrywki1
     *
     * @param \Szymon\TurniejBundle\Entity\WynikRozgrywki $wynikRozgrywki1
     */
    public function removeWynikRozgrywki1(\Szymon\TurniejBundle\Entity\WynikRozgrywki $wynikRozgrywki1)
    {
        $this->wynikRozgrywki1->removeElement($wynikRozgrywki1);
    }

    /**
     * Get wynikRozgrywki1
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWynikRozgrywki1()
    {
        return $this->wynikRozgrywki1;
    }

    /**
     * Add wynikRozgrywki2
     *
     * @param \Szymon\TurniejBundle\Entity\WynikRozgrywki $wynikRozgrywki2
     * @return Zawodnik
     */
    public function addWynikRozgrywki2(\Szymon\TurniejBundle\Entity\WynikRozgrywki $wynikRozgrywki2)
    {
        $this->wynikRozgrywki2[] = $wynikRozgrywki2;

        return $this;
    }

    /**
     * Remove wynikRozgrywki2
     *
     * @param \Szymon\TurniejBundle\Entity\WynikRozgrywki $wynikRozgrywki2
     */
    public function removeWynikRozgrywki2(\Szymon\TurniejBundle\Entity\WynikRozgrywki $wynikRozgrywki2)
    {
        $this->wynikRozgrywki2->removeElement($wynikRozgrywki2);
    }

    /**
     * Get wynikRozgrywki2
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWynikRozgrywki2()
    {
        return $this->wynikRozgrywki2;
    }

    /**
     * Set czyUsuniety
     *
     * @param integer $czyUsuniety
     * @return Zawodnik
     */
    public function setCzyUsuniety($czyUsuniety)
    {
        $this->czyUsuniety = $czyUsuniety;

        return $this;
    }

    /**
     * Get czyUsuniety
     *
     * @return integer 
     */
    public function getCzyUsuniety()
    {
        return $this->czyUsuniety;
    }
}
