<?php

namespace LimeSurvey\Api\Command\V1;

use Condition;
use Exception;
use LimeExpressionManager;
use Question;
use Survey;
use LimeSurvey\Api\Command\CommandInterface;
use LimeSurvey\Api\Command\Request\Request;
use LimeSurvey\Api\Command\Mixin\Auth\AuthSession;
use LimeSurvey\Api\Command\Mixin\Auth\AuthPermission;
use LimeSurvey\Api\Command\Mixin\CommandResponse;

class QuestionDelete implements CommandInterface
{
    use AuthSession;
    use AuthPermission;
    use CommandResponse;

    /**
     * Run survey question delete command.
     *
     * @access public
     * @param \LimeSurvey\Api\Command\Request\Request $request
     * @return \LimeSurvey\Api\Command\Response\Response
     */
    public function run(Request $request)
    {
        $sSessionKey = (string) $request->getData('sessionKey');
        $iQuestionID = (int) $request->getData('questionID');

        if (
            ($response = $this->checkKey($sSessionKey)) !== true
        ) {
            return $response;
        }

        $iQuestionID = (int) $iQuestionID;
        $oQuestion = Question::model()->findByPk($iQuestionID);
        if (!isset($oQuestion)) {
            return $this->responseErrorNotFound(
                array('status' => 'Error: Invalid question ID')
            );
        }

        $iSurveyID = $oQuestion['sid'];

        if (
            ($response = $this->hasSurveyPermission(
                    $iSurveyID,
                    'surveycontent',
                    'delete'
                )
            ) !== true
        ) {
            return $response;
        }

        $oSurvey = Survey::model()->findByPk($iSurveyID);

        if ($oSurvey->isActive) {
            return $this->responseErrorBadRequest(
                array('status' => 'Survey is active and not editable')
            );
        }

        $oCondition = Condition::model()->findAllByAttributes(array('cqid' => $iQuestionID));
        if (count($oCondition) > 0) {
            return $this->responseErrorBadRequest(
                array('status' => 'Cannot delete Question. Others rely on this question')
            );
        }

        LimeExpressionManager::RevertUpgradeConditionsToRelevance(null, $iQuestionID);

        try {
            $oQuestion->delete();
            return $this->responseSuccess((int) $iQuestionID);
        } catch (Exception $e) {
            return $this->responseException($e);
        }
    }
}