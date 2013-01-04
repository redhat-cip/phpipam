<?php

/**
 *
 * All functions to communicate with database
 *
 * Extended mysqli class to simplify result handling
 * 
 */
 
 
class database extends mysqli 
{

  public function __construct($host = NULL, $username = NULL, $dbname = NULL, $port = NULL, $socket = NULL) {
    parent::__construct($host, $username, $dbname, $port, $socket);
    $this->set_charset("utf8");
  } 
  
  # save last SQL insert id
  public $lastSqlId;
	
	/**
	 * execute given query 
	 *
	 */
	function executeQuery( $query, $lastId = false ) 
	{
		/* execute query */
		$result     = parent::query( $query );
		$this->lastSqlId   = $this->insert_id;

		/* if it failes throw new exception */
		if ( mysqli_error( $this ) ) {
            throw new exception( mysqli_error( $this ), mysqli_errno( $this ) ); 
      		}
        else {
        	# return lastId if requested
        	if($lastId)	{ return $this->lastSqlId; }
        	else 		{ return true; }
        }
	}
		
	
	/**
	 * get only 1 row
	 *
	 */
    function getRow ( $query ) 
    {
        /* get result */
        if ($result = parent::query($query)) {     
            $resp = $result->fetch_row();   
        }
        else {
            throw new exception( mysqli_error( $this ), mysqli_errno( $this ) ); 
        }
        /* return result */
        return $resp;   
        
        /* free result */
		$result->close();  
    }
	
	
	/**
	 * get array of results
	 *
	 * returns multi-dimensional array
	 *     first dimension is number
	 *     from second on the values
	 * 
	 * if nothing is provided use assocciative results
	 *
	 */
	function getArray( $query , $assoc = true ) 
	{	
		/* execute query */
		$result = parent::query($query);
	
	    /* if it failes throw new exception */
		if(mysqli_error($this)) {
      		throw new exception(mysqli_error($this), mysqli_errno($this)); 
        }
        
		/** 
		 * fetch array of all access responses 
         * either assoc or num, based on input
         *
         */
		if ($assoc == true) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $fields[] = $row;	
            }
		} 
		else {
            while($row = $result->fetch_array(MYSQLI_NUM)) {
                $fields[] = $row;	
            }
        }
        
		/* return result array */
		if(isset($fields)) {
        	return($fields);
        }
        else {
        	$fields = array();
        	return $fields;
        }
		
		/* free result */
		$result->close();	
	}


	
	/**
	 * get array of multiple results
	 *
	 * returns multi-dimensional array
	 *     first dimension is number
	 *     from second on the values
	 * 
	 * if nothing is provided use assocciative results
	 *
	 */
	function getMultipleResults( $query ) 
	{
        /* execute query */
		$result = parent::multi_query($query);
		
		/**
		 * get results to array
		 * first save it, than get each row from result and store it to active[]
		 */
		do { 
            $results = parent::store_result();
			
			/* save each to array (only first field) */
			while ( $row = $results->fetch_row() ) {
				$rows[] = $row[0];
			}
			$results->free();
		}
		while( parent::next_result() );
		
		/* return result array of rows */
		return($rows);
		
		/* free result */
		$result->close();	
	}
	
	
	/**
	 * Execute multiple querries!
	 *
	 */
	function executeMultipleQuerries( $query, $lastId = false ) 
	{	
        /* execute querries */
		$result = parent::multi_query($query);
		$this->lastSqlId   = $this->insert_id;

		/* if it failes throw new exception */
		if ( mysqli_error( $this ) ) {
            throw new exception( mysqli_error( $this ), mysqli_errno( $this ) ); 
      	}
        else {
       		if($lastId)	{ return $this->lastSqlId; }
        	else 		{ return true; }
        }
		
		/* free result */
		$result->close();	
	}


	/**
	 * Select database
	 *
	 */
	function selectDatabase( $database ) 
	{	
        /* execute querries */
		$result = parent::select_db($database);

		/* if it failes throw new exception */
		if ( mysqli_error( $this ) ) {
            throw new exception( mysqli_error( $this ), mysqli_errno( $this ) ); 
      	}
        else {
            return true;
        }
		
		/* free result */
		$result->close();	
	}
}

?>