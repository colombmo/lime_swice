<?php

namespace ls\tests\unit\api\command\v1;

use Permission;
use Question;
use ls\tests\TestBaseClass;
use ls\tests\unit\api\command\mixin\AssertResponse;
use LimeSurvey\Api\Command\V1\QuestionPropertiesGet;
use LimeSurvey\Api\Command\Request\Request;
use LimeSurvey\Api\Command\Response\Status\StatusErrorBadRequest;
use LimeSurvey\Api\Command\Response\Status\StatusErrorNotFound;
use LimeSurvey\Api\Command\Response\Status\StatusErrorUnauthorised;
use LimeSurvey\Api\ApiSession;
use Mockery;

/**
 * @testdox API command v1 QuestionPropertiesGet.
 */
class QuestionPropertiesGetTest extends TestBaseClass
{
    use AssertResponse;

    /**
     * @testdox Returns invalid session response (error unauthorised) if session key is not valid.
     */
    public function testQuestionPropertiesGetInvalidSession()
    {
        $request = new Request(array(
            'sessionKey' => 'not-a-valid-session-id',
            'questionID' => 'questionID',
            'questionSettings' => 'questionSettings',
            'language' => 'language'
        ));
        $response = (new QuestionPropertiesGet())->run($request);

        $this->assertResponseInvalidSession($response);
    }

    /**
     * @testdox Returns error not-found if question id is not valid.
     */
    public function testQuestionPropertiesGetInvalidQuestionId()
    {
        $request = new Request(array(
            'sessionKey' => 'mock',
            'questionID' => 'mock',
            'questionSettings' => 'questionSettings',
            'language' => 'language'
        ));

        $mockApiSession= Mockery::mock(ApiSession::class);
        $mockApiSession
            ->allows()
            ->checkKey('mock')
            ->andReturns(true);

        $command = new QuestionPropertiesGet();
        $command->setApiSession($mockApiSession);

        $response = $command->run($request);

        $this->assertResponseStatus(
            $response,
            new StatusErrorNotFound()
        );

        $this->assertResponseDataStatus(
            $response,
            'Error: Invalid questionid'
        );
    }

    /**
     * @testdox Returns error unauthorised if user does not have permission.
     */
    public function testQuestionPropertiesGetNoPermission()
    {
        $request = new Request(array(
            'sessionKey' => 'mock',
            'questionID' => 'mock',
            'questionSettings' => array(),
            'language' => 'language'
        ));

        $mockApiSession= Mockery::mock(ApiSession::class);
        $mockApiSession
            ->allows()
            ->checkKey('mock')
            ->andReturns(true);

        $mockQuestion= $this->createStub(Question::class);

        $mockModelPermission= Mockery::mock(Permission::class);
        $mockModelPermission
            ->allows()
            ->hasSurveyPermission(0, 'survey', 'read', null)
            ->andReturns(false);

        $command = new QuestionPropertiesGet();
        $command->setApiSession($mockApiSession);
        $command->setQuestionModel($mockQuestion);
        $command->setPermissionModel($mockModelPermission);

        $response = $command->run($request);

        $this->assertResponseStatus(
            $response,
            new StatusErrorUnauthorised()
        );

        $this->assertResponseDataStatus(
            $response,
            'No permission'
        );
    }

    /**
     * @testdox Returns error bad request if invalid language specified.
     */
    public function testQuestionPropertiesGetInvalidLanguage()
    {
        $request = new Request(array(
            'sessionKey' => 'mock',
            'questionID' => 'mock',
            'questionSettings' => array(),
            'language' => 'invalid'
        ));

        $mockApiSession= Mockery::mock(ApiSession::class);
        $mockApiSession
            ->allows()
            ->checkKey('mock')
            ->andReturns(true);

        $mockQuestion= $this->createStub(Question::class);

        $mockModelPermission= Mockery::mock(Permission::class);
        $mockModelPermission
            ->allows()
            ->hasSurveyPermission(0, 'survey', 'read', null)
            ->andReturns(true);

        $command = new QuestionPropertiesGet();
        $command->setApiSession($mockApiSession);
        $command->setQuestionModel($mockQuestion);
        $command->setPermissionModel($mockModelPermission);

        $response = $command->run($request);

        $this->assertResponseStatus(
            $response,
            new StatusErrorBadRequest()
        );

        $this->assertResponseDataStatus(
            $response,
            'Error: Invalid language'
        );
    }

    /**
     * @testdox Returns error bad-request no valid settings provided.
     */
    public function testQuestionPropertiesGetInvalidSettings()
    {
        $request = new Request(array(
            'sessionKey' => 'mock',
            'questionID' => 'mock',
            'questionSettings' => array(),
            'language' => 'language'
        ));

        $mockApiSession= Mockery::mock(ApiSession::class);
        $mockApiSession
            ->allows()
            ->checkKey('mock')
            ->andReturns(true);

        $mockQuestion= $this->createStub(Question::class);

        $mockModelPermission= Mockery::mock(Permission::class);
        $mockModelPermission
            ->allows()
            ->hasSurveyPermission(0, 'survey', 'read', null)
            ->andReturns(true);

        $command = new QuestionPropertiesGet();
        $command->setApiSession($mockApiSession);
        $command->setQuestionModel($mockQuestion);
        $command->setPermissionModel($mockModelPermission);

        $response = $command->run($request);

        $this->assertResponseStatus(
            $response,
            new StatusErrorBadRequest()
        );

        $this->assertResponseDataStatus(
            $response,
            'Error: Invalid language'
        );
    }
}