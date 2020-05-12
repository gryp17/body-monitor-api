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
		$query = $this->connection->prepare('SELECT * FROM body_user WHERE email = :email AND password = :password');
		$params = array('email' => $email, 'password' => md5($password));
		$query->execute($params);

        $result = $query->fetch(PDO::FETCH_ASSOC);
		
        if ($result) {
			unset($result['password']);
			
			//convert the dates to javascript friendly format
			$result['registered'] = Utils::formatDate($result['registered']);
			
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
				$query = $this->connection->prepare('select * from body_user where username = :username');
				$query->execute(array('username' => $value));
			}else{
				$query = $this->connection->prepare('select * from body_user where email = :email');
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
		$query = $this->connection->prepare('INSERT INTO body_user '
				. '(password, email, registered) '
				. 'VALUES '
				. '(:password, :email, now())');
		
		$params = array(
			'password' => $password,
			'email' => $email
		);
		
		if($query->execute($params)){
			$user_id = $this->connection->lastInsertId();
			return $this->getUser($user_id);
		}else{
			return null;
		}
	}

	/**
	 * Returns the user data
	 * @param int $id
	 * @return array
	 */
	public function getUser($id){
		$query = $this->connection->prepare('SELECT * FROM body_user WHERE id = :id');
		$params = array('id' => $id);
		$query->execute($params);

        $result = $query->fetch(PDO::FETCH_ASSOC);
		
        if ($result) {
			unset($result['password']);
			
			//convert the dates to javascript friendly format
			$result['registered'] = Utils::formatDate($result['registered']);

            return $result;
		}else{
			return null;
		}
	}
}
