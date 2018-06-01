<?php

class UpdateSiteCommand extends CConsoleCommand
{
    public function actionUpdateDatabase()
    {
        echo "Finding new task for update".PHP_EOL;

        $us = new SiteUpdaterOnServer();
        $task = $us->FindLastUploadedNotProcessedTask();

        if(empty($task))
        {
            echo "Nothing to update...".PHP_EOL;
            return;
        }

        echo 'Found task with ID '.$task['id'].PHP_EOL;
        $us->UpdateDatabase($task);

        echo 'Done'.PHP_EOL;
    }

    public function actionReindex()
    {
        echo "Finding new task for reindex".PHP_EOL;
        $us = new SiteUpdaterOnServer();
        $task = $us->FindReindexTask();

        if(empty($task))
        {
            echo "Nothing to update...".PHP_EOL;
            return;
        }

        echo 'Found task with ID '.$task['id'].PHP_EOL;
        $us->Reindex($task);

        echo 'Done'.PHP_EOL;
    }


    public function actionDoWork($id)
    {
        echo 'Start updating site by id '.$id."\n";
        $us = new SiteUpdaterOnServer();
        $row = $us->FindLastTask();

        if(empty($row))
        {
            echo "No record in database with ID ".$id."\n";
        }
        else
        {
            if($row['task_state'] != SiteUpdaterOnServer::TASK_UPLOADED)
            {
                echo "Task is not in uploaded state\n";
            }
            else
            {
                echo "Processing...\n";
                $us->ProcessUpdate($row);
            }
        }
    }
}