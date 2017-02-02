<?php

namespace Simples\Core\Http\Specialty;

use Simples\Core\Data\Record;
use Simples\Core\Http\Controller;
use Simples\Core\Http\Response;
use Simples\Core\Model\Action;
use Simples\Core\Model\Repository\ApiRepository;

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
     * @return Response
     * @throws \Exception
     */
    public function post()
    {
        $this->repository->setLog($this->request()->get('log'));

        $fields = $this->repository->getFields(Action::CREATE);

        $data = [];
        foreach ($fields as $name => $field) {
            $value = $this->input($name, $field['type']);
            if (!is_null($value)) {
                $data[$name] = $value;
            }
        }

        $posted = $this->repository->post(new Record($data));

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
    public function get($id = null)
    {
        $this->repository->setLog($this->request()->get('log'));

        $data = [];
        $start = null;
        $end = null;
        if ($id) {
            $data = [$this->repository->getHashKey() => $id];
        } else {
            $page = (int)$this->request()->get('page');
            $size = (int)$this->request()->get('size');
            $start = ($page - 1) * $size;
            $end = $size;
            $fields = $this->repository->getFields(Action::READ);
            foreach ($fields as $name => $field) {
                $value = $this->input($name, $field['type']);
                if (!is_null($value)) {
                    $data[$name] = $value;
                }
            }
        }
        $collection = $this->repository->get(new Record($data), $start, $end);

        return $this->answerOK($collection->getRecords(), (isset($page)) ? ['page' => $page, 'size' => $collection->size() ] : null);
    }

    /**
     * @return Response
     * @throws \Exception
     */
    public function put($id)
    {
        $this->repository->setLog($this->request()->get('log'));

        $fields = $this->repository->getFields(Action::UPDATE);

        $data = [
            $this->repository->getHashKey() => $id
        ];
        foreach ($fields as $name => $field) {
            $value = $this->input($name, $field['type']);
            if (!is_null($value)) {
                $data[$name] = $value;
            }
        }

        $posted = $this->repository->put(new Record($data));

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
     * @throws \Exception
     */
    public function delete($id)
    {
        $this->repository->setLog($this->request()->get('log'));

        $data = [
            $this->repository->getHashKey() => $id
        ];

        $deleted = $this->repository->delete(new Record($data));

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
