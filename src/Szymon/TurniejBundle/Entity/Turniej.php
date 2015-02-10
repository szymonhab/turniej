<?php

namespace Szymon\TurniejBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Turniej
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Szymon\TurniejBundle\Entity\TurniejRepository")
 */
class Turniej
{
		/**
		 * Stałe porządkujące ustalanie rund w aplikacji
		 * 
		 * @var int
		 */
		const TURNIEJ_NIE_ROZPOCZETY     = 0;
		const TURNIEJ_ROZGRYWKI          = 1;
		const TURNIEJ_ROZGRYWKI_FINALOWE = 2;
		const TURNIEJ_ZAKONCZONY         = 3;
	
		public static $nazwyRund = array(
			self::TURNIEJ_NIE_ROZPOCZETY     => 'Turniej jeszcze nie rozpoczęty',
			self::TURNIEJ_ROZGRYWKI          => 'Runda pierwsza',
			self::TURNIEJ_ROZGRYWKI_FINALOWE => 'Runda finałowa',
			self::TURNIEJ_ZAKONCZONY         => 'Turniej zakończony'
		);
		
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_rozpoczecia", type="datetime")
     * @Assert\Date()
     * @Assert\NotBlank()
     */
    private $dataRozpoczecia;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="data_zakonczenia", type="datetime", nullable=true)
     */
    private $dataZakonczenia;

    /**
     * @var integer
     *
     * @ORM\Column(name="runda", type="integer")
     */
    private $runda;

    /**
     * @var string
     *
     * @ORM\Column(name="nazwa_turnieju", type="string", length=255)
     */
    private $nazwaTurnieju;

    /**
     * @var integer
     *
     * @ORM\Column(name="sposob_przyporzadkowania", type="integer", nullable=true)
     */
    private $sposobPrzyporzadkowania;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="ilosc_szachownic", type="integer", nullable=true)
     * @Assert\Range(
     *      min = 1,
     *      max = 20,
     *      minMessage = "Nie może być mniej niż jedna szachownica do przeprowadzenia turnieju",
     *      maxMessage = "Maksymalna ilość szachownic to: 20"
     * )
     */
    private $iloscSzachownic;

    /**
    * @ORM\OneToMany(targetEntity="Zawodnik", mappedBy="turniej", cascade={"remove"})
    */
    protected $zawodnicy;
    
    /**
     * @ORM\OneToMany(targetEntity="Grupa", mappedBy="turniej", cascade={"remove"})
     */
    protected $grupy;
    
    /**
     * @ORM\OneToMany(targetEntity="WynikRozgrywki", mappedBy="turniej", cascade={"remove"})
     */
    protected $wynikiRozgrywki;
    
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
     * Set dataRozpoczecia
     *
     * @param \DateTime $dataRozpoczecia
     * @return Turniej
     */
    public function setDataRozpoczecia($dataRozpoczecia)
    {
        $this->dataRozpoczecia = $dataRozpoczecia;

        return $this;
    }

    /**
     * Get dataRozpoczecia
     *
     * @return \DateTime 
     */
    public function getDataRozpoczecia()
    {
        return $this->dataRozpoczecia;
    }

    /**
     * Set dataZakonczenia
     *
     * @param \DateTime $dataZakonczenia
     * @return Turniej
     */
    public function setDataZakonczenia($dataZakonczenia)
    {
        $this->dataZakonczenia = $dataZakonczenia;

        return $this;
    }

    /**
     * Get dataZakonczenia
     *
     * @return \DateTime 
     */
    public function getDataZakonczenia()
    {
        return $this->dataZakonczenia;
    }

    /**
     * Set runda
     *
     * @param integer $runda
     * @return Turniej
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
     * Set nazwaTurnieju
     *
     * @param string $nazwaTurnieju
     * @return Turniej
     */
    public function setNazwaTurnieju($nazwaTurnieju)
    {
        $this->nazwaTurnieju = $nazwaTurnieju;

        return $this;
    }

    /**
     * Get nazwaTurnieju
     *
     * @return string 
     */
    public function getNazwaTurnieju()
    {
        return $this->nazwaTurnieju;
    }

    /**
     * Set sposobPrzyporzadkowania
     *
     * @param integer $sposobPrzyporzadkowania
     * @return Turniej
     */
    public function setSposobPrzyporzadkowania($sposobPrzyporzadkowania)
    {
        $this->sposobPrzyporzadkowania = $sposobPrzyporzadkowania;

        return $this;
    }

    /**
     * Get sposobPrzyporzadkowania
     *
     * @return integer 
     */
    public function getSposobPrzyporzadkowania()
    {
        return $this->sposobPrzyporzadkowania;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add products
     *
     * @param \Szymon\TurniejBundle\Entity\Zawodnik $products
     * @return Turniej
     */
    public function addProduct(\Szymon\TurniejBundle\Entity\Zawodnik $products)
    {
        $this->products[] = $products;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \Szymon\TurniejBundle\Entity\Zawodnik $products
     */
    public function removeProduct(\Szymon\TurniejBundle\Entity\Zawodnik $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Add zawodnicy
     *
     * @param \Szymon\TurniejBundle\Entity\Zawodnik $zawodnicy
     * @return Turniej
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
     * Get id and nazwaTurnieju
     *
     * @return string
     */
    public function getNazwaAndIdTurnieju() {
        return $this->id.". ".$this->nazwaTurnieju;
    }

    /**
     * Add grupy
     *
     * @param \Szymon\TurniejBundle\Entity\Grupa $grupy
     * @return Turniej
     */
    public function addGrupy(\Szymon\TurniejBundle\Entity\Grupa $grupy)
    {
        $this->grupy[] = $grupy;

        return $this;
    }

    /**
     * Remove grupy
     *
     * @param \Szymon\TurniejBundle\Entity\Grupa $grupy
     */
    public function removeGrupy(\Szymon\TurniejBundle\Entity\Grupa $grupy)
    {
        $this->grupy->removeElement($grupy);
    }

    /**
     * Get grupy
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGrupy()
    {
        return $this->grupy;
    }

    /**
     * Set iloscSzachownic
     *
     * @param integer $iloscSzachownic
     * @return Turniej
     */
    public function setIloscSzachownic($iloscSzachownic)
    {
        $this->iloscSzachownic = $iloscSzachownic;

        return $this;
    }

    /**
     * Get iloscSzachownic
     *
     * @return integer 
     */
    public function getIloscSzachownic()
    {
        return $this->iloscSzachownic;
    }

    /**
     * Add wynikiRozgrywki
     *
     * @param \Szymon\TurniejBundle\Entity\WynikRozgrywki $wynikiRozgrywki
     * @return Turniej
     */
    public function addWynikiRozgrywki(\Szymon\TurniejBundle\Entity\WynikRozgrywki $wynikiRozgrywki)
    {
        $this->wynikiRozgrywki[] = $wynikiRozgrywki;

        return $this;
    }

    /**
     * Remove wynikiRozgrywki
     *
     * @param \Szymon\TurniejBundle\Entity\WynikRozgrywki $wynikiRozgrywki
     */
    public function removeWynikiRozgrywki(\Szymon\TurniejBundle\Entity\WynikRozgrywki $wynikiRozgrywki)
    {
        $this->wynikiRozgrywki->removeElement($wynikiRozgrywki);
    }

    /**
     * Get wynikiRozgrywki
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWynikiRozgrywki()
    {
        return $this->wynikiRozgrywki;
    }
    
    /**
     * Get nazwaRundy
     * 
     * @return string
     */
    public function getNazwaRundy()
    {
    	return self::$nazwyRund[$this->runda];
    }
}
