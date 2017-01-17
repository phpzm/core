<?php

namespace Simples\Core\Http;

use Simples\Core\Helper\Json;
use Simples\Core\Kernel\App;
use Simples\Core\Template\View;

/**
 * @method Response atom($data, $code = 200)
 * @method Response css($data, $code = 200)
 * @method Response html($data, $code = 200)
 * @method Response jpeg($data, $code = 200)
 * @method Response json($data, $code = 200)
 * @method Response pdf($data, $code = 200)
 * @method Response rss($data, $code = 200)
 * @method Response plain($data, $code = 200)
 * @method Response xml($data, $code = 200)
 *
 * Class Response
 * @package Simples\Core\Http
 */
class Response extends ResponseStream
{
    /**
     * @var string
     */
    const CONTENT_TYPE_UNKNOWN = 'unknown';
    /**
     * @var string
     */
    const CONTENT_TYPE_ATOM = 'atom';
    /**
     * @var string
     */
    const CONTENT_TYPE_CSS = 'css';
    /**
     * @var string
     */
    const CONTENT_TYPE_HTML = 'html';
    /**
     * @var string
     */
    const CONTENT_TYPE_JPEG = 'jpeg';
    /**
     * @var string
     */
    const CONTENT_TYPE_JSON = 'json';
    /**
     * @var string
     */
    const CONTENT_TYPE_PDF = 'pdf';
    /**
     * @var string
     */
    const CONTENT_TYPE_RSS = 'rss';
    /**
     * @var string
     */
    const CONTENT_TYPE_PLAIN = 'plain';
    /**
     * @var string
     */
    const CONTENT_TYPE_XML = 'xml';
    /**
     * @var string
     */
    const CONTENT_TYPE_API = 'api';

    /**
     * @var array
     */
    const CONTENT_TYPES = [
        'atom' => 'application/atom+xml', 'css' => 'text/css', 'html' => 'text/html; charset=UTF-8', 'jpeg' => 'image/jpeg',
        'json' => 'application/json', 'pdf' => 'application/pdf', 'rss' => 'application/rss+xml; charset=ISO-8859-1',
        'plain' => 'text/plain', 'xml' => 'text/xml'
    ];

    /**
     * Response constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param $name
     * @param string $value
     * @return $this
     */
    public function header($name, $value = '')
    {
        $this->headers[headerify($name)] = $value;

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        $this->write($this->toString(off($arguments, 0, null)));

        $this->header('content-type', off(self::CONTENT_TYPES, $name, self::CONTENT_TYPE_UNKNOWN));

        if (off($arguments, 1)) {
            return $this->withStatus(off($arguments, 1));
        }

        return $this;
    }

    /**
     * @param $template
     * @param $data
     * @return $this
     */
    public function view($template, $data = [])
    {
        $view = new View(path(true, App::config('app')->views['root']));

        $this->write($view->render($template, $data));

        return $this;
    }

    /**
     * @param $data
     * @return string
     */
    public function toString($data)
    {
        if (is_null($data)) {
            return '';
        }
        $scalars = [TYPE_STRING, TYPE_DATE, TYPE_BOOLEAN, TYPE_FLOAT, TYPE_INTEGER];
        if (in_array(gettype($data), $scalars)) {
            return (string)$data;
        }
        return json_encode($data, JSON_NUMERIC_CHECK);
    }

    /**
     * @param $origin
     * @return $this
     */
    public function cors($origin)
    {
        return
            $this
                ->header('Access-Control-Allow-Origin', $origin)
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400');
    }

    /**
     * @param $allowed
     * @return $this
     */
    public function preFlight($allowed)
    {
        return $this
            ->plain('')
            ->header('Access-Control-Allow-Methods', 'GET, POST, DELETE, PUT, OPTIONS')
            ->header('Access-Control-Allow-Headers', $allowed);
    }

    /**
     * https://labs.omniti.com/labs/jsend
     * @param $data
     * @param int $code
     * @param array $meta
     * @return Response
     */
    public function api($data, $code = null, $meta = [])
    {
        $json = [
            'data' => $data,
            'meta' => $meta,
            'status' => $this->parseStatus($code ?? $this->getStatusCode()),
        ];

        return $this->json($json, $code);
    }

    /**
     * @param $property
     * @param $value
     * @return $this
     */
    public function meta($property, $value)
    {
        $contents = $this->getBody()->getContents();
        if (Json::isJson($contents)) {
            $contents = Json::decode($contents);
            if (gettype($contents) === TYPE_OBJECT) {
                if (isset($contents->meta)) {
                    if (gettype($contents->meta) === TYPE_OBJECT) {
                        $contents->meta->$property = $value;
                    }
                    if (gettype($contents->meta) === TYPE_ARRAY) {
                        $contents->meta[$property] = $value;
                    }
                }
                $this->write(Json::encode($contents), true);
            }
        }
        return $this;
    }

    /**
     * @param $statusCode
     * @return array
     */
    private function parseStatus($statusCode)
    {
        $status = [
            'code' => $statusCode,
            'phrase' => off(ResponseStream::HTTP_STATUS_CODE, $statusCode),
            'type' => $this->getStatusType($statusCode)
        ];

        return $status;
    }

    /**
     * @param $statusCode
     * @return string
     */
    public function getStatusType($statusCode)
    {
        $statusType = 'unknown';
        $statusCode = (string)$statusCode;
        $startsWith = $statusCode{0};
        switch ($startsWith) {
            case '1':
                $statusType = 'success';
                break;
            case '2':
                $statusType = 'success';
                break;
            case '3':
                $statusType = 'success';
                break;
            case '4':
                $statusType = 'fail';
                break;
            case '5':
                $statusType = 'error';
                break;
        }
        return $statusType;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->getStatusType($this->getStatusCode()) === 'success';
    }

    /**
     * @return bool
     */
    public function isFail()
    {
        return $this->getStatusType($this->getStatusCode()) === 'fail';
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->getStatusType($this->getStatusCode()) === 'error';
    }
}
