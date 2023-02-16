<?php

namespace RolfHaug\FrontSms;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;
use RolfHaug\FrontSms\Exceptions\Front\InvalidApiRequest;

class FrontClient
{
    protected $serviceId;

    private $fakeMessages;

    protected $url = 'https://www.pling.as/psk/push.php';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public function __construct(Client $client)
    {
        $this->serviceId = config('front-sms.serviceId');
        $this->fakeMessages = config('front.fakeMessages');

        $this->client = $client;
    }

    /**
     * Send message to Front API.
     *
     * @param FrontMessage $message
     * @return object API response
     * @throws InvalidApiRequest
     */
    public function push(FrontMessage $message)
    {
        if ($this->fakeMessages) {
            return $this->_pretendPush($message);
        }
        $request = $this->client->post($this->url, $this->mapPayload($message));

        $response = $this->parseResponse($request);

        if ($response->errorcode && $response->errorcode != 0) {
            throw new InvalidApiRequest("Error Code {$response->errorcode}: {$response->description}");
        }

        $message->markAsSent($response->id);

        return $response;
    }

    /**
     * Map message data to payload fields.
     *
     * @param FrontMessage $message
     * @return array
     */
    private function mapPayload(FrontMessage $message)
    {
        $payload = [
            RequestOptions::JSON => [
                'serviceid' => (int) $this->serviceId,
                'fromid' => $message->from,
                'phoneno' => $message->to,
                'txt' => $message->message,
                'price' => $message->price,
                'unicode' => $message->isUnicode()
            ]
        ];

        if ($password = config('front-sms.password')) {
            $payload[RequestOptions::JSON]['password'] = $password;
        }

        return $payload;
    }

    /**
     * Parse API response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return mixed
     */
    private function parseResponse(\Psr\Http\Message\ResponseInterface $response)
    {
        return json_decode($response->getBody());
    }

    /**
     * Pretend sending message, outputs message in Laravel log.
     *
     * @param FrontMessage $message
     * @return object API response
     */
    private function _pretendPush($message)
    {
        Log::info('Pretending to send SMS', [
            'serviceid' => (int) $this->serviceId,
            'fromid' => $message->from,
            'phoneno' => $message->to,
            'txt' => $message->message,
            'price' => $message->price,
            'unicode' => $message->isUnicode()
        ]);

        // Fake response
        return json_encode([
            'id' => 'PRETEND_ID',
            'errorcode' => 0,
            'description' => 'OK'
        ]);
    }
}
