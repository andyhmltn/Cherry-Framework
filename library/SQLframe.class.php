<?php

/**
 * SQLFrame Class
 * I've completely re-written the class to
 * act more like active-record (syntax wise.)
 * 
 * @package SQLFrame
 * 
 **/

class SQLFrame {
	
	/**
	 * Declare the private
	 * variables.
	 *
	 * @package N/A
	 * 
	 **/
	
	protected $_connection;
	protected $_results;
	protected $_newTemp;
	
	/**
	 * Just the connect function
	 * using constants defined in the config/config.php file
	 *
	 * @package Connect()
	 * 
	 **/
	
	function connect($host, $username, $password, $database) {
		$this->_connection = @mysql_connect($host, $username, $password);
		if($this->_connection) {
			return(mysql_select_db($database, $this->_connection));
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Used for disconnecting the DB
	 *
	 * @package disconnect()
	 * 
	 **/
	
	function disconnect() {
		return mysql_close($this->_connection);
	}
	
	/**
	 * A shorter way of calling
	 * mysql_real_escape_string
	 *
	 * @package Escape()
	 * 
	 **/
	
	
	function escape($s) {
		return mysql_escape_string($s);
	}
	
	/**
	 * The main query class.
	 * Queries the databases, adds results into an array
	 * and then converts that array into an object.
	 *
	 * @package Query()
	 * 
	 **/
	
	
	function query($query, $singleResult = FALSE) {
		
		global $log;

		if(!$this->_results = mysql_query($query, $this->_connection)) {
			$log->logError($this->error(), 'MYSQL');
			return FALSE;
		}

		if(preg_match("/select/i", $query)) {
			$result = array();
			$table = array();
			$field = array();
			$tempResults = array();
			$numberOfFields = mysql_num_fields($this->_results);
			for($i = 0; $i < $numberOfFields; $i++) {
				array_push($table, mysql_field_table($this->_results, $i));
				array_push($field, mysql_field_name($this->_results, $i));
			}
			while($row = mysql_fetch_row($this->_results)) {
				for($i = 0; $i < $numberOfFields; $i++) {
					$tempResults[$field[$i]] = $row[$i];
				}
				if($singleResult || mysql_num_rows($this->_results) == 1) {
					mysql_free_result($this->_results);
					return arrayToObject($tempResults);
				}
				array_push($result, $tempResults);
			}
			mysql_free_result($this->_results);
			
			return(arrayToObject($result));
		} 
	}
	

	/**
	 * Used for managing 'Order By' and 'Limit'
	 * from most of these functions. Called when an
	 * array is presented as an argument.
	 *
	 * @package SortArgsAndAppend()
	 * 
	 **/
	
	function sortArgsAndAppend($args, $query) {
		if(!is_array($args)) {
			return $query;
		}
		foreach($args as $k => $v) {
			switch ($k) {
				case 'order':
					$query .= " ORDER BY $v";
					break;
				case 'limit':
					$query .= " LIMIT $v";
					break;
			}
		}
		return $query;
	}
	
	/**
	 * Returns the first result
	 * from the table.
	 * $Post->first()
	 *
	 * @package First()
	 * 
	 **/
	
	
	function first($args = array()) {
		$query = $this->sortArgsAndAppend($args, "SELECT * FROM $this->_table");
		$query .= " LIMIT 1";
		return $this->query($query);
	}
	
	/**
	 * Same as above but selects
	 * the last value (sorted by id.)
	 * $Post->last();
	 *
	 * @package Last()
	 * 
	 **/
	
	
	function last() {
		$query = "SELECT * FROM $this->_table";
		$query .= " ORDER BY id DESC LIMIT 1";
		return $this->query($query);
	}
	
	/**
	 * Selects everything in the
	 * table and returns it.
	 * $Post->all();
	 *
	 * @package All
	 * 
	 **/
	
	function all($args = array()) {
		$query = $this->sortArgsAndAppend($args, "SELECT * FROM $this->_table");

		$results = $this->query($query);

		$_results = ((array) $results); 

		/**
		 * This is so no matter if it's a single result
		 * or multiple ones, you can use foreach to echo
		 * them out. 
		 */

		if(empty($_results)) {
			return FALSE;
		}
		if(!isset($_results[0])) {			#If it's a single result returned.
			$_results = array();			#Create a new array.
			$_results[0] = $results;		#Set the first value to the object returned.
			return ((object) $_results);	#Return the new object.
		} else {
			return $results;				#If there are multiple results, just return them.
		}

	}
	
	/**
	 * Checks if a value exists with
	 * the id provided. Returns a boolean
	 * $Post->exists(1); //TRUE
	 *
	 * @package Exists()
	 * 
	 **/
	
	function exists() {
		$arguments = func_get_args();
		$result = call_user_func_array(array($this, "find"), $arguments[0]);
		return count((array) $result) > 0;
	}
	

	/**
	 * Finds a random value
	 *
	 * @package findRandom()
	 * 
	 **/

	function findRandom() {
		return $this->first(array('order' => 'RAND()'));
	}
	/**
	 * Used for finding values.
	 * $Post->find(1) //Returns the post with the id 1
	 * $Post->find('1') //Same as above. ''s don't matter.
	 * $Post->find('id > 5') //Finds all rows with an id over 5
	 * $Post->find("title = 'Hello world!'" array('order' => 'date ASC')) //Returns the post with the title 'Hello world!' Orders by date.
	 *
	 * @package Find()
	 * 
	 **/
	
	
	function find() {
		$_args = func_get_args();
		if(func_num_args() == 1 && preg_match('/^[0-9]*$/', $_args[0])) {
			$query = "SELECT * FROM $this->_table WHERE id = '$_args[0]'";
			$result = $this->query($query, TRUE);
			if(count((array) $result) > 0) {
				return $this->query($query, TRUE);
			} else {
				return FALSE;	
			}
		}
		
		$conditions = array();
		foreach($_args as $a) {
			array_push($conditions, $a);
		}
		
		$query = 'SELECT * FROM ' . $this->_table . ' WHERE ';
		$i = 0;
		$temp = '';
		$single = FALSE;
		
		foreach($conditions as $c) {
			if(is_array($c)) {
				$args = $c;
				$argsExist = TRUE;
				break;
			}
			if(substr($c, 0, 2) !== '||') {
				if($i == 0) { $temp .= $c . ' '; } else { $temp .= 'AND ' . $c . ' '; }
			} else {
				$c = substr( $c, 2 );
				if($i == 0) { $temp .= $c . ' '; } else { $temp .= 'OR ' . $c . ' '; }
			}
			$i++;
		}

		
		$query .= $temp;
		if(!isset($argsExist)) {
			$args = array();
		}
		$query = $this->sortArgsAndAppend($args, $query);
		$result = $this->query($query, $single);
		if(count((array) $result) > 0) {
			return $this->query($query, $single);
		} else {
			return FALSE;	
		}
	}
	
	/**
	 * Updates the table via the suppliedq
	 * ID. Usage:
	 * $Post->update(array('title' => 'Hello world), 1) //Updates The post with ID 1.
	 *
	 * @package update()
	 * 
	 **/
	
	
	function update($args, $id) {
		$query = 'UPDATE ' . $this->_table . ' SET ';
		$i = 0;
		foreach($args as $k => $v) {
			if(count($args) - 1 == $i) {
				$last = "' ";
			} else {
				$last = "', ";
			}
			$query .= $k . "=" . "'" . $v . $last;
			$i++;
		}
		
		$query .= "WHERE id=$id";
		
		return $this->query($query);
	}
	
	/**
	 * This stores the arguments in a temporary
	 * array (for if you want to do something with them.)
	 *
	 * @package newRecord()
	 * 
	 **/
	
	function newRecord($_args) {
		if(!is_array($_args)) { return FALSE; } else {
			$this->_newTemp = $_args;
		}
	}
	
	/**
	 * Takes $this->_newTemp, preforms the _before
	 * and _after actions and then creates a new
	 * result in the table.
	 *
	 * @package saveRecord()
	 * 
	 **/
	
	
	function saveRecord() {
			$fields = '';
			$values = '';
			
			$i = 0;
			if ((int)method_exists($this->_model, '_before')) {
				$_toSave = call_user_func(array($this->_model, '_before'), $this->_newTemp);
				if($_toSave == NULL) {
					$_toSave = $this->_newTemp;
				}
			} else {
				$_toSave = $this->_newTemp;
			}
			foreach($_toSave as $k => $v) {
				if($i == count($_toSave) - 1) {
					$fields .= $this->escape($k);
					$values .= '"' . $this->escape($v) . '"';
				} else {
					$fields .= $this->escape($k) . ', ';
					$values .= '"' . $this->escape($v) . '", ';
				}
				$i++;
			}
			return $this->query('INSERT INTO ' . $this->_table . ' (' . $fields . ') VALUES (' . $values . ')');
			
			if ((int)method_exists($this->_model, '_after')) {
				call_user_func(array($this->_model, '_after'), $this->_newTemp);
			}
	}
	
	/**
	 * Uses newRecord and saveRecord
	 * to insert a new value into the table. Usage:
	 * $Post->create(array('title' => 'hey'), 'title') //Makes a post if the 'title' is unique.
	 *
	 * @package create()
	 * 
	 **/
	
	
	function create($args, $unique = NULL) {
		$this->newRecord($args);
		if($unique !== NULL) {
			$unique = explode(',', $unique);
			$uniqueArray = array();

			$i = 0;
			foreach($unique as $u) {
				$u = str_replace(" ", "", $u);
				if(!isset($args[$u], $args)) { return FALSE; }
				if($i > 0) {
					array_push($uniqueArray, "||$u = '" . $args[$u] . "'");
				} else {
					array_push($uniqueArray, "$u = '" . $args[$u] . "'");
				}
				$i++;
			}
				
			if($this->exists($uniqueArray)) {
				return FALSE;
			} else {
				$this->saveRecord();
				return TRUE;
			}
		} else {
			$this->saveRecord();
			return TRUE;
		}
		
	}

	/**
	 * Removes a result from the table using $id
	 *
	 * @package remove()
	 *
	 **/
	
	function remove($id) {
		$query = "DELETE FROM $this->_table WHERE id =  '".$this->escape($id)."'";
		return $this->query($query);
	}
	
	/**
	 * These are the functions related
	 * to the relationship functipn
	 *
	 * @package Relationship Functions
	 * 
	 **/
	
		
		/**
		 * This is for both the hasOne and hasMany functions
		 * to save repeating the code twice; 
		 */

		function hasX($relationTablename, $id) {
			$name = pluralise($relationTablename);
			$nameP = $name['p'];
			$nameNP = $name['np'];
		
			$id = intval($id);
		
			$thisTableName = substr($this->_table, 0, -1);
		
			return "SELECT * FROM $nameP WHERE " . $thisTableName . "_id = $id";
		}

		/**
		 * Returns the 'hasOne' relation.
		 */

		function hasOne($relationTablename, $id, $additionals) {
			$query = $this->hasX($relationTablename, $id);
			return $this->query($this->sortArgsAndAppend($additionals, $query), TRUE);
		}

		/**
		 * Returns an array of the 'hasMany' relation.
		 */
	
		function hasMany($relationTablename, $id, $additionals) {			
			$query = $this->sortArgsAndAppend($additionals, $this->hasX($relationTablename, $id));
			return $this->query($query);
		}
	
		/**
		 * Returns the result that the current selection
		 * belongs to. 
		 */

		function belongsTo($relationTableName, $id, $additionals) {
			$name = pluralise($relationTableName);
			$nameP = $name['p'];
			$nameNP = $name['np'];
			
			$_id = $this->query("SELECT $nameNP" . "_id FROM $this->_table WHERE id = $id", TRUE);
			$_id = $_id->{$nameNP . "_id"};
		
			return $this->query($this->sortArgsAndAppend($additionals, "SELECT * FROM $relationTableName WHERE id = $_id", TRUE));
		}

		/**
		 * Returns an object with all the 
		 * related objects in
		 */
	
		function hasAndBelongsToMany($relationTablename, $query = array(), $additionals) {
			$name = pluralise($relationTablename);
			$nameP = $name['p'];
			$nameNP = $name['np'];
			
			$whereString = "";
			if(!is_array($query)) {
				$whereString .= "$this->_table.id = " . intval($query);
			} else {
				$i = 0;
				foreach($query as $k => $v) {
					if($i == 0) {
						$whereString .= "$k = '$v' ";
					} else {
						$whereString .= "AND $kv = '$v'";
					}
					$i++;
				}
			}
			
			$sorted = array($name['p']);
			array_push($sorted, $this->_table);
			sort($sorted);
			
			$table_name = $sorted[0] . '_' . $sorted[1];
			
			$query = "SELECT $nameP.* FROM $nameP INNER JOIN $sorted[0]"."_".$sorted[1]."
				ON $nameP.id = $table_name.$nameNP" . "_id INNER JOIN $this->_table ON $table_name." . substr($this->_table, 0, -1) .
				"_id = $this->_table.id WHERE $whereString";
		
			return $this->query($this->sortArgsAndAppend($additionals, $query));					
		}

		/**
		 * The next 4 functions are to do with the HABTM
		 * relationship. Because they have a seperate table
		 * I added some functions to make certain actions
		 * easier (creating a relationship, deleting one and
		 * seeing if one exists.)
		 **/

		 /**
		  * This sorts the two table names alphabetically
		  * and then returns the correct table name.
		  **/

		function HABTtableName($relationTableName) {
			$toSort = array($this->_table, $relationTableName);
			sort($toSort);
			
			return "$toSort[0]_$toSort[1]";	
		}

		/**
		 * This removes a relationship between two entities. 
		 */

		function HABTMremove($relationTableName, $thisID, $relationID) {
			$relationTableName = pluralise($relationTableName);
			$tableName = $this->HABTtableName($relationTableName['p']);

			$query = "DELETE FROM $tableName WHERE " . $this->_model . "_id = '$thisID'";
			$query .= " AND " . $relationTableName['np'] . "_id = '$relationID'"; 

			return $this->query($query);
		}

		/**
		 * This adds a relationship between two entities.
		 */
		
		function HABTMadd($relationTableName, $thisID, $relationID) {
			$relationTableName = pluralise($relationTableName);
			$tableName = $this->HABTtableName($relationTableName['p']);

			$query = "INSERT INTO $tableName (`" . $this->_model . "_id`, `" . $relationTableName['np'] . "_id`)";
			$query .= " VALUES ('$thisID', '$relationID')";

			return $this->query($query);
		}

		/**
		 * This returns 'true' or 'false' depending on whether a relationship
		 * already exists between two items.
		 */

		function HABTexists($relationTableName, $thisID, $relationID) {
			$relationTableName = pluralise($relationTableName);
			$tableName = $this->HABTtableName($relationTableName['p']);

			$query = "SELECT COUNT(id) AS count FROM $tableName WHERE " . $this->_model . "_id = '$thisID'";
			$query .= " AND " . $relationTableName['np'] . "_id = '$relationID'"; 

			$result = $this->query($query);

			return $result->count > 0;
		}
	/**
	 * Used to return all the results for
	 * the supplied relationship
	 *
	 * @package relationship()
	 * 
	 **/
	
	function relationship($relation, $id, $additionals = array()) {
		$relationships = array(
			'_hasAndBelongsToMany',
			'_hasMany',
			'_belongsTo',
			'_hasOne'
		);
		
		$i = 0;
		foreach ($relationships as $_r) {
			if(isset($this->{$_r})) {						#$this->{$_r} would turn out like so: $this->_hasOne
				if(in_array($relation, $this->{$_r})) {		#If the relation is in that tablename
					$relationship = $_r;
				}
			}
			$i++;
		}
		if(isset($relationship)) {
			$relationship = str_replace("_", "",  $relationship);			#Removes the first underscore
			$results = $this->$relationship($relation, $id, $additionals);	#If the $relationship = '_hasMany' it will run $this->hasMany

			$_results = ((array) $results); 

			/**
			 * This is so no matter if it's a single result
			 * or multiple ones, you can use foreach to echo
			 * them out. 
			 */

			if(empty($_results)) {
				return FALSE;
			}

			if(!isset($_results[0])) {			#If it's a single result returned.
				$_results = array();			#Create a new array.
				$_results[0] = $results;		#Set the first value to the object returned.
				return ((object) $_results);	#Return the new object.
			} else {
				return $results;				#If there are multiple results, just return them.
			}
		} else {
			return FALSE;
		}
	}


	function error() {
		return mysql_error($this->_connection);
	}
	
	/**
	 * This is for the find_by* and find_all_by*
	 * functions. Using the __call magic method
	 * to dynamically interpret the called method.
	 *
	 * @package find_by*() and find_all_by*()
	 * 
	 **/
	
	
	function __call($method,$arguments) {

		global $log;

	    if(preg_match('/find_(.*?)by_([^.\/]+)*/', $method, $result)) {
		
			$end = $result[2];
			$limit = ($result[1] !== 'all_');
			$queryString = "SELECT * FROM $this->_table WHERE ";
			
			$i2 = 0;
			foreach($arguments as $a) {
				if(is_array($a)) {
					$optionals = $a;
				}
				$i2++;
			}
			if(preg_match('/[^.\/]*_or_([^.\/]+)*/', $end, $_results)) {
				$_results = explode('_or_', $end);
				$i = 0;
				foreach($_results as $x) {
					if(isset($arguments[$i])) {
						if($i > 0) {
							$queryString .= " OR";
						}
						$queryString .= " $x = '$arguments[$i]'";
						$i++;
					}
				}
				$this->query($queryString);
			} elseif(preg_match('/[^.\/]*_and_([^.\/]+)*/', $end)) {
				$_results = explode('_and_', $end);
				$i = 0;
				foreach($_results as $x) {
					if(isset($arguments[$i])) {
						if($i > 0) {
							$queryString .= " AND";
						}
						$queryString .= " $x = '$arguments[$i]'";
						$i++;
					}
				}
				$this->query($queryString);
			} else {
				$queryString .= "$end = '$arguments[0]'";
				if(isset($arguments[1])) {
					if(is_array($arguments[1])) {
						$optionals = $arguments[1];
					}
				}
				if($limit) {
					$queryString .= " LIMIT 1";
				}
			}
			if(isset($optionals)) {
				return $this->query($this->sortArgsAndAppend($optionals, $queryString));
			} else {
				return $this->query($queryString);
			}			
		} else {

			/**
			 * If the called function isn't find_by* or find_all_by*
			 * then it doesn't exist so display a warning to the user and
			 * log that warning
			 */

			$warning = 'Undefined function - ' . $this->_model . '::' . $method . '()';
			$log->logError($warning, 'METHOD');
			echo "WARNING: $warning";
		}
	}
}