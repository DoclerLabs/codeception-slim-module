<?php

declare(strict_types=1);

namespace DoclerLabs\CodeceptionSlimModule\Test\Functional;

use DoclerLabs\CodeceptionSlimModule\Test\FunctionalTester;

class TestAppCest
{
    public function convertGetRequest(FunctionalTester $I): void
    {
        $I->haveHttpHeader('custom', 'header');
        $I->haveHttpHeader('Cookie', 'name=value; name2=value2; name3=value3');
        $I->haveServerParameter('custom', 'server');

        $I->sendGet('/hello/John/Doe', ['foo' => 'bar']);

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        // Check http method.
        $I->assertSame('GET', $response['method'], 'Method is not identical.');

        // Check uri.
        $I->assertSame('http://localhost/hello/John/Doe?foo=bar', $response['uri'], 'Uri is not identical.');

        // Check attributes.
        $I->assertSame('John', $response['attributes']['firstname'], 'Firstname attribute is not identical.');
        $I->assertSame('Doe', $response['attributes']['lastname'], 'Lastname attribute is not identical.');

        // Check query params.
        $I->assertSame(['foo' => 'bar'], $response['query_params'], 'Query parameters are not identical.');

        // Check body.
        $I->assertSame('', $response['body'], 'Request body is not identical.');
        $I->assertNull($response['parsed_body'], 'Parsed request body is not identical.');

        // Check server parameters.
        $I->assertSame('server', $response['server_params']['custom'], 'Custom server parameter is not identical.');
        $I->assertSame('header', $response['server_params']['HTTP_CUSTOM'], 'HTTP_CUSTOM server parameter is not identical.');
        $I->assertSame('localhost', $response['server_params']['HTTP_HOST'], 'HTTP_HOST server parameter is not identical.');

        // Check headers.
        $I->assertSame(
            [
                'User-Agent'       => ['Symfony BrowserKit'],
                'X-Default-Header' => ['default-value'],
                'Custom'           => ['header'],
                'Cookie'           => ['name=value; name2=value2; name3=value3'],
                'Host'             => ['localhost'],
            ],
            $response['headers'],
            'Header parameters are not identical.'
        );

        // Check cookies.
        $I->assertSame(
            [
                'name'  => 'value',
                'name2' => 'value2',
                'name3' => 'value3',
            ],
            $response['cookie_params'],
            'Cookie parameters are not identical.'
        );

        // Check uploaded files.
        $I->assertSame([], $response['uploaded_files'], 'Uploaded file parameters are not identical.');
    }

    public function convertPostMultipartFormDataRequest(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Content-type', 'multipart/form-data');
        $I->haveHttpHeader('Cookie', 'name=value; name2=value2; name3=value3');
        $I->haveServerParameter('custom', 'server');

        $I->sendPost('/hello/John/Doe?query=value', ['foo' => 'bar']);

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        // Check http method.
        $I->assertSame('POST', $response['method'], 'Method is not identical.');

        // Check uri.
        $I->assertSame('http://localhost/hello/John/Doe?query=value', $response['uri'], 'Uri is not identical.');

        // Check attributes.
        $I->assertSame('John', $response['attributes']['firstname'], 'Firstname attribute is not identical.');
        $I->assertSame('Doe', $response['attributes']['lastname'], 'Lastname attribute is not identical.');

        // Check query params.
        $I->assertSame(['query' => 'value'], $response['query_params'], 'Query parameters are not identical.');

        // Check body.
        $I->assertSame('foo=bar', $response['body'], 'Request body is not identical.');
        $I->assertSame(['foo' => 'bar'], $response['parsed_body'], 'Parsed request body is not identical.');

        // Check server parameters.
        $I->assertSame('server', $response['server_params']['custom'], 'Custom server parameter is not identical.');
        $I->assertSame('multipart/form-data', $response['server_params']['HTTP_CONTENT_TYPE'], 'HTTP_CONTENT_TYPE server parameter is not identical.');
        $I->assertSame('localhost', $response['server_params']['HTTP_HOST'], 'HTTP_HOST server parameter is not identical.');

        // Check headers.
        $I->assertSame(
            [
                'User-Agent'       => ['Symfony BrowserKit'],
                'X-Default-Header' => ['default-value'],
                'Content-Type'     => ['multipart/form-data'],
                'Cookie'           => ['name=value; name2=value2; name3=value3'],
                'Host'             => ['localhost'],
            ],
            $response['headers'],
            'Header parameters are not identical.'
        );

        // Check cookies.
        $I->assertSame(
            [
                'name'  => 'value',
                'name2' => 'value2',
                'name3' => 'value3',
            ],
            $response['cookie_params'],
            'Cookie parameters are not identical.'
        );

        // Check uploaded files.
        $I->assertSame([], $response['uploaded_files'], 'Uploaded file parameters are not identical.');
    }

