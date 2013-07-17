<?php

/**
 *	phpIPAM API class to work with sections
 *
 * Reading sections:
 *	get by id: 		?controller=sections&action=read&id=1
 *	get by name: 	?controller=sections&action=read&name=Section name
 *	get all:		?controller=sections&action=read&all=true
 */

class Sections
{
	/* variables */
	private $_params;
	
	/* set parameters, provided via post */
	public function __construct($params)
	{
	  $this->_params = $params;
	}

	/** 
	* create new section 
	*/
	public function createSections()
	{
		//init section class
		$section = new Section();
		//required parameters
		$section->action      		= $this->_params['action'];
		$section->name        		= $this->_params['name'];
		$section->description 		= $this->_params['description'];
		$section->strictMode  		= $this->_params['strictMode'];
		$section->order  			= $this->_params['order'];
		$section->subnetOrdering  	= $this->_params['subnetOrdering'];
		$section->permissions	  	= $this->_params['permissions'];
		
		//create section
		$res = $section->createSection(); 	
		//return result
		return $res;
	}


	/** 
	* read sections 
	*/
	public function readSections()
	{
		//init section class
		$section = new Section();
		
		//get all sections?
		if($this->_params['all'])		{ $section->all	 = true; }
		//get section by name
		elseif($this->_params['name']) 	{ $section->name = $this->_params['name']; }
		//get section by ID
		else 							{ $section->id 	 = $this->_params['id'];	}
		
		//fetch results
		$res = $section->readSection(); 
		//return section(s) in array format
		return $res;
	}	
	
	
	/** 
	* update existing section 
	*/
	public function updateSections()
	{
		//init section class
		$section = new Section();
		//required parameters
		$section->id      			= $this->_params['id'];
		$section->action      		= $this->_params['action'];
		$section->name        		= $this->_params['name'];
		$section->description 		= $this->_params['description'];
		$section->strictMode  		= $this->_params['strictMode'];
		$section->order  			= $this->_params['order'];
		$section->subnetOrdering  	= $this->_params['subnetOrdering'];
		$section->permissions	  	= $this->_params['permissions'];
		
		//create section
		$res = $section->updateSection(); 	
		//return result
		return $res;

	}	
	
	
	/** 
	* delete section 
	*/
	public function deleteSections()
	{
		//init section class
		$section = new Section();
		//required parameters
		$section->action      	= $this->_params['action'];
		$section->id        	= $this->_params['id'];

		//delete section
		$res = $section->deleteSection(); 	
		//return result
		return $res;		
	}
}

?>