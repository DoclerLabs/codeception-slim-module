<?php

declare(strict_types=1);

namespace DoclerLabs\CodeceptionSlimModule\Lib\Connector;

use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use Slim\App;
use Slim\Http\Cookies;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Stream;
use Slim\Http\UploadedFile;
use Slim\Http\Uri;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Request as BrowserKitRequest;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;

class Slim extends AbstractBrowser
{
    /** @var App */
    private $app;

    public function setApp(App $app): void
    {
        $this->app = $app;
    }

    /**
     * @param BrowserKitRequest $request An origin request instance.
     *
     * @return BrowserKitResponse An origin response instance.
     */
    protected function doRequest($request): BrowserKitResponse
    {
        $slimRequest = $this->convertRequest($request);

        $stream = fopen('php://temp', 'wb+');
        if ($stream === false) {
            throw new RuntimeException('Could not open `php://temp` stream.');
        }

        $headers      = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
        $body         = new Stream($stream);
        $slimResponse = new Response(200, $headers, $body);
        $slimResponse = $this->app->process($slimRequest, $slimResponse);

        return new BrowserKitResponse(
            (string)$slimResponse->getBody(),
            $slimResponse->getStatusCode(),
            $slimResponse->getHeaders()
        );
    }

    private function convertRequest(BrowserKitRequest $request): Request
    {
        $environment  = Environment::mock($request->getServer());
        $uri          = Uri::createFromString($request->getUri());
        $headers      = Headers::createFromEnvironment($environment);
        $cookieHeader = $headers->get('Cookie', []);
        $cookies      = Cookies::parseHeader($cookieHeader[0] ?? '');

        $slimRequest = Request::createFromEnvironment($environment);
        $slimRequest = $slimRequest
            ->withMethod($request->getMethod())
            ->withUri($uri)
            ->withUploadedFiles($this->convertFiles($request->getFiles()))
            ->withCookieParams($cookies);

        foreach ($headers->keys() as $key) {
            $slimRequest = $slimRequest->withHeader($key, $headers->get($key));
        }

        if ($request->getContent() !== null) {
            $body = new RequestBody();
            $body->write($request->getContent());
            $slimRequest = $slimRequest
                ->withBody($body);
        }

        $parsed = [];
        if ($request->getMethod() !== 'GET') {
            $parsed = $request->getParameters();
        }

        // Make sure we do not overwrite a request with a parsed body.
        if (!$slimRequest->getParsedBody()) {
            $slimRequest = $slimRequest
                ->withParsedBody($parsed);
        }

        return $slimRequest;
    }

    private function convertFiles(array $files): array
    {
        $fileObjects = [];
        foreach ($files as $fieldName => $file) {
            if ($file instanceof UploadedFileInterface) {
                $fileObjects[$fieldName] = $file;
            } elseif (!isset($file['tmp_name']) && !isset($file['name'])) {
                $fileObjects[$fieldName] = $this->convertFiles($file);
            } else {
                $fileObjects[$fieldName] = new UploadedFile(
                    $file['tmp_name'],
                    $file['name'],
                    $file['type'],
                    $file['size'],
                    $file['error']
                );
            }
        }

        return $fileObjects;
    }
}
