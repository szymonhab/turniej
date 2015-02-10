<?php 

namespace Szymon\TurniejBundle\Lib;

class NazwyGrup
{
	
	private static $nazwyGrup = array(
		1  => 'Pierwsza',
		2  => 'Druga',
		3  => 'Trzecia',
		4  => 'Czwarta',
		5  => 'Piąta',
		6  => 'Szósta',
		7  => 'Siódma',
		8  => 'Ósma',
		9  => 'Dziewiąta',
		10 => 'Dziesiąta',
		11 => 'Jedenasta',
		12 => 'Dwunasta',
		13 => 'Trzynasta',
		14 => 'Czternasta',
		15 => 'Piętnasta'
	);
	
	public static function getNazwyGrup() {
		
		return self::$nazwyGrup;
	}
	
	/**
	 * Zwraca nazwę grupy dla danego numeru,
	 * jeżeli nie ma takiej nazwy w tablicy,
	 * zwraca Grupa + $i
	 * 
	 * @param integer $i
	 * @return String $nazwaGrupy
	 */
	public static function getNazwaGrupy($i) {
		if(array_key_exists($i, self::$nazwyGrup)) {
			$nazwaGrupy = self::$nazwyGrup[$i];
		} else {
			$nazwaGrupy = $i;
		}
		
		return $nazwaGrupy;
	}
}