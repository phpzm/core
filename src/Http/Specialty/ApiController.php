<?php

namespace Simples\Core\Http\Specialty;

use Simples\Core\Data\Record;
use Simples\Core\Data\Collection;
use Simples\Core\Data\Repository\ApiRepository;
use Simples\Core\Http\Controller;
use Simples\Core\Http\Response;

/**
 * Class ApiController
 * @package Simples\Core\Http\Specialty
 */
abstract class ApiController extends Controller
{
    /**
     * @var ApiRepository
     */
    protected $repository;

    /**
     * @param null $content
     * @param array $meta
     * @param int $code
     * @return Response
     */
    protected function answer($content = null, $meta = [], $code = 200): Response
    {
        return $this
            ->response()
            ->api($content, $code, $meta);
    }

    /**
     * @param $id
     * @return Collection
     */
    public function get($id)
    {
        return new Collection([]);
    }

    /**
     * @param Record $record
     * @return Record
     */
    public function post(Record $record)
    {
        return $record;
    }

    /**
     * @param Record $record
     * @return Record
     */
    public function put(Record $record)
    {
        return $record;
    }

    /**
     * @param Record $record
     * @return Record
     */
    public function patch(Record $record)
    {
        return $record;
    }

    /**
     * @param Record $record
     * @return Record
     */
    public function delete(Record $record)
    {
        return $record;
    }
}