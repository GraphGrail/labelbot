<?php

namespace common\components;

use common\models\Label;
use common\models\LabelGroup;
use Yii;

/**
 * LabelsTree component
 * 
 * Creates Labels Tree structure in DB for LabelGroup
 */
class LabelsTree extends yii\base\BaseObject
{
	protected $labelGroupId;

	/**
	 * Json formatted Labels tree example: 
	 *	 [{"1-1-1":[{"2-1-1":[]},{"2-1-2":[{"3-1-1":[]},{"3-1-2":[]}]}]},{"1-1-2":[]}] 
	 */
	protected $asJson;
	protected $labelGroupName;

    public function __construct(LabelGroup $labelGroup)
    {
        $this->labelGroupId   = $labelGroup->id;
        $this->labelGroupName = $labelGroup->name;
        $this->asJson         = $labelGroup->labels_tree;

        parent::__construct();
    }

    public function validate() : bool
    {
        // TODO: upgrade this validator
        $decoded = $this->asArray(); 
        if ($decoded) {


        	return true;
        }
        return false;
    }

    public function create() : bool
    {
    	// At first, we create root label with parent_id=0
    	$parent_id = $this->createLabel(0, $this->labelGroupName);

        $this->createLabelsRecursievly($parent_id, $this->asArray());
        
        return true;
    }

    public function delete()
    {
    	Label::deleteAll(['parent_label_id'=>$this->labelGroupId]);
    }

    private function createLabelsRecursievly(int $parent_id, $array) {
	    foreach ($array as $key => $value) {
	    	if (is_object($value)) {
	    		$this->createLabelsRecursievly($parent_id, (array) $value);
	    		continue;
	    	}

	    	$parent_id = $this->createLabel($parent_id, $key);
    		$this->createLabelsRecursievly($parent_id, $value);
    	}
    }


    private function createLabel(int $parent_label_id, string $text) : int
    {
    	$label = new Label;
    	$label->parent_label_id = $parent_label_id;
    	$label->text = $text;
    	$label->label_group_id = $this->labelGroupId;
    	$label->ordering = 0;

    	if (!$label->save()) return null;

    	return $label->id;
    }

    private function asArray()
    {
    	return json_decode($this->asJson);
    }

}