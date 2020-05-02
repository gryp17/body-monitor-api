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
			),
			'addMeasurement' => array(
				'required_role' => self::LOGGED_IN_USER,
				'params' => array(
					'name' => array('min-3', 'max-50'),
					'type' => array('required', 'int')
				)
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

	public function addMeasurement() {
		$measurement_model = $this->load_model('MeasurementModel');
		$measurement_unit_model = $this->load_model('MeasurementUnitModel');

		$units = $measurement_unit_model->getMeasurementUnits();
		$type = $this->params['type'];

		//check if the type is valid
		$index = array_search($type, array_column($units, 'id'));

		if ($index === false) {
			$this->sendResponse(0, array('field' => 'type', 'error_code' => ErrorCodes::EMPTY_FIELD));
		} else {
			$result = $measurement_model->addMeasurement($_SESSION['user']['id'], $this->params['name'], $type);

			if($result) {
				$this->sendResponse(1, array('success' => true));
			} else {
				$this->sendResponse(0, ErrorCodes::DB_ERROR);
			}
		}

	}
}
