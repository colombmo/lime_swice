<?php

namespace LimeSurvey\Api\Command\V1;

use QuestionGroup;
use QuestionGroupL10n;
use LimeSurvey\Api\Command\CommandInterface;
use LimeSurvey\Api\Command\Request\Request;
use LimeSurvey\Api\Command\Mixin\Auth\AuthSession;
use LimeSurvey\Api\Command\Mixin\Auth\AuthPermission;
use LimeSurvey\Api\Command\Mixin\CommandResponse;
use LimeSurvey\Api\Command\Mixin\Accessor\SurveyModel;

class QuestionGroupAdd implements CommandInterface
{
    use AuthSession;
    use AuthPermission;
    use CommandResponse;
    use SurveyModel;

    /**
     * Run group add command.
     *
     * @access public
     * @param \LimeSurvey\Api\Command\Request\Request $request
     * @return \LimeSurvey\Api\Command\Response\Response
     */
    public function run(Request $request)
    {
        $sSessionKey = (string) $request->getData('sessionKey');
        $iSurveyID = (int) $request->getData('surveyID');
        $sGroupTitle = (string) $request->getData('groupTitle');
        $sGroupDescription = (string) $request->getData('groupDescription');

        if (
            ($response = $this->checkKey($sSessionKey)) !== true
        ) {
            return $response;
        }

        if (
            ($response = $this->hasSurveyPermission(
                $iSurveyID,
                'survey',
                'update'
            )
            ) !== true
        ) {
            return $response;
        }

        $oSurvey = $this->getSurveyModel($iSurveyID);
        if (!isset($oSurvey)) {
            return $this->responseErrorBadRequest(
                array('status' => 'Error: Invalid survey ID')
            );
        }

        if ($oSurvey->isActive) {
            return $this->responseErrorBadRequest(
                array('status' => 'Error: Survey is active and not editable')
            );
        }

        $oGroup = new QuestionGroup();
        $oGroup->sid = $iSurveyID;
        $oGroup->group_order = getMaxGroupOrder($iSurveyID);
        if (!$oGroup->save()) {
            return $this->responseError(
                array('status' => 'Creation Failed')
            );
        }

        $oQuestionGroupL10n = new QuestionGroupL10n();
        $oQuestionGroupL10n->group_name = $sGroupTitle;
        $oQuestionGroupL10n->description = $sGroupDescription;
        $oQuestionGroupL10n->language = \Survey::model()->findByPk($iSurveyID)->language;
        $oQuestionGroupL10n->gid = $oGroup->gid;

        if ($oQuestionGroupL10n->save()) {
            return $this->responseSuccess((int) $oGroup->gid);
        } else {
            return $this->responseError(
                array('status' => 'Creation Failed')
            );
        }
    }
}