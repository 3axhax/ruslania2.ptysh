<?php

class SiteUpdaterOnServer
{
    const TASK_ADDED = 1;
    const TASK_IN_PROCESS = 2;
    const TASK_DONE = 3;
    const TASK_UPLOADED = 4;

    public function ProcessUpdate($row)
    {
        return;
        echo "Processing ID ".$row['task_id']."\n";

        $id = $row['task_id'];
        $dir = '/tmp/siteupdate';
        $fileZip = $dir.'/'.$id.'.zip';
        $fileMD5 = $dir.'/'.$id.'.md5';
        $fileSQL = $dir.'/'.$id.'.sql';

        echo "MD5 file: ".$fileMD5."\n";
        echo 'ZIP file: '.$fileZip."\n";
        echo 'SQL file: '.$fileSQL."\n";

        $md5Right = '';
        $md5File = '';
        if(file_exists($fileMD5))
            $md5Right = file_get_contents($fileMD5);
        else $this->Error($row, 'MD5 file doesnt exists');

        if(file_exists($fileZip))
            $md5File = md5_file($fileZip);

        if($md5Right !== $md5File)
            $this->Error($row, 'MD5 file error ['.$md5File.'] expected ['.$md5Right.']');

        echo "All files OK. Starting UNZIP \n";

        $zip = new ZipArchive();
        if ($zip->open($fileZip) !== true) $this->Error($row, 'Failed to unzip');

        $zip->extractTo($dir);
        $zip->close();

        echo "Unzipped. Preparing to modify database \n";

        if(!file_exists($fileSQL)) $this->Error($row, 'SQL file not exists '.$fileSQL);

        $user = Yii::app()->db->username;
        $pass = Yii::app()->db->password;
        $command = 'mysql -u '.$user.' -p'.$pass.' ruslania_unicode < '.$fileSQL.' 2>&1';
        //echo "Command: ".$command."\n\n";

        $this->AddTimeStamp($row['id'], 'modify_db_time');
        $t = '';
        $ret = exec($command, $t);
        print_r($ret);
        if(is_array($t) && count($t) > 0) foreach($t as $t1) echo $t1."\n";
        $this->AddTimeStamp($row['id'], 'done_time');

        $sql = 'UPDATE site_update_task SET task_state='.self::TASK_DONE.' WHERE id=:id';
        Yii::app()->db->createCommand($sql)->execute(array(':id' => $row['id']));

        unlink($fileMD5);
        unlink($fileZip);
        unlink($fileSQL);

        // Clear caches
        echo 'Clearing caches';
        $t = '';
        $command = 'echo "flush_all" | nc localhost 11211 -q 5 >/tmp/siteupdate/'.$row['id'].'.out.log 2>/tmp/siteupdate/'.$row['id'].'.err.log &';
        $eRet = exec($command, $t);

        print_r($eRet);
        print_r($t);
        //if(is_array($t) && count($t) > 0) foreach($t as $t1) echo $t1."\n";

        // Start reindex
        echo 'Starting reindex';
        $reindexCommand = '/usr/local/sphinx/bin/indexer --all --rotate > /tmp/siteupdate/'.$row['id'].'.spxout.log 2>/tmp/siteupdate/'.$row['id'].'.spxerr.log &';
        $this->AddTimeStamp($row['id'], 'reindex_start_time');
        $t = '';
        $eRet = exec($reindexCommand, $t);
        print_r($eRet);
        print_r($t);

        //if(is_array($t) && count($t) > 0) foreach($t as $t1) echo $t1."\n";
        $this->AddTimeStamp($row['id'], 'reindex_end_time');
    }

    public function AddTimeStamp($id, $field)
    {
        $sql = 'UPDATE site_update_task SET '.$field.'=NOW() WHERE id=:id LIMIT 1';
        return Yii::app()->db->createCommand($sql)->execute(array(':id' => $id));
    }

    public function Error($id, $msg)
    {
        $sql = 'UPDATE site_update_task SET error=:error, error_time=NOW() WHERE id=:id';
        Yii::app()->db->createCommand($sql)->execute(array(':error' => $msg, ':id' => $id));
        throw new CException($msg);
    }

