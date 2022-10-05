<?php

namespace LimeSurvey\Models\Services;

class Quotas
{

    /** @var \Survey the survey */
    private $survey;


    /**
     * Quicktranslation constructor.
     *
     * @param \Survey $survey the survey object
     *
     */
    public function __construct(\Survey $survey)
    {
        $this->survey = $survey;
    }


    public function getQuotaStructure(){
        $totalquotas = 0;
        $totalcompleted = 0;
        if (!empty($this->survey->quotas)) {
            $aQuotaItems = array();
            //loop through all quotas
            foreach ($this->survey->quotas as $oQuota) {
                $totalquotas += $oQuota->qlimit;
                $totalcompleted += $oQuota->completeCount;

                // Edit URL  --> hmmm could be done in a function in Quota model --> getButtons()
                $aData['aEditUrls'][$oQuota->primaryKey] =
                    App()->createUrl("admin/quotas/sa/editquota/surveyid/" . $this->survey->sid, array(
                    'sid' => $this->survey->sid,
                   // 'action' => 'quotas', --> not needed in the action itself
                    'quota_id' => $oQuota->primaryKey,
                   // 'subaction' => 'quota_editquota'
                ));

                // Delete URL  --> hmmm could be done in a function in Quota model --> getButtons()
                $aData['aDeleteUrls'][$oQuota->primaryKey] =
                    App()->createUrl("admin/quotas/sa/delquota/surveyid/" . $this->survey->sid, array(
                    'sid' => $this->survey->sid,
                   // 'action' => 'quotas',
                    'quota_id' => $oQuota->primaryKey,
                   // 'subaction' => 'quota_delquota'
                ));

                //loop through all sub-parts
                foreach ($oQuota->quotaMembers as $oQuotaMember) {
                    $aQuestionAnswers = self::getQuotaAnswers($oQuotaMember['qid'], $oQuota['id']);
                    if ($oQuotaMember->question->type == '*') {
                        $answerText = $oQuotaMember->code;
                    } else {
                        $answerText = isset($aQuestionAnswers[$oQuotaMember['code']]) ? flattenText($aQuestionAnswers[$oQuotaMember['code']]['Display']) : null;
                    }

                    $aQuotaItems[$oQuota['id']][] = array(
                        'oQuestion' => \Question::model()
                            ->with('questionl10ns', array('language' => $this->survey->language))
                            ->findByPk(array('qid' => $oQuotaMember['qid'])),
                        'answer_title' => $answerText,
                        'oQuotaMember' => $oQuotaMember,
                        'valid' => isset($answerText),
                    );
                }
            }
            $aData['aQuotaItems'] = $aQuotaItems;

            // take the last quota as base for bulk edits
            $aData['oQuota'] = $oQuota;
            $aData['aQuotaLanguageSettings'] = array();
            foreach ($oQuota->languagesettings as $languagesetting) {
                $aData['aQuotaLanguageSettings'][$languagesetting->quotals_language] = $languagesetting;
            }
        }
        $aData['totalquotas'] = $totalquotas;
        $aData['totalcompleted'] = $totalcompleted;

        return $aData;
    }

