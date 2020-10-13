<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../../../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->any(
    '/hello/{firstname}/{lastname}',
    static function (ServerRequestInterface $request, ResponseInterface $response) {
        $response->getBody()->write(
            json_encode(
                [
                    'method'         => $request->getMethod(),
                    'uri'            => (string)$request->getUri(),
                    'attributes'     => $request->getAttributes(),
                    'query_params'   => $request->getQueryParams(),
                    'body'           => (string)$request->getBody(),
                    'parsed_body'    => $request->getParsedBody(),
                    'server_params'  => $request->getServerParams(),
                    'headers'        => $request->getHeaders(),
                    'cookie_params'  => $request->getCookieParams(),
                    'uploaded_files' => array_map(
                        static function (UploadedFileInterface $uploadedFile) {
                            return [
                                'filename'   => $uploadedFile->getClientFilename(),
                                'media_type' => $uploadedFile->getClientMediaType(),
                                'size'       => $uploadedFile->getSize(),
                                'error'      => $uploadedFile->getError(),
                                'content'    => (string)$uploadedFile->getStream(),
                            ];
                        },
                        $request->getUploadedFiles()
                    ),
                ]
            )
        );

        return $response;
    }
);

return $app;
