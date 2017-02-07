<?php

namespace Simples\Core\Http;

use Simples\Core\Persistence\Transaction;
use Simples\Core\Route\Match;
use Simples\Core\Route\Wrapper;

/**
 * @method Response answerContinue($data = null, $meta = []) // 100
 * @method Response answerSwitching Protocols($data = null, $meta = []) // 101
 * @method Response answerProcessing($data = null, $meta = []) // 102
 *
 * @method Response answerOK($data = null, $meta = []) // 200
 * @method Response answerCreated($data = null, $meta = []) // 201
 * @method Response answerAccepted($data = null, $meta = []) //  202
 * @method Response answerNonAuthoritativeInformation($data = null, $meta = []) // 203
 * @method Response answerNoContent($data = null, $meta = []) // 204
 * @method Response answerResetContent($data = null, $meta = []) // 205
 * @method Response answerPartialContent($data = null, $meta = []) // 206
 * @method Response answerMultiStatus($data = null, $meta = []) // 207
 * @method Response answerAlreadyReported($data = null, $meta = []) // 208
 *
 * @method Response answerMultiple Choices($data = null, $meta = []) // 300
 * @method Response answerMoved Permanently($data = null, $meta = []) // 301
 * @method Response answerFound($data = null, $meta = []) // 302
 * @method Response answerSeeOther($data = null, $meta = []) // 303
 * @method Response answerNotModified($data = null, $meta = []) // 304
 * @method Response answerUseProxy($data = null, $meta = []) // 305
 * @method Response answerTemporaryRedirect($data = null, $meta = []) // 307
 *
 * @method Response answerBadRequest($data = null, $meta = []) // 400
 * @method Response answerUnauthorized($data = null, $meta = []) // 401
 * @method Response answerPaymentRequired($data = null, $meta = []) // 402
 * @method Response answerForbidden($data = null, $meta = []) // 403
 * @method Response answerNotFound($data = null, $meta = []) // 404
 * @method Response answerMethodNotAllowed($data = null, $meta = []) // 405
 * @method Response answerNotAcceptable($data = null, $meta = []) // 406
 * @method Response answerProxyAuthenticationRequired($data = null, $meta = []) // 407
 * @method Response answerRequestTimeout($data = null, $meta = []) // 408
 * @method Response answerConflict($data = null, $meta = []) // 409
 * @method Response answerGone($data = null, $meta = []) // 410
 * @method Response answerLengthRequired($data = null, $meta = []) // 411
 * @method Response answerPreconditionFailed($data = null, $meta = []) // 412
 * @method Response answerRequestEntityTooLarge($data = null, $meta = []) // 413
 * @method Response answerRequestURITooLarge($data = null, $meta = []) // 414
 * @method Response answerUnsupportedMediaType($data = null, $meta = []) // 415
 * @method Response answerRequestedRangeNotSatisfiable($data = null, $meta = []) // 416
 * @method Response answerExpectationFailed($data = null, $meta = []) // 417
 * @method Response answerIAmATeapot($data = null, $meta = []) // 418
 * @method Response answerNotProcessableEntity($data = null, $meta = []) // 422
 * @method Response answerLocked($data = null, $meta = []) // 423
 * @method Response answerFailedDependency($data = null, $meta = []) // 424
 * @method Response answerUnorderedCollection($data = null, $meta = []) // 425
 * @method Response answerUpgradeRequired($data = null, $meta = []) // 426
 * @method Response answerPreconditionRequired($data = null, $meta = []) // 428
 * @method Response answerTooManyRequests($data = null, $meta = []) // 429
 * @method Response answerRequestHeaderFieldsTooLarge($data = null, $meta = []) // 431
 *
 * @method Response answerInternalServerErro($data = null, $meta = []) // 500
 * @method Response answerNotImplemented($data = null, $meta = []) // 501
 * @method Response answerBadGateway($data = null, $meta = []) // 502
 * @method Response answerServiceUnavailable($data = null, $meta = []) // 503
 * @method Response answerGatewayTimeout($data = null, $meta = []) // 504
 * @method Response answerHTTPVersionNotSupported($data = null, $meta = []) // 505
 * @method Response answerVariantAlsoNegotiates($data = null, $meta = []) // 506
 * @method Response answerInsufficientStorage($data = null, $meta = []) // 507
 * @method Response answerLoopDetected($data = null, $meta = []) // 508
 * @method Response answerNetworkAuthenticationRequired($data = null, $meta = []) // 511
 *
 * Class Controller
 * @package Simples\Core\Http
 */
abstract class Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var Match
     */
    private $match;

    /**
     * @param Request $request
     * @param Response $response
     * @param Match $match
     * @return $this
     */
    public function __invoke(Request $request, Response $response, Match $match)
    {
        $this->request = $request;
        $this->response = $response;
        $this->match = $match;

        return $this;
    }

    /**
     * @param null $content
     * @param array $meta
     * @param int $code
     * @return Response
     */
    abstract protected function answer($content = null, $meta = [], $code = 200) : Response;

    /**
     * @return Request
     */
    final protected function request()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    final protected function response()
    {
        return $this->response;
    }

    /**
     * @return Match
     */
    public function match()
    {
        return $this->match;
    }

    /**
     * @param $name
     * @param $type
     * @return mixed
     */
    public function input($name, $type = null)
    {
        $input = $this->request->getInput($name);
        if (!$input or !$type) {
            return $input;
        }
        return $input->filter($type);
    }

    /**
     * @param $logging
     */
    public function setLog($logging)
    {
        Transaction::log($logging && env('TEST_MODE'));
    }

    /**
     * @param $name
     * @param $arguments
     * @return Response
     */
    public function __call($name, $arguments)
    {
        $httpStatusCodes = ResponseStream::HTTP_STATUS_CODE;

        $content = off($arguments, 0, '');
        $meta = off($arguments, 1, []);
        $code = 501;

        if (substr($name, 0, 6) === 'answer') {
            $reasonPhrase = substr($name, 6);
            foreach ($httpStatusCodes as $statusCode => $statusReasonPhrase) {
                if ($reasonPhrase === str_replace([' ', '-'], '', $statusReasonPhrase)) {
                    $code = $statusCode;
                    break;
                }
            }
        }

        return $this->answer($content, $meta, $code);
    }
}
