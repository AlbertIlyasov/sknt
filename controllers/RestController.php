<?php

namespace app\controllers;

class RestController extends AbstractController
{
    public function __construct()
    {
        Header('Content-type: application/json');
    }

    protected function response(bool $isSuccess, array $data): void
    {
        echo json_encode([
            'result' => $isSuccess ? 'ok' : 'error',
        ] + $data);
    }

    public function success(array $data = []): void
    {
        $this->response(true, $data);
    }

    public function error(array $data = []): void
    {
        $this->response(false, $data);
    }
}
