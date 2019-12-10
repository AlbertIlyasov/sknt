<?php

namespace app\controllers;

use app\models\Service;

class ServiceController extends RestController
{
    public function tariffs(int $userId, int $serviceId)
    {
        $service = new Service;
        $tariffs = $service->getTariffs($userId, $serviceId);

        if (!$tariffs) {
            return $this->error();
        }
        return $this->success(['tarifs' => $tariffs]);
    }

    public function tariff(int $userId, int $serviceId)
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $tariffId = $data['tarif_id'] ?? null;
        if (!is_numeric($tariffId)) {
            return $this->error();
        }

        $service = new Service;
        $result = $service->setPayday($userId, $serviceId, $tariffId);

        if (!$result) {
            return $this->error();
        }
        return $this->success();
    }
}
