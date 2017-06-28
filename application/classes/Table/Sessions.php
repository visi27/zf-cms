<?php

class Table_Sessions extends Table_Abstract {
	
	protected $_name 		= 'sys_sessions';
	
	protected $_primary 	= 'session_id';
	
	
	public function getSessionById($sessionId){
		return $this->find($sessionId)->current();
	}
	
	public function createSessionDb($sessionId, $userId){
		
		try {
			// get current timestamp
			$currentTime = time();
			
			//expire any existing database sessions for this user
			$this->expireSessionByUser($userId, $currentTime);
			
			// create a new row in the bugs table
			$row = $this->createRow ();
			
			// set the values		
			$row->session_id = $sessionId;
			$row->user_id = $userId;
			$row->login_time = $currentTime;
			$row->last_action = $currentTime; 
			$row->expiry_time = $currentTime + Zend_Registry::get('config')->cookie_life;
			$row->remote_ip = $_SERVER ['REMOTE_ADDR'];
				
			// save the new row
			$row->save ();
			
			// now fetch the id of the row you just created and return it	
			return true;
		}
		catch(Exception $e){
			return false;
		}
		
	}
	
	/**
	 * 
	 * @param String $sessionId
	 * @return boolean
	 * Expire a session in the database identified by the $sessionId.
	 */
	public function expireSessionById($sessionId, $now = null){
		// get current timestamp
		$currentTime = ($now!=null)?$now:time();
		
		// find the session by primary key
		$row = $this->getSessionById ( $sessionId );
		
		if ($row) {
			
			//set the values
			$row->last_action = $currentTime;
			$row->expiry_time = $currentTime;
			
			// save the updated row
			$row->save ();
			
			//return true
			return true;
			
		}else {// if not found
			
			//return false
			return false;
		}
	}
	
	/**
	 * 
	 * @param String $userId
	 * @param in $now
	 * @return boolean
	 * Expire an alive user session in the database where "expiry_time"
	 * is greater than $now or the current timestamp.
	 */
	public function expireSessionByUser($userId, $now = null){
		// get current timestamp
		$currentTime = ($now!=null)?$now:time();
		
		// Update session expiry time in database, by extending it.
		$where[] = $this->getAdapter()->quoteInto('user_id = ?', $userId);
		$where[] = $this->getAdapter()->quoteInto('expiry_time > ?', $currentTime);
		
		// commit the query
		return $this->update($data = array('expiry_time' => $currentTime), $where);
		
	}
	
	/**
	 * 
	 * @param String $sessionId
	 * @return boolean
	 * Update a sessions expiry_time or return false if allready expired.
	 */
	public function keepSessionAlive($sessionId){
		
		// get current timestamp
		$currentTime = time();
		
		// Get the current session's data from the database
		$row = $this->getSessionById ( $sessionId );
		
		// try block to eliminate sql problems
		try{
			//check if the session is alive
			if ($row->expiry_time > $currentTime){
				
				// This is an action being performed.
				$where = $this->getAdapter()->quoteInto('session_id = ?', $row->session_id);
				
				// Lets update this action's timestamp.
				$this -> update($data = array('last_action' => $currentTime), $where);
				
				// Update session expiry time in database, by extending it.
				$this -> update($data = array('expiry_time' => $currentTime + Zend_Registry::get('config')->cookie_life), $where);
				
				return true;
			}
			
			// The session is dead in the database. Lifetime has expired.
				
				// The lifetime can expire because 
				// a) the user could have loged in using the same 
				//	  credentials in another browser.
				// b) more time than COOKIE_LIFE has gone without 
				//	  performing an action.
			return false;
		}
		catch(Exception $e){
			// an error ocurred
			return false;
		}
				
	}
}

?>