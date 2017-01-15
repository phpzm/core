<?php

namespace Simples\Core\Http;

use Simples\Core\Route\Match;

/**
 * @method Response answerContinue($content = null, $meta = []) // 100
 * @method Response answerSwitching Protocols($content = null, $meta = []) // 101
 * @method Response answerProcessing($content = null, $meta = []) // 102
 *
 * @method Response answerOK($content = null, $meta = []) // 200
 * @method Response answerCreated($content = null, $meta = []) // 201
 * @method Response answerAccepted($content = null, $meta = []) //  202
 * @method Response answerNonAuthoritativeInformation($content = null, $meta = []) // 203
 * @method Response answerNoContent($content = null, $meta = []) // 204
 * @method Response answerResetContent($content = null, $meta = []) // 205
 * @method Response answerPartialContent($content = null, $meta = []) // 206
 * @method Response answerMultiStatus($content = null, $meta = []) // 207
 * @method Response answerAlreadyReported($content = null, $meta = []) // 208
 *
 * @method Response answerMultiple Choices($content = null, $meta = []) // 300
 * @method Response answerMoved Permanently($content = null, $meta = []) // 301
 * @method Response answerFound($content = null, $meta = []) // 302
 * @method Response answerSeeOther($content = null, $meta = []) // 303
 * @method Response answerNotModified($content = null, $meta = []) // 304
 * @method Response answerUseProxy($content = null, $meta = []) // 305
 * @method Response answerTemporaryRedirect($content = null, $meta = []) // 307
 *
 * @method Response answerBadRequest($content = null, $meta = []) // 400
 * @method Response answerUnauthorized($content = null, $meta = []) // 401
 * @method Response answerPaymentRequired($content = null, $meta = []) // 402
 * @method Response answerForbidden($content = null, $meta = []) // 403
 * @method Response answerNotFound($content = null, $meta = []) // 404
 * @method Response answerMethodNotAllowed($content = null, $meta = []) // 405
 * @method Response answerNotAcceptable($content = null, $meta = []) // 406
 * @method Response answerProxyAuthenticationRequired($content = null, $meta = []) // 407
 * @method Response answerRequestTimeout($content = null, $meta = []) // 408
 * @method Response answerConflict($content = null, $meta = []) // 409
 * @method Response answerGone($content = null, $meta = []) // 410
 * @method Response answerLengthRequired($content = null, $meta = []) // 411
 * @method Response answerPreconditionFailed($content = null, $meta = []) // 412
 * @method Response answerRequestEntityTooLarge($content = null, $meta = []) // 413
 * @method Response answerRequestURITooLarge($content = null, $meta = []) // 414
 * @method Response answerUnsupportedMediaType($content = null, $meta = []) // 415
 * @method Response answerRequestedRangeNotSatisfiable($content = null, $meta = []) // 416
 * @method Response answerExpectationFailed($content = null, $meta = []) // 417
 * @method Response answerIAmATeapot($content = null, $meta = []) // 418
 * @method Response answerNotProcessableEntity($content = null, $meta = []) // 422
 * @method Response answerLocked($content = null, $meta = []) // 423
 * @method Response answerFailedDependency($content = null, $meta = []) // 424
 * @method Response answerUnorderedCollection($content = null, $meta = []) // 425
 * @method Response answerUpgradeRequired($content = null, $meta = []) // 426
 * @method Response answerPreconditionRequired($content = null, $meta = []) // 428
 * @method Response answerTooManyRequests($content = null, $meta = []) // 429
 * @method Response answerRequestHeaderFieldsTooLarge($content = null, $meta = []) // 431
 *
 * @method Response answerInternalServerErro($content = null, $meta = []) // 500
 * @method Response answerNotImplemented($content = null, $meta = []) // 501
 * @method Response answerBadGateway($content = null, $meta = []) // 502
 * @method Response answerServiceUnavailable($content = null, $meta = []) // 503
 * @method Response answerGatewayTimeout($content = null, $meta = []) // 504
 * @method Response answerHTTPVersionNotSupported($content = null, $meta = []) // 505
 * @method Response answerVariantAlsoNegotiates($content = null, $meta = []) // 506
 * @method Response answerInsufficientStorage($content = null, $meta = []) // 507
 * @method Response answerLoopDetected($content = null, $meta = []) // 508
 * @method Response answerNetworkAuthenticationRequired($content = null, $meta = []) // 511
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
     * @return Request
     */
    protected final function request()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    protected final function response()
    {
        return $this->response;
    }

    /**
     * @param null $content
     * @param array $meta
     * @param int $code
     * @return Response
     */
    protected abstract function answer($content = null, $meta = [], $code = 200) : Response;

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