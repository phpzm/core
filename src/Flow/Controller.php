<?php

namespace Simples\Core\Flow;

use Simples\Core\App;
use Simples\Core\Gateway\Request;
use Simples\Core\Gateway\Response;

/**
 * Class Controller
 * @package Simples\Core\Flow
 */
class Controller
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
     * @var string
     */
    private $view = '';

    /**
     * @var object
     */
    private $route = null;

    /**
     * @param Request $request
     * @param Response $response
     * @param object $route
     */
    public function __construct(Request $request, Response $response, $route = null)
    {
        $this->request = $request;
        $this->response = $response;

        $this->route = $route;
        $this->view = isset($route->uri) ? $route->uri : App::config('app')->views['controller'];
    }

    /**
     * @param array $data
     * @return Response
     */
    public function index(array $data)
    {
        $view = $this->view('/index');

        $data['assign'] = '@index';
        $data['view'] = $view;

        return $this->response()->view($view . '/view.php', $data);
    }

    /**
     * @param array $data
     * @return Response
     */
    public function create($data)
    {
        $view = $this->view('/create');

        $data['assign'] = '@create';
        $data['view'] = $view;

        return $this->response()->view($view . '/view.php', $data);
    }

    /**
     * @param $id
     * @param array $data
     * @return Response
     */
    public function show($id, array $data)
    {
        $view = $this->view('/' . $id);
        
        $data['assign'] = '@show';
        $data['id'] = $id;
        $data['view'] = $view;

        return $this->response()->view($view . '/view.php', $data);
    }

    /**
     * @param $id
     * @param array $data
     * @return Response
     */
    public function edit($id, array $data)
    {
        $view = $this->view('/' . $id . '/edit');

        $data['assign'] = '@edit';
        $data['id'] = $id;
        $data['view'] = $view;

        return $this->response()->view($view . '/view.php', $data);
    }

    /**
     * @param array $data
     * @return Response
     */
    public function store(array $data)
    {
        $view = $this->view('');

        $data['assign'] = '@store';
        $data['input'] = $this->request->all();
        $data['view'] = $view;

        return $this->response()->view($view . '/view.php', $data);
    }

    /**
     * @param $id
     * @param array $data
     * @return Response
     */
    public function update($id, array $data)
    {
        $view = $this->view('/' . $id);

        $data['assign'] = '@update';
        $data['id'] = $id;
        $data['input'] = $this->request->all();
        $data['view'] = $view;

        return $this->response()->view($view . '/view.php',$data);
    }

    /**
     * @param $id
     * @param array $data
     * @return Response
     */
    public function destroy($id, array $data)
    {
        $view = $this->view('/' . $id);

        $data['assign'] = '@destroy';
        $data['id'] = $id;
        $data['view'] = $view;

        return $this->response()->view($view . '/view.php', $data);
    }

    /**
     * @param $relative
     * @return mixed
     */
    private function view($relative)
    {
        return str_replace($relative, '', $this->view);
    }

    /**
     * @return Request
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function response()
    {
        return $this->response;
    }

}