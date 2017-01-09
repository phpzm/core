<?php

namespace Simples\Core\Http\Specialty;

use Simples\Core\Http\Controller;
use Simples\Core\Http\Response;
use Simples\Core\Http\ResponseStream;

/**
 * Class ApiController
 * @package Simples\Core\Http\Specialty
 */
class ApiController extends Controller
{
    /** @noinspection PhpMissingParentCallCommonInspection */
    /**
     * @param null $content
     * @param array $meta
     * @param int $code
     * @return Response
     */
    protected function answer($content = null, $meta = [], $code = 200)
    {
        $json = [
            'content' => $content,
            'meta' => $meta,
            'status' => $this->parseStatus($code),
        ];
        return $this
            ->response()
            ->json($json)
            ->withStatus($code);
    }

    /**
     * @param $statusCode
     * @return array
     */
    private function parseStatus($statusCode)
    {
        $statusCodes = ResponseStream::HTTP_STATUS_CODE;

        $statusType = 'unknown';
        $statusCode = (string)$statusCode;
        $startsWith = $statusCode{0};
        switch ($startsWith) {
            case '1':
                $statusType = 'information';
                break;
            case '2':
                $statusType = 'success';
                break;
            case '3':
                $statusType = 'redirect';
                break;
            case '4':
                $statusType = 'client-error';
                break;
            case '5':
                $statusType = 'server-error';
                break;
        }
        $status = [
            'code' => $statusCode,
            'phrase' => $statusCodes[$statusCode],
            'type' => $statusType
        ];

        return $status;
    }

}