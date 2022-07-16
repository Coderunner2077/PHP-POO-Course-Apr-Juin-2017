<?php
/**
 * @ClassInfo(name = "Personnage", description = "il fait que dalle")
 */
class Personnage {
	/**
	 * @AttrInfo({force = "0", movement = "1", "mon experience" = "pas terrible"})
	 */
	private $_force = 0;
	private $_movement = 1;
	private $_mon_experience = 'pas très terrible';
	/**
	 * @AttrInfo(enBas = true)
	 */ 
	private $_enBas = true;

	/** 
	 * @ParamInfo(name = 'force', description = 'la force est avec toi')
	 * @MethodInfo(description = 'se deplacer', return = "int", returnDescription = 'retourne un nombre')
	 */
	public function deplacer($force) {
		$this->_movement++;
		
		return $this->_movement;
	}
}