<?php

class User extends Model {
	
	public function _before($args) {
		$args['salt'] = Member::salt();
		if(isset($args['password'])) {
			$args['password'] = Member::hash($args['password'], $args['salt']);
		}
		return $args;
	}
	
	function isLoggedIn() {
		return isset($_SESSION['QO0']);
	}
	
	function setLoggedIn($username, $id) {
		$_SESSION['QO0'] = TRUE;
		$_SESSION['username'] = $username; 
		$_SESSION['id'] = $id;
	}
	
	function logout() {
		unset($_SESSION['QO0'], $_SESSION['username']);
	}
	
	function hash($password,$salt) {
		$lastTwo = substr($password, -2); //Adds last two letters of the password for extra security
		return hash('sha256', $salt . $lastTwo . $password . $salt); //Yay! Bcrypt
	}
	
	function salt() {
		$firstSalt = substr(str_replace('+', '.', base64_encode(sha1(microtime(true), true))), 0, 22);
		return $firstSalt;
	}
	
	function verify($username, $password) {
		if($result = $this->find_by_username($username)) {
			$db_password = $result->password;
			$db_salt = $result->salt;

			if($db_password == Member::hash($password, $db_salt)) {
				return $result->id;
			} else {
				return FALSE;
			}
		}
	}
	
	
}