    /**
     * Get Quota Answers
     *
     * todo: rewrite this function, use switch instead of if-elseif... and create OOPs for questiontypes
     * @param integer $iQuestionId
     * @param integer $iQuotaId
     * @return array
     */
    public function getQuotaAnswers(int $iQuestionId, int $iQuotaId)
    {
        $iQuestionId = sanitize_int($iQuestionId);
        $iQuotaId    = sanitize_int($iQuotaId);
        $sBaseLang = $this->survey->language;

        $aQuestion = \Question::model()
            ->with('questionl10ns', array('language' => $sBaseLang))
            ->findByPk(array('qid' => $iQuestionId));
        $aQuestionType = $aQuestion['type'];
        $aAnswerList = [];
        switch ($aQuestionType){
            case \Question::QT_M_MULTIPLE_CHOICE:
                $aResults = \Question::model()
                    ->with('questionl10ns', array('language' => $sBaseLang))
                    ->findAllByAttributes(array('parent_qid' => $iQuestionId));
                foreach ($aResults as $oDbAnsList) {
                    $tmparrayans = array('Title' => $aQuestion['title'],
                        'Display' => substr($oDbAnsList->questionl10ns[$sBaseLang]->question, 0, 40),
                        'code' => $oDbAnsList->title);
                    $aAnswerList[$oDbAnsList->title] = $tmparrayans;
                }
                break;
            case  \Question::QT_G_GENDER:
                $aAnswerList = array(
                    'M' => array('Title' => $aQuestion['title'], 'Display' => gT("Male"), 'code' => 'M'),
                    'F' => array('Title' => $aQuestion['title'], 'Display' => gT("Female"), 'code' => 'F'));
                break;
            case  \Question::QT_L_LIST:
            case  \Question::QT_O_LIST_WITH_COMMENT:
            case  \Question::QT_EXCLAMATION_LIST_DROPDOWN:
            $aAnsResults = \Answer::model()
                ->with('answerl10ns', array('language' => $sBaseLang))
                ->findAllByAttributes(array('qid' => $iQuestionId));

            foreach ($aAnsResults as $aDbAnsList) {
                $aAnswerList[$aDbAnsList['code']] = array('Title' => $aQuestion['title'],
                    'Display' => $aDbAnsList->answerl10ns[$sBaseLang]->answer,
                    'code' => $aDbAnsList['code']);
            }
            break;
            case \Question::QT_A_ARRAY_5_POINT:
                $aAnsResults = \Question::model()
                    ->with('questionl10ns', array('language' => $sBaseLang))
                    ->findAllByAttributes(array('parent_qid' => $iQuestionId));

                foreach ($aAnsResults as $aDbAnsList) {
                    for ($x = 1; $x < 6; $x++) {
                        $tmparrayans = array('Title' => $aQuestion['title'],
                            'Display' => substr($aDbAnsList->questionl10ns[$sBaseLang]->question, 0, 40) . ' [' . $x . ']',
                            'code' => $aDbAnsList['title']);
                        $aAnswerList[$aDbAnsList['title'] . "-" . $x] = $tmparrayans;
                    }
                }
                break;
            case  \Question::QT_B_ARRAY_10_CHOICE_QUESTIONS:
                $aAnsResults = \Question::model()
                    ->with('questionl10ns', array('language' => $sBaseLang))
                    ->findAllByAttributes(array('parent_qid' => $iQuestionId));

                foreach ($aAnsResults as $aDbAnsList) {
                    for ($x = 1; $x < 11; $x++) {
                        $tmparrayans = array('Title' => $aQuestion['title'],
                            'Display' => substr($aDbAnsList->questionl10ns[$sBaseLang]->question, 0, 40) . ' [' . $x . ']',
                            'code' => $aDbAnsList['title']);
                        $aAnswerList[$aDbAnsList['title'] . "-" . $x] = $tmparrayans;
                    }
                }
                break;
            case \Question::QT_Y_YES_NO_RADIO:
                $aAnswerList = array(
                    'Y' => array('Title' => $aQuestion['title'], 'Display' => gT("Yes"), 'code' => 'Y'),
                    'N' => array('Title' => $aQuestion['title'], 'Display' => gT("No"), 'code' => 'N'));
                break;
            case \Question::QT_I_LANGUAGE:
                $slangs = $this->survey->allLanguages;

                foreach ($slangs as $key => $value) {
                    $tmparrayans = array('Title' => $aQuestion['title'],
                        'Display' => getLanguageNameFromCode($value, false), $value);
                    $aAnswerList[$value] = $tmparrayans;
                }
                break;
        }

        if (!empty($aAnswerList)){
            // Now we mark answers already used in this quota as such
            $aExistsingAnswers = \QuotaMember::model()->findAllByAttributes(array('sid' => $this->survey->sid,
                'qid' => $iQuestionId, 'quota_id' => $iQuotaId));
            foreach ($aExistsingAnswers as $aAnswerRow) {
                if (array_key_exists($aAnswerRow['code'], $aAnswerList)) {
                    $aAnswerList[$aAnswerRow['code']]['rowexists'] = '1';
                }
            }
        }

        return  $aAnswerList;
    }
}