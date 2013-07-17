<?php

/**
 *	phpIPAM Subnet class
 */

class Subnet
{	
	
	/**
	* get subnet details
	*/
	public function getSubnet() {
	
		/**
		* all subnets 
		*/
		if($this->all) {
			//get subnet by id
			$res = fetchAllSubnets ();
		}

		/** 
		* all subnets in section
		*/
		elseif($this->sectionId) {
			//id must be set and numberic
			if ( is_null($this->sectionId) || !is_numeric($this->sectionId) ) 	{ throw new Exception('Invalid section Id - '.$this->sectionId); }
			//get all subnets in section
			$res = fetchSubnets ($this->sectionId);
			//throw new exception if not existing
			if(sizeof($res)==0) {
				//check if section exists
				if(sizeof(getSectionDetailsById ($this->sectionId))==0) 		{ throw new Exception('Section not existing');	}
			}
		}
		
		/** 
		* subnet by id 
		*/
		elseif($this->id) {
			//id must be set and numberic
			if ( is_null($this->id) || !is_numeric($this->id) ) 				{ throw new Exception('Subnet id not existing - '.$this->id); }
			//get subnet by id
			$res = getSubnetDetailsById ($this->id);
			//throw new exception if not existing
			if(sizeof($res)==0) 												{ throw new Exception('Subnet not existing'); }
		}
		
		/**
		* subnet by name 
		*/
		elseif($this->name) {
			//id must be set and numberic
			if ( is_null($this->name) || strlen($this->name)==0 ) 				{ throw new Exception('Invalid subnet name - '.$this->name); }
			//get subnet by id
			$res = getSubnetDetailsByName ($this->name);
			//throw new exception if not existing
			if(sizeof($res)==0) 												{ throw new Exception('Subnet not existing'); }
		}
		
		/** 
		* method missing 
		*/
		else 																	{ throw new Exception('Selector missing'); }

		//create object from results
		foreach($res as $key=>$line) {
			$this->$key = $line;
		}
		//output format
		$format = $this->format;
		//remove input parameters from output
		unset($this->all);															//remove from result array
		unset($this->format);
		unset($this->name);	
		unset($this->id);
		unset($this->sectionId);	
		//convert object to array
		$result = $this->toArray($this, $format);	
		//return result
		return $result;
	}


	/**
	* create new subnet
	*/
	public function createSubnet() {
		# verications
		if(!isset($this->sectionId) || !is_numeric($this->sectionId)) 			{ throw new Exception('Invalid section Id'); }				//mandatory parameters
		if(!isset($this->masterSubnetId) || !is_numeric($this->masterSubnetId)) { throw new Exception('Invalid master Subnet Id'); }		//mandatory parameters
		if(!isset($this->subnet)) 												{ throw new Exception('Invalid subnet'); }					//mandatory parameters
		if(!isset($this->mask) || !is_numeric($this->mask)) 					{ throw new Exception('Invalid mask'); }					//mandatory parameters
		if(!is_numeric($this->vrfId))											{ throw new Exception('Invalid VRF Id'); }
		if(!is_numeric($this->vlanId))											{ throw new Exception('Invalid VRF Id'); }
		if($this->allowRequests != 0 || $this->allowRequests !=1)				{ throw new Exception('Invalid allow requests value'); }
		if($this->showName != 0 || $this->showName !=1)							{ throw new Exception('Invalid show Name value'); }
		if($this->pingSubnet != 0 || $this->pingSubnet !=1)						{ throw new Exception('Invalid ping subnet value'); }


		//output format
		$format = $this->format;
		
		//create array to write new section
		$newSubnet = $this->toArray($this, $format);
		//create new section
		$res = UpdateSection2 ($newSection, true);								//true means from API	
		//return result (true/false)
		if(!$res) 																{ throw new Exception('Invalid query'); } 
		else {
			//format response
			return "Subnet created";		
		}
	}
	

	/**
	* function to return multidimensional array
	*/
	public function toArray($obj, $format)
	{
		//if object create array
		if(is_object($obj)) $obj = (array) $obj;
		if(is_array($obj)) {
			$arr = array();
			foreach($obj as $key => $val) {
				// proper format
				if($key=="subnet" && $format=="ip") {
					$val = transform2long($val);
				}
				// output format
				$arr[$key] = $this->toArray($val, $format);
			}
		}
		else { 
			$arr = $obj;
		}
		//return an array of items
		return $arr;
	}
}
