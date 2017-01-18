<?php

namespace Simples\Core\Http\Specialty;

use Simples\Core\Data\Record;
use Simples\Core\Data\Collection;
use Simples\Core\Data\Validator;
use Simples\Core\Message\Lang;
use Simples\Core\Model\Action;
use Simples\Core\Model\Repository\ApiRepository;
use Simples\Core\Http\Controller;
use Simples\Core\Http\Response;
use Simples\Core\Route\Wrapper;

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
     * ApiController constructor.
     * @param $repository
     */
    public function __construct($repository)
    {
        $this->repository = $repository;
    }

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
     * @return Response
     * @throws \Exception
     */
    public function post()
    {
        $this->repository->setLog($this->request()->get('log') && env('TEST_MODE'));

        $fields = $this->repository->getFields(Action::CREATE);

        $data = [];
        foreach ($fields as $name => $field) {
            $data[$name] = $this->input($name, $field['type']);
        }

        $posted = $this->repository->post(new Record($data));

        $errors = $this->repository->getErrors()->all();
        if (count($errors)) {
            return $this->answerBadRequest('', $errors);
        }

        if ($posted->isEmpty()) {
            throw new \Exception(Lang::auth('error', ['action' => implode('->', [__CLASS__, __METHOD__])]));
        }

        return $this->answerOK($posted->all());
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