    public function GetById($id)
    {
        $sql = 'SELECT * FROM site_update_task WHERE id=:id';
        $row = Yii::app()->db->createCommand($sql)->queryRow(true, array(':id' => $id));
        return $row;
    }

    public function AppendInfo($id, $msg)
    {
        $sql = 'UPDATE site_update_task SET info=:info WHERE id=:id';
        Yii::app()->db->createCommand($sql)->execute(array(':info' => $msg, ':id' => $id));
    }

    public function FindLastUploadedNotProcessedTask()
    {
        $sql = <<<SQL
            SELECT *
            FROM site_update_task
            WHERE upload_end_time IS NOT NULL
            AND modify_db_time IS NULL
            AND done_time IS NULL
            AND reindex_start_time IS NULL
            AND reindex_end_time IS NULL
            ORDER BY id DESC
            LIMIT 1
SQL;

        $row = Yii::app()->db->createCommand($sql)->queryRow();
        return $row;
    }

    public function UpdateDatabase($row)
    {
        echo "Processing ID ".$row['task_id']."\n";

        $id = $row['task_id'];
        $dir = '/tmp/siteupdate';
        $fileZip = $dir.'/'.$id.'.zip';
        $fileMD5 = $dir.'/'.$id.'.md5';
        $fileSQL = $dir.'/'.$id.'.sql';

        echo "MD5 file: ".$fileMD5."\n";
        echo 'ZIP file: '.$fileZip."\n";
        echo 'SQL file: '.$fileSQL."\n";

        $md5Right = '';
        $md5File = '';
        if(file_exists($fileMD5))
            $md5Right = file_get_contents($fileMD5);
        else $this->Error($row, 'MD5 file doesnt exists');

        if(file_exists($fileZip))
            $md5File = md5_file($fileZip);

        if($md5Right !== $md5File)
            $this->Error($row, 'MD5 file error ['.$md5File.'] expected ['.$md5Right.']');

        echo "All files OK. Starting UNZIP \n";

        $zip = new ZipArchive();
        if ($zip->open($fileZip) !== true) $this->Error($row, 'Failed to unzip');

        $zip->extractTo($dir);
        $zip->close();

        echo "Unzipped. Preparing to modify database \n";

        if(!file_exists($fileSQL)) $this->Error($row, 'SQL file not exists '.$fileSQL);

        $user = Yii::app()->db->username;
        $pass = Yii::app()->db->password;
        $command = 'mysql -u '.$user.' -p'.$pass.' ruslania_unicode < '.$fileSQL;

        //echo "Command: ".$command."\n\n";

        $this->AddTimeStamp($row['id'], 'modify_db_time');
        $ret = shell_exec($command);
        print_r($ret);

        $this->AddTimeStamp($row['id'], 'done_time');

        unlink($fileMD5);
        unlink($fileZip);
        unlink($fileSQL);
    }

    public function FindReindexTask()
    {
        $sql = <<<SQL
            SELECT *
            FROM site_update_task
            WHERE upload_end_time IS NOT NULL
            AND modify_db_time IS NOT NULL
            AND done_time IS NOT NULL
            AND reindex_start_time IS NULL
            AND reindex_end_time IS NULL
            ORDER BY id DESC
            LIMIT 1
SQL;

        $row = Yii::app()->db->createCommand($sql)->queryRow();
        return $row;
    }

    public function Reindex($row)
    {
        // Clear caches
        echo 'Clearing caches';
        $command = 'echo "flush_all" | nc localhost 11211 -q 5 >/tmp/siteupdate/'.$row['id'].'.out.log 2>/tmp/siteupdate/'.$row['id'].'.err.log &';
        $eRet = shell_exec($command);

        print_r($eRet);

        // Start reindex
        echo 'Starting reindex';
        $reindexCommand = '/usr/local/sphinx/bin/indexer --all --rotate > /tmp/siteupdate/'.$row['id'].'.spxout.log 2>/tmp/siteupdate/'.$row['id'].'.spxerr.log &';
        $this->AddTimeStamp($row['id'], 'reindex_start_time');
        $eRet = shell_exec($reindexCommand);
        print_r($eRet);

        $this->AddTimeStamp($row['id'], 'reindex_end_time');
    }
}