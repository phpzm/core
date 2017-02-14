<?php

namespace Simples\Core\Http\Specialty;

use Simples\Core\Data\Record;
use Simples\Core\Http\Controller;
use Simples\Core\Http\Response;
use Simples\Core\Model\Field;
use Simples\Core\Model\Repository\ModelRepository;

/**
 * Class ApiController
 * @package Simples\Core\Http\Specialty
 */
abstract class ApiController extends Controller
{
    /**
     * @var ModelRepository
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
     * @return Response
     */
    public function post(): Response
    {
        $this->setLog($this->request()->get('log'));

        $fields = $this->repository->getFields();

        $data = [];
        foreach ($fields as $name => $field) {
            /** @var Field $field */
            $value = $this->input($name, $field->getType());
            if (!is_null($value)) {
                $data[$name] = $value;
            }
        }

        $posted = $this->repository->create(Record::make($data));

        $errors = $this->repository->getErrors()->all();
        if (count($errors)) {
            return $this->answerBadRequest('', $errors);
        }

        if ($posted->isEmpty()) {
            return $this->answerConflict('');
        }

        return $this->answerOK($posted->all());
    }

    /**
     * @param $id
     * @return Response
     */
    public function get($id = null): Response
    {
        $this->setLog($this->request()->get('log'));

        $start = null;
        $end = null;
        $data = [$this->repository->getHashKey() => $id];
        if (!$id) {
            $data = [];
            $page = (int)of($this->request()->get('page'), 1);
            $size = (int)of($this->request()->get('size'), 25);
            $start = ($page - 1) * $size;
            $end = $size;
            $fields = $this->repository->getFields();

            /** @var Field $field */
            foreach ($fields as $name => $field) {
                $value = $this->input($name, $field->getType());
                if (!is_null($value)) {
                    $data[$name] = $value;
                }
            }
        }

        $collection = $this->repository->read(Record::make($data), $start, $end);

        $count = $this->repository->count($data);

        return $this->answerOK($collection->getRecords(), (isset($page)) ? ['total' => $count] : []);
    }

    /**
     * @param $id
     * @return Response
     */
    public function put($id): Response
    {
        $this->setLog($this->request()->get('log'));

        $fields = $this->repository->getFields();

        $data = [
            $this->repository->getHashKey() => $id
        ];
        foreach ($fields as $name => $field) {
            /** @var Field $field */
            $value = $this->input($name, $field->getType());
            if (!is_null($value)) {
                $data[$name] = $value;
            }
        }

        $posted = $this->repository->update(Record::make($data));

        $errors = $this->repository->getErrors()->all();
        if (count($errors)) {
            return $this->answerBadRequest('', $errors);
        }

        if ($posted->isEmpty()) {
            return $this->answerGone('');
        }

        return $this->answerOK($posted->all());
    }

    /**
     * @param $id
     * @return Response
     */
    public function delete($id): Response
    {
        $this->setLog($this->request()->get('log'));

        $data = [
            $this->repository->getHashKey() => $id
        ];

        $deleted = $this->repository->destroy(Record::make($data));

        $errors = $this->repository->getErrors()->all();
        if (count($errors)) {
            return $this->answerBadRequest('', $errors);
        }

        if ($deleted->isEmpty()) {
            return $this->answerGone('');
        }

        return $this->answerOK($deleted->all());
    }
}
