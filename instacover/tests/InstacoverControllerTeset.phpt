<?php

declare(strict_types=1);

use Tester\Assert;
use Tester\TestCase;
use Core\Instacover\Controller\InstacoverController;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

require __DIR__ . '/../vendor/autoload.php';

class InstacoverControllerTest extends TestCase
{
    private InstacoverController $controller;
    private $mockClient;

    protected function setUp(): void
    {
        // Create a simple stub of Guzzle Client with callbacks
        $this->mockClient = new class {
            private $queue = [];

            public function enqueueResponse($responseOrException): void
            {
                $this->queue[] = $responseOrException;
            }

            public function post($uri, $options = [])
            {
                if (empty($this->queue)) {
                    throw new RuntimeException("No mock response in queue for $uri");
                }

                $item = array_shift($this->queue);
                if ($item instanceof \Throwable) {
                    throw $item;
                }
                return $item;
            }
        };

        $this->controller = new InstacoverController(
            'test_table',
            __DIR__ . '/tmp',
            'fake-client-id',
            'fake-client-secret',
            'http://fake-api.local',
            'http://fake-callback.local',
            'salt123'
        );

        // Replace real client with mock
        $ref = new ReflectionClass($this->controller);
        $prop = $ref->getProperty('client');
        $prop->setAccessible(true);
        $prop->setValue($this->controller, $this->mockClient);
    }

    public function testGetAccessTokenSuccess(): void
    {
        $fakeToken = 'FAKE_TOKEN';
        $this->mockClient->enqueueResponse(
            new Response(200, [], json_encode(['access_token' => $fakeToken]))
        );

        $ref = new ReflectionClass($this->controller);
        $method = $ref->getMethod('getAccessToken');
        $method->setAccessible(true);

        $token = $method->invoke($this->controller);

        Assert::same($fakeToken, $token);
    }

    public function testGetAccessTokenFailure(): void
    {
        $this->mockClient->enqueueResponse(new Exception("Connection error"));

        $ref = new ReflectionClass($this->controller);
        $method = $ref->getMethod('getAccessToken');
        $method->setAccessible(true);

        $token = $method->invoke($this->controller);

        Assert::null($token);
    }

    public function testGetSessionMissingId(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $input = json_encode([]); // missing id
        $this->setPhpInput($input);

        ob_start();
        $this->controller->getSession();
        $output = ob_get_clean();

        $decoded = json_decode($output, true);

        Assert::same(400, http_response_code());
        Assert::same(['error' => 'Missing required POST parameter: id'], $decoded);
    }

    public function testGetSessionValid(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $input = json_encode(['id' => 123]);
        $this->setPhpInput($input);

        // Mock responses: first token, then session
        $this->mockClient->enqueueResponse(
            new Response(200, [], json_encode(['access_token' => 'FAKE_TOKEN']))
        );
        $this->mockClient->enqueueResponse(
            new Response(200, [], json_encode([
                'sessionId' => 'SESSION123',
                'link' => 'http://fake.link'
            ]))
        );

        ob_start();
        $this->controller->getSession();
        $output = ob_get_clean();

        $decoded = json_decode($output, true);

        Assert::same(200, http_response_code());
        Assert::same('OK', $decoded['status']);
        Assert::same('SESSION123', $decoded['session']);
        Assert::same('http://fake.link', $decoded['url']);
    }

    /**
     * Fake php://input
     */
    private function setPhpInput(string $data): void
    {
        $tmp = tmpfile();
        fwrite($tmp, $data);
        fseek($tmp, 0);

        stream_wrapper_unregister("php");
        stream_wrapper_register("php", get_class(new class($tmp) {
            private $tmp;
            public function __construct($tmp) { $this->tmp = $tmp; }
            public function stream_open() { return true; }
            public function stream_read($count) { return fread($this->tmp, $count); }
            public function stream_eof() { return feof($this->tmp); }
            public function stream_stat() { return []; }
        }));
    }
}

(new InstacoverControllerTest())->run();
