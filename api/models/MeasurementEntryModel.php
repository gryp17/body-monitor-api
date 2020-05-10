<?php

class MeasurementEntryModel {

	private $connection;

	public function __construct() {
		$this->connection = DB::getInstance()->connection;
	}

	/**
	 * Adds new measurement entry
	 * @param int $measurement_id
	 * @param string $date
	 * @param int $value
	 * @return array
	 */
	public function addMeasurementEntry($measurement_id, $date, $value) {
		$query = $this->connection->prepare('INSERT INTO measurement_entry (measurement_id, date, value) VALUES (:measurement_id, :date, :value)');
		$params = array(
			'measurement_id' => $measurement_id,
			'date' => $date,
			'value' => $value
		);

		if($query->execute($params)){
			$entry_id = $this->connection->lastInsertId();
			return $this->getMeasurementEntry($entry_id);
		}else{
			return null;
		}
	}

	/**
	 * Gets the measurement entry by id
	 * @param int $id
	 * @return array
	 */
	public function getMeasurementEntry($id) {
		$query = $this->connection->prepare('SELECT * FROM measurement_entry WHERE id = :id');
		$params = array('id' => $id);
		$query->execute($params);

		$result = $query->fetch(PDO::FETCH_ASSOC);
		
        if ($result) {
			//convert the dates to javascript friendly format
			$result['date'] = Utils::formatDate($result['date']);
            return $result;
		}else{
			return null;
		}
	}

	/**
	 * Deletes a measurement entry
	 * @param int $id
	 * @return boolean
	 */
	public function deleteMeasurementEntry($id) {
		$query = $this->connection->prepare('DELETE FROM measurement_entry WHERE id = :id');
		$params = array('id' => $id);
		return $query->execute($params);
	}

	/**
	 * Returns all measurement entries for the specified user
	 * @param int $user_id
	 * @return array
	 */
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