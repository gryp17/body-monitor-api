<?php

class UserModel {

	private $connection;

	public function __construct() {
		$this->connection = DB::getInstance()->connection;
	}


	/**
	 * Checks if there is an user with the provided credentials
	 * @param string $email
	 * @param string $password
	 * @return boolean
	 */
	public function checkLogin($email, $password) {		
		$query = $this->connection->prepare('SELECT * FROM user WHERE email = :email AND password = :password');
		$params = array('email' => $email, 'password' => md5($password));
		$query->execute($params);

        $result = $query->fetch(PDO::FETCH_ASSOC);
		
        if ($result) {
			unset($result['password']);
			
			//convert the dates to javascript friendly format
			$result['registered'] = Utils::formatDate($result['register_date']);
			
            return $result;
		}else{
			return false;
		}
	}
	
	/**
	 * Checks if the specified field is unique in the user table
	 * @param string $field
	 * @param string $value
	 * @return boolean
	 */
	public function isUnique($field, $value){
		
		if($field !== 'username' && $field !== 'email'){
			return true;
		}else{
			if($field === 'username'){
				$query = $this->connection->prepare('select * from user where username = :username');
				$query->execute(array('username' => $value));
			}else{
				$query = $this->connection->prepare('select * from user where email = :email');
				$query->execute(array('email' => $value));
			}
			
			$result = $query->fetch(PDO::FETCH_ASSOC);
		
			if ($result) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Signup new user
	 * @param string $email
	 * @param string $password
	 * @return int
	 */
	public function insertUser($email, $password){
		$password = md5($password);
		$query = $this->connection->prepare('INSERT INTO user '
				. '(password, email, registered) '
				. 'VALUES '
				. '(:password, :email, now())');
		
		$params = array(
			'password' => $password,
			'email' => $email
		);
		
		if($query->execute($params)){
			return $this->connection->lastInsertId();
		}else{
			return null;
		}
	}
}
