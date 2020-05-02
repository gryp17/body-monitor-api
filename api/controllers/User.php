<?php

class User extends Controller {

	public function __construct() {

		/**
		 * List of required parameters and permissions for each API endpoint
		 * also indicates the parameter type
		 */
		$this->endpoints = array(
			'login' => array(
				'required_role' => self::PUBLIC_ACCESS,
				'params' => array(
					'email' => array('required', 'valid-email'),
					'password' => 'required'
				)
			),
			'logout' => array(
				'required_role' => self::PUBLIC_ACCESS
			),
			'isLoggedIn' => array(
				'required_role' => self::PUBLIC_ACCESS
			),
			'signup' => array(
				'required_role' => self::PUBLIC_ACCESS,
				'params' => array(
					'email' => array('valid-email', 'unique[email]', 'max-80'),
					'password' => array('min-6', 'max-20', 'strong-password'),
					'repeatPassword' => 'matches[password]'
				)
			)
		);

		#request params
		$this->params = $this->checkRequest();
	}

	public function index() {
		
	}

	/**
	 * Checks if the email and password credentials match and starts the session
	 */
	public function login() {
		$user_model = $this->load_model('UserModel');
		$data = $user_model->checkLogin($this->params['email'], $this->params['password']);

		if ($data === false) {
			$this->sendResponse(0, array('field' => 'password', 'error_code' => ErrorCodes::INVALID_LOGIN));
		} else {
			setcookie(session_name(), session_id(), strtotime('+30 days'), '/');
			$_SESSION['user'] = $data;
			$this->sendResponse(1, $data);
		}
	}

	/**
	 * Logs out the user
	 */
	public function logout() {
		session_destroy();
		unset($_SESSION['user']);
		$this->sendResponse(1, true);
	}

	/**
	 * Checks if the user session is set
	 */
	public function isLoggedIn() {
		if (isset($_SESSION['user'])) {
			$user_model = $this->load_model('UserModel');
			$this->sendResponse(1, array('loggedIn' => true, 'user' => $_SESSION['user']));
		} else {
			$this->sendResponse(1, array('loggedIn' => false));
		}
	}

	/**
	 * New user signup
	 */
	public function signup() {
		$user_model = $this->load_model('UserModel');
		
		$user = $user_model->insertUser($this->params['email'], $this->params['password']);

		if($user !== null){
			$this->sendResponse(1, array('success' => true, 'user' => $user));
		}else{
			$this->sendResponse(0, ErrorCodes::DB_ERROR);
		}
	}
}
