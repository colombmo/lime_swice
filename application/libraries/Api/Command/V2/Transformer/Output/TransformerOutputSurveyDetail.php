<?php

namespace LimeSurvey\Api\Command\V2\Transformer\Output;

use LimeSurvey\Api\Transformer\Output\TransformerOutputActiveRecordAbstract;

class TransformerOutputSurveyDetail extends TransformerOutputActiveRecordAbstract
{
    protected function getDataMap()
    {
        return [
            'sid' => true,
            'gsid' => true,
            'active' => true,
            'language'  => true,
            'expires' => true,
            'startdate' => true,
            'anonymized' => true,
            'savetimings' => true,
            'additional_languages' => true,
            'datestamp' => true,
            "usecookie" => true,
            "allowregister" => true,
            "allowsave" => true,
            "autonumber_start" => true,
            "autoredirect" => true,
            "allowprev" => true,
            "printanswers" => true,
            "ipaddr" => true,
            "ipanonymize" => true,
            "refurl" => true,
            "datecreated" => true,
            "publicstatistics" => true,
            "publicgraphs" => true,
            "listpublic" => true,
            "sendconfirmation" => true,
            "tokenanswerspersistence" => true,
            "assessments" => true,
            "usecaptcha" => true,
            "usetokens" => true,
            "bounce_email" => true,
            "attributedescriptions" => true,
            "emailresponseto" => true,
            "emailnotificationto" => true,
            "tokenlength" => true,
            "showxquestions" => true,
            "showgroupinfo" => true,
            "shownoanswer" => true,
            "showqnumcode" => true,
            "bouncetime" => true,
            "bounceprocessing" => true,
            "bounceaccounttype" => true,
            "bounceaccounthost" => true,
            "bounceaccountpass" => true,
            "bounceaccountencryption" => true,
            "bounceaccountuser" => true,
            "showwelcome" => true,
            "showprogress" => true,
            "questionindex" => true,
            "navigationdelay" => true,
            "nokeyboard" => true,
            "alloweditaftercompletion" => true,
            "googleanalyticsstyle" => true,
            "googleanalyticsapikey" => true,
            "showsurveypolicynotice" => true,
        ];
    }
}
