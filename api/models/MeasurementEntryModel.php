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

	public function getMeasurementEntries($user_id) {
		$data = array();

		$query = $this->connection->prepare('SELECT * FROM measurement_entry '
			.'WHERE measurement_entry.measurement_id IN (SELECT id FROM measurement WHERE user_id = :user_id) ORDER BY date');
		$params = array('user_id' => $user_id);

		$query->execute($params);

		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			//convert the dates to javascript friendly format
			$row['date'] = Utils::formatDate($row['date']);
			$data[] = $row;
		}

		return $data;
	}
}