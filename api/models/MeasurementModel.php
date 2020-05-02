<?php

class MeasurementModel {

	private $connection;

	public function __construct() {
		$this->connection = DB::getInstance()->connection;
	}

	/**
	 * Returns the configured user measurements
	 * @param int $user_id
	 * @return array
	 */
	public function getMeasurements($user_id){
		$data = array();

		$query = $this->connection->prepare('SELECT * FROM measurement WHERE user_id = :user_id');
		$params = array('user_id' => $user_id);

		$query->execute($params);

		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$data[] = $row;
		}

        return $data;
	}

	public function addMeasurement($user_id, $name, $unit_id) {
		$query = $this->connection->prepare('INSERT INTO measurement (user_id, unit_id, name) VALUES (:user_id, :unit_id, :name)');
		$params = array(
			'user_id' => $user_id,
			'name' => $name,
			'unit_id' => $unit_id
		);

		if($query->execute($params)){
			return true;
		}else{
			return false;
		}
	}
}