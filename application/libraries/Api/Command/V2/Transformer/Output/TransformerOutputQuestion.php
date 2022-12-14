<?php

namespace LimeSurvey\Api\Command\V2\Transformer\Output;

use LimeSurvey\Api\Transformer\Output\TransformerOutputActiveRecordAbstract;

class TransformerOutputQuestion extends TransformerOutputActiveRecordAbstract
{
    public function transformAll($array)
    {
        $array = parent::transformAll($array);

        usort($array,function($a, $b){
            return ((int)$a['question_order']) > ((int)$b['question_order']);
        });

        return $array;
    }

    protected function getDataMap()
    {
        return [
            'qid' => true,
            'parent_qid' => true,
            'sid' => true,
            'type' => true,
            'title' => true,
            'preg' => true,
            'other' => true,
            'mandatory' => true,
            'encrypted' => true,
            'question_order' => true,
            'scale_id' => true,
            'same_default' => true,
            'question_theme_name' => true,
            'modulename' => true,
            'gid' => true,
            'relevance' => true,
            'same_script' => true,
        ];
    }
}