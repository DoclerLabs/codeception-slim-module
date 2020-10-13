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

        $I->sendGET('/hello/John/Doe', ['foo' => 'bar']);

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        // Check http method.
        $I->assertSame('GET', $response['method'], 'Method is not identical.');

        // Check uri.
        $I->assertSame(
            'http://localhost/hello/John/Doe?foo=bar',
            $response['uri'],
            'Uri is not identical.'
        );

        // Check attributes.
        $I->assertSame('John', $response['attributes']['firstname'], 'Firstname attribute is not identical.');
        $I->assertSame('Doe', $response['attributes']['lastname'], 'Lastname attribute is not identical.');

        // Check query params.
        $I->assertSame(['foo' => 'bar'], $response['query_params'], 'Query parameters are not identical.');

        // Check body.
        $I->assertSame('', $response['body'], 'Request body is not identical.');
        $I->assertNull($response['parsed_body'], 'Parsed request body is not identical.');

        // Check server parameters.
        $I->assertSame(
            'server',
            $response['server_params']['custom'],
            'Custom server parameter is not identical.'
        );
        $I->assertSame(
            'header',
            $response['server_params']['HTTP_CUSTOM'],
            'HTTP_CUSTOM server parameter is not identical.'
        );
        $I->assertSame(
            'localhost',
            $response['server_params']['HTTP_HOST'],
            'HTTP_HOST server parameter is not identical.'
        );

        // Check headers.
        $I->assertSame(
            [
                'User-Agent' => ['Symfony BrowserKit'],
                'Custom'     => ['header'],
                'Cookie'     => ['name=value; name2=value2; name3=value3'],
                'Host'       => ['localhost'],
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

        $I->sendPOST('/hello/John/Doe?query=value', ['foo' => 'bar']);

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        // Check http method.
        $I->assertSame('POST', $response['method'], 'Method is not identical.');

        // Check uri.
        $I->assertSame(
            'http://localhost/hello/John/Doe?query=value',
            $response['uri'],
            'Uri is not identical.'
        );

        // Check attributes.
        $I->assertSame('John', $response['attributes']['firstname'], 'Firstname attribute is not identical.');
        $I->assertSame('Doe', $response['attributes']['lastname'], 'Lastname attribute is not identical.');

        // Check query params.
        $I->assertSame(['query' => 'value'], $response['query_params'], 'Query parameters are not identical.');

        // Check body.
        $I->assertSame('foo=bar', $response['body'], 'Request body is not identical.');
        $I->assertSame(['foo' => 'bar'], $response['parsed_body'], 'Parsed request body is not identical.');

        // Check server parameters.
        $I->assertSame(
            'server',
            $response['server_params']['custom'],
            'Custom server parameter is not identical.'
        );
        $I->assertSame(
            'multipart/form-data',
            $response['server_params']['HTTP_CONTENT_TYPE'],
            'HTTP_CONTENT_TYPE server parameter is not identical.'
        );
        $I->assertSame(
            'localhost',
            $response['server_params']['HTTP_HOST'],
            'HTTP_HOST server parameter is not identical.'
        );

        // Check headers.
        $I->assertSame(
            [
                'User-Agent'   => ['Symfony BrowserKit'],
                'Content-Type' => ['multipart/form-data'],
                'Cookie'       => ['name=value; name2=value2; name3=value3'],
                'Host'         => ['localhost'],
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

        $I->sendPOST('/hello/John/Doe?query=value', ['foo' => 'bar']);

        $I->seeResponseCodeIs(200);
        $response = json_decode($I->grabResponse(), true);

        // Check http method.
        $I->assertSame('POST', $response['method'], 'Method is not identical.');

        // Check uri.
        $I->assertSame(
            'http://localhost/hello/John/Doe?query=value',
            $response['uri'],
            'Uri is not identical.'
        );

        // Check attributes.
        $I->assertSame('John', $response['attributes']['firstname'], 'Firstname attribute is not identical.');
        $I->assertSame('Doe', $response['attributes']['lastname'], 'Lastname attribute is not identical.');

        // Check query params.
        $I->assertSame(['query' => 'value'], $response['query_params'], 'Query parameters are not identical.');

        // Check body.
        $I->assertSame('{"foo":"bar"}', $response['body'], 'Request body is not identical.');
        $I->assertSame(['foo' => 'bar'], $response['parsed_body'], 'Parsed request body is not identical.');

        // Check server parameters.
        $I->assertSame(
            'server',
            $response['server_params']['custom'],
            'Custom server parameter is not identical.'
        );
        $I->assertSame(
            'application/json',
            $response['server_params']['HTTP_CONTENT_TYPE'],
            'HTTP_CONTENT_TYPE server parameter is not identical.'
        );
        $I->assertSame(
            'localhost',
            $response['server_params']['HTTP_HOST'],
            'HTTP_HOST server parameter is not identical.'
        );

        // Check headers.
        $I->assertSame(
            [
                'User-Agent'   => ['Symfony BrowserKit'],
                'Content-Type' => ['application/json'],
                'Cookie'       => ['name=value; name2=value2; name3=value3'],
                'Host'         => ['localhost'],
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
}
