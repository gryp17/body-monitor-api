<?php

class Measurement extends Controller {

	public function __construct() {

		/**
		 * List of required parameters and permissions for each API endpoint
		 * also indicates the parameter type
		 */
		$this->endpoints = array(
			'getUnits' => array(
				'required_role' => self::PUBLIC_ACCESS
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
			),
			'addMeasurementEntries' => array(
				'required_role' => self::LOGGED_IN_USER,
				'params' => array(
					'date' => 'datetime'
				)
			),
			'deleteMeasurementEntry' => array(
				'required_role' => self::LOGGED_IN_USER,
				'params' => array(
					'entryId' => array('required', 'int')
				)
			),
			'getMeasurementEntries' => array(
				'required_role' => self::LOGGED_IN_USER
			)
		);

		#request params
		$this->params = $this->checkRequest();
	}

	public function index() {
		
	}

	/**
	 * Returns all measurement units
	 */
	public function getUnits() {
		$measurement_unit_model = $this->load_model('MeasurementUnitModel');
		$data = $measurement_unit_model->getMeasurementUnits();
		$this->sendResponse(1, $data);
	}

	/**
	 * Returns all user measurements
	 */
	public function getMeasurements() {
		$measurement_model = $this->load_model('MeasurementModel');
		$data = $measurement_model->getMeasurements($_SESSION['user']['id']);
		$this->sendResponse(1, $data);
	}

	/**
	 * Adds new measurement
	 */
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

	/**
	 * Adds new measurement entries
	 */
	public function addMeasurementEntries() {
		$measurement_model = $this->load_model('MeasurementModel');
		$measurement_entry_model = $this->load_model('MeasurementEntryModel');

		$measurements = $measurement_model->getMeasurements($_SESSION['user']['id']);

		$error = null;

		//check if all the values are valid
		foreach ($this->params['values'] as $measurement_id => $value) {
			//check if it's a valid number
			$validationResult = Validator::checkParam($measurement_id, $value, array('number'), array());

			if ($validationResult !== true) {
				$error = $validationResult;
				break;
			}

			//check if the measurement_id exists and belongs to the logged in user
			$index = array_search($measurement_id, array_column($measurements, 'id'));

			if ($index === false) {
				$error = ErrorCodes::ACCESS_DENIED;
				break;
			}
		}

		if (isset($error)) {
			$this->sendResponse(0, $error);
		} else {
			$entries = array();

			foreach ($this->params['values'] as $measurement_id => $value) {
				$entries[] = $measurement_entry_model->addMeasurementEntry($measurement_id, $this->params['date'], $value);
			}

			$this->sendResponse(1, array('entries' => $entries));
		}
	}

	/**
	 * Deletes a measurement entry
	 */
	public function deleteMeasurementEntry() {
		$measurement_model = $this->load_model('MeasurementModel');
		$measurement_entry_model = $this->load_model('MeasurementEntryModel');

		$entry_record = $measurement_entry_model->getMeasurementEntry($this->params['entryId']);

		if ($entry_record) {
			$measurements = $measurement_model->getMeasurements($_SESSION['user']['id']);
			$index = array_search($entry_record['measurement_id'], array_column($measurements, 'id'));

			if ($index === false) {
				$this->sendResponse(0, ErrorCodes::ACCESS_DENIED);
			} else {
				$measurement_entry_model->deleteMeasurementEntry($this->params['entryId']);
				$this->sendResponse(1, array('success' => true));
			}
		} else {
			$this->sendResponse(0, ErrorCodes::NOT_FOUND);
		}
	}

	/**
	 * Gets all user measurement entries
	 */
	public function getMeasurementEntries() {
		$measurement_entry_model = $this->load_model('MeasurementEntryModel');
		$data = $measurement_entry_model->getMeasurementEntries($_SESSION['user']['id']);
		$this->sendResponse(1, $data);
	}
}