    public function convertPostJsonRequest(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Content-type', 'application/json');
        $I->haveHttpHeader('Cookie', 'name=value; name2=value2; name3=value3');
        $I->haveServerParameter('custom', 'server');

        $I->sendPost('/hello/John/Doe?query=value', ['foo' => 'bar']);

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        // Check http method.
        $I->assertSame('POST', $response['method'], 'Method is not identical.');

        // Check uri.
        $I->assertSame('http://localhost/hello/John/Doe?query=value', $response['uri'], 'Uri is not identical.');

        // Check attributes.
        $I->assertSame('John', $response['attributes']['firstname'], 'Firstname attribute is not identical.');
        $I->assertSame('Doe', $response['attributes']['lastname'], 'Lastname attribute is not identical.');

        // Check query params.
        $I->assertSame(['query' => 'value'], $response['query_params'], 'Query parameters are not identical.');

        // Check body.
        $I->assertSame('{"foo":"bar"}', $response['body'], 'Request body is not identical.');
        $I->assertSame(['foo' => 'bar'], $response['parsed_body'], 'Parsed request body is not identical.');

        // Check server parameters.
        $I->assertSame('server', $response['server_params']['custom'], 'Custom server parameter is not identical.');
        $I->assertSame('application/json', $response['server_params']['HTTP_CONTENT_TYPE'], 'HTTP_CONTENT_TYPE server parameter is not identical.');
        $I->assertSame('localhost', $response['server_params']['HTTP_HOST'], 'HTTP_HOST server parameter is not identical.');

        // Check headers.
        $I->assertSame(
            [
                'User-Agent'       => ['Symfony BrowserKit'],
                'X-Default-Header' => ['default-value'],
                'Content-Type'     => ['application/json'],
                'Cookie'           => ['name=value; name2=value2; name3=value3'],
                'Host'             => ['localhost'],
            ],
            $response['headers'],
            'Header parameters are not identical.'
        );

        // Check cookies.
        $I->assertSame(
            [
                'name'  => 'value',
                'name2' => 'value2',
                'name3' => 'value3',
            ],
            $response['cookie_params'],
            'Cookie parameters are not identical.'
        );

        // Check uploaded files.
        $I->assertSame([], $response['uploaded_files'], 'Uploaded file parameters are not identical.');
    }

    public function convertPutRequest(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Content-type', 'application/json');

        $I->sendPut('/echo', ['foo' => 'bar']);

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        $I->assertSame('PUT', $response['method'], 'Method is not identical.');
        $I->assertSame('{"foo":"bar"}', $response['body'], 'Request body is not identical.');
        $I->assertSame(['foo' => 'bar'], $response['parsed_body'], 'Parsed request body is not identical.');
    }

    public function convertPatchRequest(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Content-type', 'application/json');

        $I->sendPatch('/echo', ['key' => 'value']);

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        $I->assertSame('PATCH', $response['method'], 'Method is not identical.');
        $I->assertSame('{"key":"value"}', $response['body'], 'Request body is not identical.');
        $I->assertSame(['key' => 'value'], $response['parsed_body'], 'Parsed request body is not identical.');
    }

    public function convertDeleteRequest(FunctionalTester $I): void
    {
        $I->sendDelete('/echo');

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        $I->assertSame('DELETE', $response['method'], 'Method is not identical.');
    }

    public function convertPostWithFileUpload(FunctionalTester $I): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_upload_');
        file_put_contents($tmpFile, 'file content here');

        $I->haveHttpHeader('Content-type', 'multipart/form-data');
        $I->sendPost(
            '/upload',
            [],
            [
                'attachment' => [
                    'tmp_name' => $tmpFile,
                    'name'     => 'test.txt',
                    'type'     => 'text/plain',
                    'size'     => filesize($tmpFile),
                    'error'    => UPLOAD_ERR_OK,
                ],
            ]
        );

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        $I->assertSame('POST', $response['method'], 'Method is not identical.');
        $I->assertCount(1, $response['uploaded_files'], 'Expected one uploaded file.');
        $I->assertSame('test.txt', $response['uploaded_files']['attachment']['filename']);
        $I->assertSame('text/plain', $response['uploaded_files']['attachment']['media_type']);
        $I->assertSame('file content here', $response['uploaded_files']['attachment']['content']);
        $I->assertSame(UPLOAD_ERR_OK, $response['uploaded_files']['attachment']['error']);

        @unlink($tmpFile);
    }

    public function handleNon200StatusCode(FunctionalTester $I): void
    {
        $I->sendGet('/status/404');

        $I->seeResponseCodeIs(404);
    }

    public function handleServerErrorStatusCode(FunctionalTester $I): void
    {
        $I->sendGet('/status/500');

        $I->seeResponseCodeIs(500);
    }

    public function handleRedirectResponse(FunctionalTester $I): void
    {
        $I->stopFollowingRedirects();

        $I->sendGet('/redirect');

        $I->seeResponseCodeIs(302);
        $I->seeHttpHeader('Location', '/hello/John/Doe');

        $I->startFollowingRedirects();
    }

    public function handleEmptyResponse(FunctionalTester $I): void
    {
        $I->sendGet('/empty');

        $I->seeResponseCodeIs(204);
        $I->assertSame('', $I->grabResponse(), 'Response body should be empty.');
    }

    public function convertMinimalGetRequest(FunctionalTester $I): void
    {
        $I->sendGet('/echo');

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        $I->assertSame('GET', $response['method'], 'Method is not identical.');
        $I->assertSame('http://localhost/echo', $response['uri'], 'Uri is not identical.');
        $I->assertSame([], $response['query_params'], 'Query parameters should be empty.');
        $I->assertSame([], $response['cookie_params'], 'Cookie parameters should be empty.');
        $I->assertNull($response['parsed_body'], 'Parsed body should be null.');
    }

    public function convertPostWithEmptyBody(FunctionalTester $I): void
    {
        $I->haveHttpHeader('Content-type', 'application/json');

        $I->sendPost('/echo', '');

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        $I->assertSame('POST', $response['method'], 'Method is not identical.');
        $I->assertSame('', $response['body'], 'Request body should be empty.');
    }

    public function configHeadersAreSentWithEveryRequest(FunctionalTester $I): void
    {
        $I->sendGet('/echo');

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        $I->assertSame(
            'default-value',
            $response['headers']['X-Default-Header'][0] ?? null,
            'Default header from config should be present.'
        );
    }
}
