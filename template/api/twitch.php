<?php

header('Content-Type: application/json');

global $qb;
global $maskList;
global $server;

if (isset($_GET['method'])) {
    if ($_GET['method'] == 'allows') {
        $json = ['trenv1x', 'ipanych', 'spl0rs', 'bobsonm'];
        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }
    else if ($_GET['method'] == 'create') {

        $streamer = '';
        if (isset($_GET['streamer']))
            $streamer = strtolower($_GET['streamer']);

        $promo = strtoupper(md5(time() + rand(0, 100000)));
        $json['promo'] = $promo;

        if (rand(0, 100) < 20) {
            $maskId = $server->getRandomMask(0);
            $mask = $maskList[$maskId];
            $json['desc'] = 'выиграл маску ' . $mask[1] . '. Редкость: ' . $server->getRareName($mask[14]);
            $json['desc2'] = 'Промокод: ' . $promo . '. Выигрыш маски ' . $mask[1] . '. Редкость: ' . $server->getRareName($mask[14]);

            $qb
                ->createQueryBuilder('promocode_list')
                ->insertSql(
                    ['code', 'bonus', 'bonus2', 'is_one', 'streamer'],
                    [$promo, 0, '{"mask":' . $maskId . '}', 1, $streamer]
                )
                ->executeQuery(true)
                ->getResult()
            ;
        }
        else if (rand(0, 100) < 10) {
            //vip

            $vipTime = rand(1, 7);

            $json['desc'] = 'выиграл VIP HARD на ' . $vipTime . 'д.';
            $json['desc2'] = 'Промокод: ' . $promo . '. Выигрыш VIP HARD ' . $vipTime . 'д.';

            $qb
                ->createQueryBuilder('promocode_list')
                ->insertSql(
                    ['code', 'bonus', 'bonus2', 'is_one', 'streamer'],
                    [$promo, 0, '{"vip":' . $vipTime . '}', 1, $streamer]
                )
                ->executeQuery(true)
                ->getResult()
            ;
        }
        else {

            $money = 1000;
            if (rand(0, 100) < 1)
                $money = 6000;
            else if (rand(0, 100) < 20)
                $money = 5000;
            else if (rand(0, 100) < 40)
                $money = 4000;
            else if (rand(0, 100) < 60)
                $money = 3000;
            else if (rand(0, 100) < 80)
                $money = 2000;

            $qb
                ->createQueryBuilder('promocode_list')
                ->insertSql(
                    ['code', 'bonus', 'is_one', 'streamer'],
                    [$promo, $money, 1, $streamer]
                )
                ->executeQuery()
                ->getResult()
            ;

            //money
            $json['desc'] = 'выиграл денежную сумму в размере $' . number_format($money);
            $json['desc2'] = 'Промокод: ' . $promo . '. Денежный выигрыш на сумму $' . number_format($money);
        }

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }
    else if ($_GET['method'] == 'network') {

        $promo = strtoupper(md5(time()));
        $json['promo'] = $promo;

        if (isset($_GET['type'])) {
            if ($_GET['type'] == 'money') {

                if (!isset($_GET['count']))
                    return;

                $money = intval($_GET['count']);
                $qb
                    ->createQueryBuilder('promocode_list')
                    ->insertSql(
                        ['code', 'bonus', 'is_one', 'streamer'],
                        [$promo, $money, 1, 'network']
                    )
                    ->executeQuery()
                    ->getResult()
                ;

                //money
                $json['desc'] = 'Промокод: ' . $promo . '. Денежный выигрыш на сумму $' . number_format($money);
            }
            if ($_GET['type'] == 'vip') {

                if (!isset($_GET['count']))
                    return;

                $vipTime = intval($_GET['count']);

                $json['desc'] = 'Промокод: ' . $promo . '. Выигрыш VIP HARD ' . $vipTime . 'д.';

                $qb
                    ->createQueryBuilder('promocode_list')
                    ->insertSql(
                        ['code', 'bonus', 'bonus2', 'is_one', 'streamer'],
                        [$promo, 0, '{"vip":' . $vipTime . '}', 1, 'network']
                    )
                    ->executeQuery(true)
                    ->getResult()
                ;
            }
            if ($_GET['type'] == 'mask') {

                $maskId = $server->getRandomMask(0);
                $mask = $maskList[$maskId];
                $json['desc'] = 'Промокод: ' . $promo . '. Выигрыш маски ' . $mask[1] . '. Редкость: ' . $server->getRareName($mask[14]);

                $qb
                    ->createQueryBuilder('promocode_list')
                    ->insertSql(
                        ['code', 'bonus', 'bonus2', 'is_one', 'streamer'],
                        [$promo, 0, '{"mask":' . $maskId . '}', 1, 'network']
                    )
                    ->executeQuery(true)
                    ->getResult()
                ;
            }
            echo $promo;
        }
        else
            echo 'GGWP';
    }
    else {
        echo 'GGWP';
    }
}
else {
    echo 'GGWP';
}