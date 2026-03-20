<?php

declare(strict_types=1);

namespace DoclerLabs\CodeceptionSlimModule\Test\Unit\Lib\Connector;

use Codeception\Test\Unit;
use DoclerLabs\CodeceptionSlimModule\Lib\Connector\SlimPsr7;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\App;
use Slim\Psr7\Response;
use Slim\Psr7\UploadedFile;
use Symfony\Component\BrowserKit\Request as BrowserKitRequest;

class SlimPsr7Test extends Unit
{
    private SlimPsr7 $connector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connector = new SlimPsr7();
    }

    public function testConvertToHeadersStripsHttpPrefix(): void
    {
        $request = $this->doRequestCapturingSlimRequest(
            new BrowserKitRequest('http://localhost/test', 'GET', [], [], [], ['HTTP_ACCEPT' => 'text/html'])
        );

        $this->assertSame(['text/html'], $request->getHeader('Accept'));
    }

    public function testConvertToHeadersReplacesUnderscoresWithDashes(): void
    {
        $request = $this->doRequestCapturingSlimRequest(
            new BrowserKitRequest('http://localhost/test', 'GET', [], [], [], ['HTTP_ACCEPT_LANGUAGE' => 'en-US'])
        );

        $this->assertSame(['en-US'], $request->getHeader('Accept-Language'));
    }

    public function testConvertToHeadersTransformsCaseCorrectly(): void
    {
        $request = $this->doRequestCapturingSlimRequest(
            new BrowserKitRequest('http://localhost/test', 'GET', [], [], [], ['HTTP_X_CUSTOM_HEADER' => 'value'])
        );

        $this->assertSame(['value'], $request->getHeader('X-Custom-Header'));
    }

    public function testConvertToHeadersDecodesHtmlEntities(): void
    {
        $request = $this->doRequestCapturingSlimRequest(
            new BrowserKitRequest('http://localhost/test', 'GET', [], [], [], ['HTTP_X&AMP;HEADER' => 'value'])
        );

        $this->assertSame(['value'], $request->getHeader('X&header'));
    }

    public function testConvertToHeadersIgnoresNonHttpServerVars(): void
    {
        $request = $this->doRequestCapturingSlimRequest(
            new BrowserKitRequest(
                'http://localhost/test',
                'GET',
                [],
                [],
                [],
                [
                    'SERVER_NAME'      => 'localhost',
                    'REMOTE_ADDR'      => '127.0.0.1',
                    'HTTP_X_FORWARDED' => 'test-value',
                ]
            )
        );

        $this->assertSame(['test-value'], $request->getHeader('X-Forwarded'));
        $this->assertSame([], $request->getHeader('Server-Name'));
        $this->assertSame([], $request->getHeader('Remote-Addr'));
    }

    public function testConvertFilesWithUploadedFileInterface(): void
    {
        $uploadedFile = $this->createMock(UploadedFileInterface::class);

        $request = $this->doRequestCapturingSlimRequest(
            new BrowserKitRequest('http://localhost/test', 'POST', [], ['avatar' => $uploadedFile])
        );

        $this->assertSame($uploadedFile, $request->getUploadedFiles()['avatar']);
    }

    public function testConvertFilesWithArrayDescriptor(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tmpFile, 'content');

        $request = $this->doRequestCapturingSlimRequest(
            new BrowserKitRequest(
                'http://localhost/test',
                'POST',
                [],
                [
                    'document' => [
                        'tmp_name' => $tmpFile,
                        'name'     => 'doc.pdf',
                        'type'     => 'application/pdf',
                        'size'     => 7,
                        'error'    => UPLOAD_ERR_OK,
                    ],
                ]
            )
        );

        $uploadedFiles = $request->getUploadedFiles();
        $this->assertInstanceOf(UploadedFile::class, $uploadedFiles['document']);
        $this->assertSame('doc.pdf', $uploadedFiles['document']->getClientFilename());
        $this->assertSame('application/pdf', $uploadedFiles['document']->getClientMediaType());
        $this->assertSame(UPLOAD_ERR_OK, $uploadedFiles['document']->getError());

        @unlink($tmpFile);
    }

    public function testConvertFilesWithPartialArrayUsesDefaults(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tmpFile, 'content');

        $request = $this->doRequestCapturingSlimRequest(
            new BrowserKitRequest(
                'http://localhost/test',
                'POST',
                [],
                [
                    'file' => [
                        'tmp_name' => $tmpFile,
                        'name'     => 'file.txt',
                    ],
                ]
            )
        );

        $uploadedFiles = $request->getUploadedFiles();
        $this->assertInstanceOf(UploadedFile::class, $uploadedFiles['file']);
        $this->assertSame('file.txt', $uploadedFiles['file']->getClientFilename());
        $this->assertSame(UPLOAD_ERR_OK, $uploadedFiles['file']->getError());

        @unlink($tmpFile);
    }

    public function testConvertFilesSkipsInvalidEntries(): void
    {
        $request = $this->doRequestCapturingSlimRequest(
            new BrowserKitRequest(
                'http://localhost/test',
                'POST',
                [],
                [
                    'invalid_string' => 'not-a-file',
                    'invalid_array'  => ['some' => 'data'],
                ]
            )
        );

        $this->assertEmpty($request->getUploadedFiles());
    }

    public function testConvertFilesWithMultipleFiles(): void
    {
        $tmpFile1 = tempnam(sys_get_temp_dir(), 'test_');
        $tmpFile2 = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tmpFile1, 'a');
        file_put_contents($tmpFile2, 'b');

        $request = $this->doRequestCapturingSlimRequest(
            new BrowserKitRequest(
                'http://localhost/test',
                'POST',
                [],
                [
                    'file1' => [
                        'tmp_name' => $tmpFile1,
                        'name'     => 'one.txt',
                    ],
                    'file2' => [
                        'tmp_name' => $tmpFile2,
                        'name'     => 'two.txt',
                    ],
                ]
            )
        );

        $uploadedFiles = $request->getUploadedFiles();
        $this->assertCount(2, $uploadedFiles);
        $this->assertSame('one.txt', $uploadedFiles['file1']->getClientFilename());
        $this->assertSame('two.txt', $uploadedFiles['file2']->getClientFilename());

        @unlink($tmpFile1);
        @unlink($tmpFile2);
    }

    public function testConvertRequestGetDoesNotPassParametersAsParsedBody(): void
    {
        $request = $this->doRequestCapturingSlimRequest(
            new BrowserKitRequest('http://localhost/test', 'GET', ['foo' => 'bar'])
        );

        $parsedBody = $request->getParsedBody();
        $this->assertNotSame(['foo' => 'bar'], $parsedBody, 'GET parameters should not be in parsed body.');
    }

    public function testConvertRequestPostSetsParsedBody(): void
    {
        $request = $this->doRequestCapturingSlimRequest(
            new BrowserKitRequest('http://localhost/test', 'POST', ['foo' => 'bar'])
        );

        $this->assertSame(['foo' => 'bar'], $request->getParsedBody());
    }

    public function testConvertRequestPreservesMethodAndUri(): void
    {
        $request = $this->doRequestCapturingSlimRequest(
            new BrowserKitRequest('http://localhost/path?key=val', 'PUT')
        );

        $this->assertSame('PUT', $request->getMethod());
        $this->assertSame('http://localhost/path?key=val', (string)$request->getUri());
    }

    public function testConvertRequestPreservesBody(): void
    {
        $request = $this->doRequestCapturingSlimRequest(
            new BrowserKitRequest(
                'http://localhost/test',
                'POST',
                [],
                [],
                [],
                [],
                '{"raw":"json"}'
            )
        );

        $this->assertSame('{"raw":"json"}', (string)$request->getBody());
    }

    private function doRequestCapturingSlimRequest(BrowserKitRequest $browserKitRequest): ServerRequestInterface
    {
        $capturedRequest = null;

        $app = $this->createMock(App::class);
        $app->method('handle')
            ->willReturnCallback(function (ServerRequestInterface $request) use (&$capturedRequest): ResponseInterface {
                $capturedRequest = $request;

                return new Response();
            });

        $this->connector->setApp($app);
        $this->connector->request(
            $browserKitRequest->getMethod(),
            $browserKitRequest->getUri(),
            $browserKitRequest->getParameters(),
            $browserKitRequest->getFiles(),
            $browserKitRequest->getServer(),
            $browserKitRequest->getContent()
        );

        $this->assertNotNull($capturedRequest, 'App::handle() was not called');

        return $capturedRequest;
    }
}
