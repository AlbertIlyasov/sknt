<?php

namespace app\models;

use app\Db;

class Service
{
    public function getTariffs(int $userId, int $serviceId): array
    {
        $query = 'SELECT tarif_id FROM services WHERE ID = ? and user_id = ?';
        $data = Db::fetchAll($query, [$serviceId, $userId]);
        if (!$data) {
            return [];
        }
        $tariffId = $data[0]['tarif_id'];

        $query = 'SELECT * FROM tarifs WHERE tarif_group_id = ?';
        $data = Db::fetchAll($query, [$tariffId]);
        if (!$data) {
            return [];
        }

        $tariffs = [
            'title'  => null,
            'link'   => null,
            'speed'  => null,
            'tarifs' => [],
        ];
        foreach ($data as $tariff) {
            if ($tariffId == $tariff['ID']) {
                $tariffs['title'] = $tariff['title'];
                $tariffs['link']  = $tariff['link'];
                $tariffs['speed'] = (float) $tariff['speed'];
            }

            $now = time();
            $newPayday = strtotime(sprintf(
                '%d-%d-%d + %d months',
                date('Y', $now),
                date('m', $now),
                date('d', $now),
                $tariff['pay_period']
            )) . '+0300';
            $tariffs['tarifs'][] = [
                'ID'         => (int) $tariff['ID'],
                'title'      => $tariff['title'],
                'price'      => (float) $tariff['price'],
                'pay_period' => $tariff['pay_period'],
                'new_payday' => $newPayday,
                'speed'      => (float) $tariff['speed'],
            ];
        }

        return $tariffs;
    }

    public function setPayday(int $userId, int $serviceId, int $tariffId): bool
    {
        $payday = date('Y-m-d');
        $query = 'UPDATE services SET payday = ? WHERE ID = ? and user_id = ? and tarif_id = ?';
        Db::rowCount($query, [$payday, $serviceId, $userId, $tariffId]);

        $query = 'SELECT payday FROM services WHERE ID = ? and user_id = ? and tarif_id = ? and payday = ?';
        return Db::rowCount($query, [$serviceId, $userId, $tariffId, $payday]);
    }
}
