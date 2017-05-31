<?php

namespace SecurID;
use \GuzzleHttp\Handler\CurlHandler;
use \GuzzleHttp\HandlerStack;
use \GuzzleHttp\Client as HttpClient;
use \GuzzleHttp\MessageFormatter;
use \GuzzleHttp\Middleware;
use \Ramsey\Uuid\Uuid;

class Session {

    private $basepath = '/mfa/v1_1';
    private $agentId;
    private $endpoint;
    private $client;
    private $authnAttemptId;
    private $inResponseTo;
    private $lastResponse;

    function __construct($agentId,$endpoint,$clientKey,$options = []) {
        $this->agentId = $agentId;
        $this->endpoint = $endpoint;
        $this->client = new \GuzzleHttp\Client(array_merge([ 'headers' => [ 'client-key' => $clientKey, 'Content-type' => 'application/json' ] ], $options));
    }

    function init($username) {
        $data = $this->doAPIRequest('/authn/initialize',[
            'clientId' => $this->agentId,
            'subjectName' => $username,
            'context' => [
                'messageId' => Uuid::uuid4()
            ]
        ]);
    }

    function verify($code) {
        $data = $this->doAPIRequest('/authn/verify',[
            'context' => [
                'messageId' => Uuid::uuid4(),
                'authnAttemptId' => $this->authnAttemptId,
                'inResponseTo' => $this->inResponseTo
            ],
            'subjectCredentials' => [
                [
                    'methodId' => 'SECURID',
                    'versionId' => '1.0.0',
                    'collectedInputs' => [
                        [
                            'name' => 'SECURID',
                            'value' => $code,
                            'dataType' => 'STRING'
                        ]
                    ]
                ]
            ]
        ]);
        if($data->attemptResponseCode == 'SUCCESS') {
            return true;
        }
        else {
            return false;
        }
    }

    function status($remove = false,$authnAttemptId = null) {
        $data = $this->doAPIRequest('/authn/status',[
            'authnAttemptId' => ($authnAttemptId != null ? $authnAttemptId : $this->authnAttemptId),
            'removeAttemptId' => $remove
        ]);
    }

    function cancel($authnAttemptId = null) {
    	$data = $this->doAPIRequest('/authn/status',[
    		'reason' => 'USER_ACTION',
    		'authnAttemptId' => ($authnAttemptId != null ? $authnAttemptId : $this->authnAttemptId)
    	]);
    }

    function getLastResponse() {
    	return $lastResponse;
    }

    private function doAPIRequest($method,$body) {
        $res = $this->client->request('POST', $this->endpoint.$this->basepath.$method,['body' => json_encode($body) ] );
        $data = json_decode((string) $res->getBody()->getContents());
        $this->lastResponse = $data;
        if(property_exists($data,'context')) {
            $this->authnAttemptId = $data->context->authnAttemptId;
            $this->inResponseTo = $data->context->messageId;
        }
        return $data;
    }
}
