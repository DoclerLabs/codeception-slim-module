<?php

declare(strict_types=1);

namespace DoclerLabs\CodeceptionSlimModule\Lib\Connector;

use Psr\Http\Message\UploadedFileInterface;
use Slim\App;
use Slim\Psr7\Cookies;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request;
use Slim\Psr7\UploadedFile;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Request as BrowserKitRequest;
use Symfony\Component\BrowserKit\Response as BrowserKitResponse;

class SlimPsr7 extends AbstractBrowser
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
        $slimRequest  = $this->convertRequest($request);
        $slimResponse = $this->app->handle($slimRequest);

        return new BrowserKitResponse(
            (string)$slimResponse->getBody(),
            $slimResponse->getStatusCode(),
            $slimResponse->getHeaders()
        );
    }

    private function convertRequest(BrowserKitRequest $request): Request
    {
        $server  = $request->getServer();
        $method  = $request->getMethod();
        $content = (string)$request->getContent();

        $uri           = (new UriFactory())->createUri($request->getUri());
        $headers       = $this->convertToHeaders($server);
        $cookies       = Cookies::parseHeader($headers->getHeader('Cookie', []));
        $body          = (new StreamFactory())->createStream($content);
        $uploadedFiles = $this->convertFiles($request->getFiles());

        $slimRequest = new Request($method, $uri, $headers, $cookies, $server, $body, $uploadedFiles);

        $parsed = [];
        if ($method !== 'GET') {
            $parsed = $request->getParameters();
        }

        // Make sure we do not overwrite a request with a parsed body.
        if (!$slimRequest->getParsedBody()) {
            $slimRequest = $slimRequest->withParsedBody($parsed);
        }

        return $slimRequest;
    }

    /**
     * Collect headers from server variables and transform to proper header names.
     *
     * @param array $serverVariables List of server variables.
     *
     * @return Headers
     */
    private function convertToHeaders(array $serverVariables): Headers
    {
        $headers = [];
        foreach ($serverVariables as $key => $value) {
            // Replace underscores to dashes.
            $headerName = str_replace('_', '-', $key);

            // Transform the first characters to uppercase of each word, other characters are lowercased.
            $headerName = implode('-', array_map('ucfirst', explode('-', strtolower($headerName))));

            // Decode if there are html entities in the header name.
            $headerName = html_entity_decode($headerName, ENT_NOQUOTES);

            // Collect headers from server variables and cut "Http-" prefix.
            if (strpos($headerName, 'Http-') === 0) {
                $headerName = substr($headerName, 5);

                $headers[$headerName] = $value;
            }
        }

        return new Headers($headers, $serverVariables);
    }

    /**
     * Convert uploaded file list to UploadedFile instances.
     *
     * @param array $files List of uploaded file instances, that implements `Psr\Http\Message\UploadedFileInterface`,
     *                     or meta data about uploaded file items from $_FILES, indexed with field name.
     *
     * @return array<string, UploadedFileInterface>
     */
    private function convertFiles(array $files): array
    {
        $uploadedFiles = [];
        foreach ($files as $fieldName => $file) {
            if ($file instanceof UploadedFileInterface) {
                $uploadedFiles[$fieldName] = $file;
            } elseif (!isset($file['tmp_name']) && !isset($file['name'])) {
                $uploadedFiles[$fieldName] = $this->createUploadedFile($file);
            }
        }

        return $uploadedFiles;
    }

    private function createUploadedFile(array $file): UploadedFile
    {
        return new UploadedFile(
            $file['tmp_name'],
            $file['name'],
            $file['type'],
            $file['size'],
            $file['error']
        );
    }
}
