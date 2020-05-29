<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use RolfHaug\FrontSms\Exceptions\Front\InvalidApiRequest;
use RolfHaug\FrontSms\FrontClient;
use RolfHaug\FrontSms\FrontMessage;
use Tests\TestCase;

class FrontClientTest extends TestCase
{
    /** @test */
    public function it_sends_sms_to_correct_url_with_correct_payload_and_updates_the_message_orig_id()
    {
        $sms = factory(FrontMessage::class)->create(['price' => 100]);
        $origId = 1234;

        $middleware = $this->pushMessageAndReturnMiddleware($sms, ['errorcode' => 0, 'id' => $origId, 'description' => 'OK']);

        /** @var Request $middleware */
        $request = $middleware['request'];

        $this->assertEquals('POST', $request->getMethod());

        /** @var Uri $uri */
        $uri = $request->getUri();

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('www.pling.as', $uri->getHost());
        $this->assertEquals('/psk/push.php', $uri->getPath());
        $this->assertEmpty($uri->getQuery());

        // Required header is sent
        $headers = $request->getHeaders();
        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertContains('application/json', $headers['Content-Type']);

        // Request Payload
        $options = $middleware['options'];

        $payload = $request->getBody()->getContents();
        $this->assertJson($payload, 'Payload is not JSON');

        $payload = json_decode($payload);

        // Keys are sent
        $this->assertObjectHasAttribute('serviceid', $payload, 'API Request is missing required data');
        $this->assertObjectHasAttribute('fromid', $payload, 'API Request is missing required data');
        $this->assertObjectHasAttribute('phoneno', $payload, 'API Request is missing required data');
        $this->assertObjectHasAttribute('txt', $payload, 'API Request is missing required data');
        $this->assertObjectHasAttribute('price', $payload, 'API Request is missing required data');
        $this->assertObjectHasAttribute('unicode', $payload, 'API Request is missing required data');

        // Data are mapped correctly
        dump($payload->serviceid);
        $this->assertEquals(config('front-sms.serviceId'), $payload->serviceid);
        $this->assertEquals($sms->from, $payload->fromid);
        $this->assertEquals($sms->to, $payload->phoneno);
        $this->assertEquals($sms->message, $payload->txt);
        $this->assertEquals($sms->price, $payload->price);
        $this->assertEquals($sms->isUnicode(), $payload->unicode);

        $this->assertEquals($origId, $sms->fresh()->origid);
    }

    /** @test */
    public function it_throws_invalid_api_request_exception_if_error_code_is_greater_than_0()
    {
        $this->expectException(InvalidApiRequest::class);
        $this->expectExceptionMessageRegExp('/Something happened/');

        $sms = factory(FrontMessage::class)->create();

        $this->pushMessageAndReturnMiddleware($sms, ['errorcode' => 1, 'id' => 0, 'description' => 'Something happened']);
    }

    /** @test */
    public function it_defaults_to_not_using_unicode()
    {
        $sms = factory(FrontMessage::class)->create(['message' => 'Not a unicode message']);
        $this->assertFalse($sms->isUnicode());

        $middleware = $this->pushMessageAndReturnMiddleware($sms);
        $payload = $this->getPayload($middleware);
        $this->assertEquals(false, $payload->unicode, 'Unicode field was not set properly');
    }

    /** @test */
    public function it_sets_unicode_field_to_true_if_message_is_unicode()
    {
        $sms = factory(FrontMessage::class)->create(['message' => 'Unicode Message 💪']);
        $this->assertTrue($sms->isUnicode());

        $middleware = $this->pushMessageAndReturnMiddleware($sms);
        $payload = $this->getPayload($middleware);

        $this->assertEquals(true, $payload->unicode, 'Unicode field was not set properly');
    }

    protected function pushMessageAndReturnMiddleware(FrontMessage $sms, $response = null)
    {
        if (! $response) {
            $response = ['errorcode' => 0, 'id' => 1, 'description' => 'OK'];
        }

        $mock = new MockHandler([
            new Response(200, [], json_encode($response))
        ]);

        $container = [];
        $history = Middleware::history($container);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        $client = new Client(['handler' => $handlerStack]);

        (new FrontClient($client))->push($sms);

        return $container[0];
    }

    private function getPayload($middleware)
    {
        return json_decode($middleware['request']->getBody()->getContents());
    }
}
