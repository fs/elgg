<?
	/**
	 * Class defintion for a rubric
	 * 
	 * @package RubricBuilder
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	class Rubric extends ElggObject {
		
		// Useful constants
		const NUM_ROWS 			= 4; // Default # of rows
		const NUM_COLS 			= 5; // Defatult # cols
		
		const CRITERIA_HEADER		= "rubricbuilder:criteria:default"; // Default Header for Criteria
		const LEVEL_1				= "rubricbuilder:level1"; // Default level 1 text
		const LEVEL_2				= "rubricbuilder:level2"; // Default level 2 text
		const LEVEL_3				= "rubricbuilder:level3"; // Default level 3 text
		const LEVEL_4				= "rubricbuilder:level4"; // Default level 4 text
			
		// Add to ElggObject attributes
		protected function initialise_attributes() {
			parent::initialise_attributes();
			$this->attributes['subtype'] = 'rubric';
			// Logged in access by default
			$this->attributes['access_id'] = 1;

		}
		
		// CTOR
		public function __construct($guid = null) {
			parent::__construct($guid);
				
		}
		
		// Accessor's
		public function getContents() {
			if ($this->contents) 
				return unserialize($this->contents);
			else 	
				return null;
		}
		
		public static function getDefaultHeaders() {
			return array(array(self::CRITERIA_HEADER, self::LEVEL_4, self::LEVEL_3, self::LEVEL_2, self::LEVEL_1));
		}
		
		public function getNumRows() {
			if ($this->num_rows)
				return $this->num_rows;
			else 
				return self::NUM_ROWS;
		}
		
		public function getNumCols() {
			if ($this->num_cols)
				return $this->num_cols;
			else 
				return self::NUM_COLS;
		}
		
		// Mutator's
		public function setContents($value) {
			$this->contents = serialize($value);
		}
		
		public function setNumRows($value) {
			$this->num_rows = $value;
		}
		
		public function setNumCols($value) {
			$this->num_rows = $value;
		}
	}

?>