<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\Log;
use Mockery;
use RolfHaug\FrontSms\Exceptions\Front\InvalidApiRequest;
use RolfHaug\FrontSms\FrontClient;
use RolfHaug\FrontSms\FrontMessage;
use Tests\TestCase;

class FrontClientTest extends TestCase
{
    /** @test */
    public function it_sends_sms_to_correct_url_with_correct_payload_and_updates_the_message_orig_id()
    {
        $sms = FrontMessage::factory()->create(['price' => 100]);
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
        $this->assertObjectHasProperty('serviceid', $payload, 'API Request is missing required data');
        $this->assertObjectHasProperty('fromid', $payload, 'API Request is missing required data');
        $this->assertObjectHasProperty('phoneno', $payload, 'API Request is missing required data');
        $this->assertObjectHasProperty('txt', $payload, 'API Request is missing required data');
        $this->assertObjectHasProperty('price', $payload, 'API Request is missing required data');
        $this->assertObjectHasProperty('unicode', $payload, 'API Request is missing required data');
        $this->assertObjectNotHasProperty('password', $payload, 'API Request included password when it shoudl not');

        // Data are mapped correctly
        $this->assertEquals(config('front-sms.serviceId'), $payload->serviceid);
        $this->assertEquals($sms->from, $payload->fromid);
        $this->assertEquals($sms->to, $payload->phoneno);
        $this->assertEquals($sms->message, $payload->txt);
        $this->assertEquals($sms->price, $payload->price);
        $this->assertEquals($sms->isUnicode(), $payload->unicode);

        $this->assertEquals($origId, $sms->fresh()->origid);
    }

    /** @test */
    public function it_includes_password_in_api_request_when_given()
    {
        $this->app['config']->set('front-sms.password', 'secret');

        $sms = FrontMessage::factory()->create(['price' => 100]);
        $origId = 1234;

        $middleware = $this->pushMessageAndReturnMiddleware($sms, ['errorcode' => 0, 'id' => $origId, 'description' => 'OK']);

        /** @var Request $middleware */
        $request = $middleware['request'];
        $payload = $request->getBody()->getContents();
        $this->assertJson($payload, 'Payload is not JSON');

        $payload = json_decode($payload);

        $this->assertObjectHasProperty('password', $payload, 'API Request is missing required data');
    }

    /** @test */
    public function it_throws_invalid_api_request_exception_if_error_code_is_greater_than_0()
    {
        $this->expectException(InvalidApiRequest::class);
        $this->expectExceptionMessage('Something happened');

        $sms = FrontMessage::factory()->create();

        $this->pushMessageAndReturnMiddleware($sms, ['errorcode' => 1, 'id' => 0, 'description' => 'Something happened']);
    }

    /** @test */
    public function it_defaults_to_not_using_unicode()
    {
        $sms = FrontMessage::factory()->create(['message' => 'Not a unicode message']);
        $this->assertFalse($sms->isUnicode());

        $middleware = $this->pushMessageAndReturnMiddleware($sms);
        $payload = $this->getPayload($middleware);
        $this->assertEquals(false, $payload->unicode, 'Unicode field was not set properly');
    }

/** @test */
public function it_sets_unicode_field_to_true_if_message_is_unicode()
    {
        $sms = FrontMessage::factory()->create(['message' => 'Unicode Message 💪']);
        $this->assertTrue($sms->isUnicode());

        $middleware = $this->pushMessageAndReturnMiddleware($sms);
        $payload = $this->getPayload($middleware);

        $this->assertEquals(true, $payload->unicode, 'Unicode field was not set properly');
    }

    /** @test */
    public function it_writes_message_to_log_if_fake_message_is_enabled()
    {
        $this->app['config']->set('front-sms.fakeMessages', true);
        $this->assertTrue(config('front-sms.fakeMessages'));

        $sms = FrontMessage::factory()->create();


        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $array) use ($sms) {
                $this->assertEquals('Pretending to send SMS', $message);

                $this->assertArrayHasKey('serviceid', $array);
                $this->assertArrayHasKey('fromid', $array);
                $this->assertArrayHasKey('phoneno', $array);
                $this->assertArrayHasKey('txt', $array);
                $this->assertArrayHasKey('price', $array);
                $this->assertArrayHasKey('unicode', $array);

                $this->assertEquals($sms->from, $array['fromid']);
                $this->assertEquals($sms->to, $array['phoneno']);
                $this->assertEquals($sms->message, $array['txt']);
                $this->assertEquals($sms->price, $array['price']);
                $this->assertEquals($sms->isUnicode(), $array['unicode']);

                return true;
            });

        (new FrontClient(new Client()))->push($sms);
    }

    /** @test */
    public function it_has_fake_messages_config_set_to_false_as_default()
    {
        $this->assertFalse(config('front-sms.fakeMessages'));
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
