<?php

class MeasurementUnitModel {

	private $connection;

	public function __construct() {
		$this->connection = DB::getInstance()->connection;
	}

	/**
	 * Returns the measurement units
	 * @return array
	 */
	public function getMeasurementUnits(){
		$data = array();

		$query = $this->connection->prepare('SELECT * FROM measurement_unit');
		$query->execute();

		while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
			$data[] = $row;
		}

        return $data;
	}
}