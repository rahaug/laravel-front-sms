<?php

namespace RolfHaug\FrontSms;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use RolfHaug\FrontSms\Exceptions\Front\InvalidApiRequest;

class FrontClient
{
    protected $serviceId;
    protected $url = 'https://www.pling.as/psk/push.php';
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->serviceId = config('front-sms.serviceId');

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
        return [
            RequestOptions::JSON => [
                'serviceid' => (int) $this->serviceId,
                'fromid' => $message->from,
                'phoneno' => $message->to,
                'txt' => $message->message,
                'price' => $message->price,
                'unicode' => $message->isUnicode(),
                'password' => config('front-sms.password', null),
            ]
        ];
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
}
