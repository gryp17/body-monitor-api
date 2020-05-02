<?php

class MeasurementEntryModel {

	private $connection;

	public function __construct() {
		$this->connection = DB::getInstance()->connection;
	}

	public function addMeasurementEntry($measurement_id, $date, $value) {
		$query = $this->connection->prepare('INSERT INTO measurement_entry (measurement_id, date, value) VALUES (:measurement_id, :date, :value)');
		$params = array(
			'measurement_id' => $measurement_id,
			'date' => $date,
			'value' => $value
		);

		if($query->execute($params)){
			return true;
		}else{
			return false;
		}
	}
}