<?php

class EDbSession extends CDbHttpSession
{
    public $autoCreateSessionTable = true;
	public $compareIpOctets = 2;

	protected function createSessionTable($db, $tableName)
	{
		$db->createCommand("CREATE TABLE {$tableName} (id CHAR(32) PRIMARY KEY, clientIpRange VARCHAR(15) NOT NULL, expire INTEGER(10) UNSIGNED NOT NULL, data TEXT NOT NULL) COLLATE = utf8_bin")->execute();
	}

	public function readSession($id)
	{
		$db = $this->getDbConnection();

		$clientIpRange = $this->getClientIpRange();

		$expire = time();

		$sql = "SELECT data FROM {$this->sessionTableName} WHERE id = :id AND clientIpRange = :clientIpRange AND expire > :expire LIMIT 1";

		$data = $db->createCommand($sql)->bindValue(':id', $id)->bindValue(':clientIpRange', $clientIpRange)->bindValue(':expire', $expire)->queryScalar();

		return (false === $data) ? '' : $data;

	}

	public function writeSession($id, $data)
	{

		$db = $this->getDbConnection();

		$clientIpRange = $this->getClientIpRange();

		$expire = time() + $this->getTimeout();

		$sql = "SELECT id FROM {$this->sessionTableName} WHERE id = :id AND clientIpRange = :clientIpRange LIMIT 1";

		if (false === $db->createCommand($sql)->bindValue(':id', $id)->bindValue(':clientIpRange', $clientIpRange)->queryScalar())
		{
			$sql = "DELETE FROM {$this->sessionTableName} WHERE id = :id LIMIT 1";
			$db->createCommand($sql)->bindValue(':id', $id)->execute();
			$sql = "INSERT INTO {$this->sessionTableName} (id, clientIpRange, expire, data) VALUES (:id, :clientIpRange, :expire, :data)";
			$db->createCommand($sql)->bindValue(':id', $id)->bindValue(':clientIpRange', $clientIpRange)->bindValue(':expire', $expire)->bindValue(':data', $data)->execute();
		}
		else
		{
			$sql = "UPDATE {$this->sessionTableName} SET expire = :expire, data = :data WHERE id = :id LIMIT 1";
			$db->createCommand($sql)->bindValue(':expire', $expire)->bindValue(':data', $data)->bindValue(':id', $id)->execute();
		}

		return true;

	}

	public function getClientIpRange()
	{

		preg_match("/^([0-9]{1,3}+\.)([0-9]{1,3}+\.)([0-9]{1,3}+\.)([0-9]{1,3}+)$/", Yii::app()->request->getUserHostAddress(), $match);

		$clientIpRange = '';

		for ($i=1; $i<=$this->compareIpOctets; $i++)
		{
			$clientIpRange .= $match[$i];
		}

		return $clientIpRange;

	}

}