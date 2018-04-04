<?php

namespace console\jobs;

use common\models\Data;
use common\models\Dataset;
use Yii;

/**
 * Class ParseDatasetJob.
 */
class ParseDatasetJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $dataset_id;
    public $file;

    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
    	$dataset = Dataset::findOne($this->dataset_id);

    	if ($dataset === null) {
    		throw new \Exception('Cant find Dataset record');
    	}

    	$fh = fopen($this->file, 'r');

    	if ($fh === false) {
    		$dataset->updateStatus(Dataset::STATUS_PARSING_ERROR);
    		throw new \Exception('Cant open uploaded Dataset file');
    	}

	    // get the first row, which contains the column-titles
	    //$header = fgetcsv($fh);

        $maximum = Yii::$app->params['datasetFileMaxRecords'];
        $counter = 0;

        // TODO: Get data from file by portions and insert them to db in one query.
	    // But for now we've just loop through the file line-by-line
	    while ( ($counter++ < $maximum) && (($dataArr = fgetcsv($fh)) !== false) )
	    {
	    	$data = new Data;
            $data->dataset_id = $dataset->id;
            // For now, we get last column from .csv file as data
            $string = end($dataArr);
            $toEncoding = 'UTF-8';
            if(!mb_check_encoding($string, $toEncoding)) {
                $fromEncoding = mb_detect_encoding($string);
                if ($fromEncoding === $toEncoding) {
                    $fromEncoding = 'Windows-1251';
                }
                $string = mb_convert_encoding($string, $toEncoding, $fromEncoding);
            }

            $data->data_raw = $string;
            // and don't filter data yet
            $data->data = $data->data_raw;

	        if (!$data->save()) {
                $dataset->updateStatus(Dataset::STATUS_PARSING_ERROR);

                // TODO: remove previously saved data from this dataset
                
                throw new \Exception('Cant save Data');
            }

            // I don't know, is that really necessary, but it couldn't harm
            unset($dataArr);
        }
	    fclose($fh);
	    unlink($this->file);

	    $dataset->updateStatus(Dataset::STATUS_READY);

    }
}
