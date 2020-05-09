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
			'addMeasurementEntry' => array(
				'required_role' => self::LOGGED_IN_USER,
				'params' => array(
					'measurementId' => array('required', 'int'),
					'date' => 'datetime',
					'value' => 'number'
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

	public function addMeasurementEntry() {
		$measurement_model = $this->load_model('MeasurementModel');
		$measurement_entry_model = $this->load_model('MeasurementEntryModel');
		$measurement_id = $this->params['measurementId'];

		$measurements = $measurement_model->getMeasurements($_SESSION['user']['id']);

		//check if the measurementId is valid
		$index = array_search($measurement_id, array_column($measurements, 'id'));

		if ($index === false) {
			$this->sendResponse(0, array('field' => 'measurementId', 'error_code' => ErrorCodes::EMPTY_FIELD));
		} else {
			$result = $measurement_entry_model->addMeasurementEntry($measurement_id, $this->params['date'], $this->params['value']);

			if($result) {
				$this->sendResponse(1, array('entry' => $result));
			} else {
				$this->sendResponse(0, ErrorCodes::DB_ERROR);
			}
		}
	}

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

	public function getMeasurementEntries() {
		$measurement_entry_model = $this->load_model('MeasurementEntryModel');
		$data = $measurement_entry_model->getMeasurementEntries($_SESSION['user']['id']);
		$this->sendResponse(1, $data);
	}
}
