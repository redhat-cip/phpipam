<?php

/**
 *	phpIPAM Section class
 */

class Section
{	
	/**
	* get section details
	*/
	public function readSection() {
	
		/**
		* all sections 
		*/
		if($this->all) {
			//get section by id
			$res = fetchSections ();
			unset($this->all);	//remove from result array
		}
		
		/** 
		* section by id 
		*/
		elseif($this->id) {
			//id must be set and numberic
			if ( is_null($this->id) || !is_numeric($this->id) ) 			{ throw new Exception('Section id not existing - '.$this->id); }
			//get section by id
			$res = getSectionDetailsById ($this->id);
			unset($this->id);	//remove from result array
			//throw new exception if not existing
			if(sizeof($res)==0) 											{ throw new Exception('Section not existing'); }
		}
		
		/**
		* section by name 
		*/
		elseif($this->name) {
			//id must be set and numberic
			if ( is_null($this->name) || strlen($this->name)==0 ) 			{ throw new Exception('Invalid section name - '.$this->name); }
			//get section by id
			$res = getSectionDetailsByName ($this->name);
			unset($this->name);		//remove from result array
			//throw new exception if not existing
			if(sizeof($res)==0) 											{ throw new Exception('Section not existing'); }
		}
		
		/** 
		* method missing 
		*/
		else 																{ throw new Exception('Selector missing'); }

		//create object from results
		foreach($res as $key=>$line) {
			$this->$key = $line;
		}		
		//convert object to array
		$result = $this->toArray($this);	
		//return result
		return $result;
	}


	/**
	* create new section
	*/
	public function createSection() {
		# verications
		//name must be at least 2 chars
		if(strlen($this->name)<2) 											{ throw new Exception('Invalid section name'); }
		//strict mode can be blank (default), 1,0
		if(isset($this->strictMode) && !(is_numeric($this->strictMode))) 	{ throw new Exception('Invalid strict mode parameter'); }
		//order
		if(isset($this->order) && !(is_numeric($this->order)))				{ throw new Exception('Order must be numberic value'); }
		//check if it already exist
		if(sizeof(getSectionDetailsByName($this->name))>0) 					{ throw new Exception('Section with this name already exists'); }
		
		//create array to write new section
		$newSection = $this->toArray($this);
		//create new section
		$res = UpdateSection ($newSection, true);								//true means from API	
		//return result (true/false)
		if(!$res) {
			throw new Exception('Invalid query');
		} else {
			//format response
			return "Section created";		
		}
	}


	/**
	* update section
	*/
	public function updateSection() {
		# verications
		//id must be set
		if(!isset($this->id)) 												{ throw new Exception('Section ID missing'); }
		//does it exist?
		if(sizeof($oldSection = getSectionDetailsById($this->id))==0) 		{ throw new Exception('Section does not exist'); }
		//name must be at least 2 chars
		if(isset($this->name) && (strlen($this->name)<2))					{ throw new Exception('Invalid section name'); }
		//strict mode can be blank (default), 1,0
		if(isset($this->strictMode) && !(is_numeric($this->strictMode))) 	{ throw new Exception('Invalid strict mode parameter'); }
		//order
		if(isset($this->order) && !(is_numeric($this->order)))				{ throw new Exception('Order must be numberic value'); }
		
		//fill old values if new ones are not provided
		if(!isset($this->name)) 			{ $this->name 			= $oldSection['name']; }
		if(!isset($this->description)) 		{ $this->description 	= $oldSection['description']; }
		if(!isset($this->strictMode)) 		{ $this->strictMode 	= $oldSection['strictMode']; }
		if(!isset($this->order)) 			{ $this->order 			= $oldSection['order']; }
		if(!isset($this->subnetOrdering)) 	{ $this->subnetOrdering = $oldSection['subnetOrdering']; }
		if(!isset($this->permissions)) 		{ $this->permissions 	= $oldSection['permissions']; }
		
		//create array to write new section
		$newSection = $this->toArray($this);
		//create new section
		$res = UpdateSection ($newSection, true);								//true means from API	
		//return result (true/false)
		if(!$res) {
			throw new Exception('Invalid query');
		} else {
			//format response
			return "Section updated";		
		}
	}



	/**
	* delete section
	*/
	public function deleteSection() {
		//verications
		if(!isset($this->id)) 												{ throw new Exception('Section ID missing'); }
		//does it exist?
		if(sizeof(getSectionDetailsById($this->id))==0) 					{ throw new Exception('Section does not exist'); }
		
		//create array to write new section
		$newSection = $this->toArray($this);
		//create new section
		$res = UpdateSection ($newSection, true);								//true means from API	
		//return result (true/false)
		if(!$res) {
			throw new Exception('Invalid query');
		} else {
			//format response
			return "Section deleted";		
		}
	}
	
	

	/**
	* function to return multidimensional array
	*/
	public function toArray($obj)
	{
		//if object create array
		if(is_object($obj)) $obj = (array) $obj;
		if(is_array($obj)) {
			$arr = array();
			foreach($obj as $key => $val) {
				$arr[$key] = $this->toArray($val);
			}
		}
		else { 
			$arr = $obj;
		}
		//return an array of items
		return $arr;
	}
}
