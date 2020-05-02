<?php

class Measurement extends Controller {

	public function __construct() {

		/**
		 * List of required parameters and permissions for each API endpoint
		 * also indicates the parameter type
		 */
		$this->endpoints = array(
			'getUnits' => array(
				'required_role' => self::LOGGED_IN_USER
			),
			'getMeasurements' => array(
				'required_role' => self::LOGGED_IN_USER
			)
		);

		#request params
		$this->params = $this->checkRequest();
	}

	public function index() {
		
	}

	public function getUnits() {
		$measurement_unit_model = $this->load_model('MeasurementUnitModel');
		$data = $measurement_unit_model->getMeasurementUnits();
		$this->sendResponse(1, $data);
	}

	public function getMeasurements() {
		$measurement_model = $this->load_model('MeasurementModel');
		$data = $measurement_model->getMeasurements($_SESSION['user']['id']);
		$this->sendResponse(1, $data);
	}
}
