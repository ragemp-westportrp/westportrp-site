<?php

header('Content-Type: application/json; charset=utf-8');

global $qb;
global $user;
global $server;

if (isset($_GET['method'])) {
    if ($_GET['method'] == 'getUserInfo') {
        if (isset($_GET['name'])) {
            $uinf = $user->getUserInfo($_GET['name']);
            $days = $server->getDaysFromTime($uinf['reg_timestamp']);
            $json['name'] = $uinf['name'];
            $json['helper_level'] = $uinf['helper_level'];
            $json['online_day'] = round($uinf['online_cont'] * 8.5 / 60, 2);
            $json['online_all'] = round($uinf['online_time'] * 8.5 / 60, 2);
            $json['online_avg'] = round(($uinf['online_time'] * 8.5 / 60) /  $days, 2);
            $json['is_online'] = $uinf['is_online'];
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
        }
        else {
            $uItem['error'] = 'Params name is empty';
            echo json_encode($uItem, JSON_UNESCAPED_UNICODE);
        }
    }
    else if ($_GET['method'] == 'getVehicleImage') {
        if (isset($_GET['name'])) {
            $json['img_small'] = 'https://dednet.ru/client/images/carssm/' . $_GET['name'] . '_1.jpg';
            $json['img_large'] = 'https://dednet.ru/client/images/cars/' . $_GET['name'] . '_1.jpg';
            echo json_encode($json, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
        }
        else {
            $uItem['error'] = 'Params name is empty';
            echo json_encode($uItem, JSON_UNESCAPED_UNICODE);
        }
    }
    else if ($_GET['method'] == 'getDateTime') {
        $dateTimeSrv = $qb->createQueryBuilder('daynight')->selectSql()->executeQuery()->getSingleResult();
        echo json_encode($dateTimeSrv, JSON_UNESCAPED_UNICODE);
    }
    else if ($_GET['method'] == 'getGhettoList') {
        $dateTimeSrv = $qb->createQueryBuilder('gang_war')->selectSql('id, street, zone, fraction_name')->executeQuery()->getResult();
        echo json_encode($dateTimeSrv, JSON_UNESCAPED_UNICODE);
    }
    else if ($_GET['method'] == 'getAdList') {
        $dateTimeSrv = $qb->createQueryBuilder('rp_inv_ad')->selectSql()->orderBy('id DESC')->limit(1000)->executeQuery()->getResult();
        echo json_encode($dateTimeSrv, JSON_UNESCAPED_UNICODE);
    }
    else if ($_GET['method'] == 'getBanList') {
        if (isset($_GET['ban_from'])) {
            $dateTimeSrv = $qb->createQueryBuilder('ban_list')->selectSql()->orderBy('id DESC')->where('ban_from = \'' . $server->charsString($_GET['ban_from']) . '\'')->executeQuery()->getResult();
            echo json_encode($dateTimeSrv, JSON_UNESCAPED_UNICODE);
        }
        else if (isset($_GET['ban_to'])) {
            $dateTimeSrv = $qb->createQueryBuilder('ban_list')->selectSql()->orderBy('id DESC')->where('ban_to = \'' . $server->charsString($_GET['ban_to']) . '\'')->executeQuery()->getResult();
            echo json_encode($dateTimeSrv, JSON_UNESCAPED_UNICODE);
        }
        else {
            $dateTimeSrv = $qb->createQueryBuilder('ban_list')->selectSql()->orderBy('id DESC')->limit(1000)->executeQuery()->getResult();
            echo json_encode($dateTimeSrv, JSON_UNESCAPED_UNICODE);
        }
    }
    else if ($_GET['method'] == 'getOnline') {
        $idx = 0;
        $list = $qb->createQueryBuilder('users')->selectSql()->where('is_online = 1')->andWhere('admin_level = 0')->orderBy('name ASC')->executeQuery()->getResult();

        $array = [];
        foreach ($list as $item) {
            $idx++;
            $days = $server->getDaysFromTime($item['reg_timestamp']);
            $uItem['idx'] = $idx;
            $uItem['name'] = $item['name'];
            $uItem['helper_level'] = $item['helper_level'];
            $uItem['online_day'] = round($item['online_cont'] * 8.5 / 60, 2);
            $uItem['online_all'] = round($item['online_time'] * 8.5 / 60, 2);
            $uItem['online_avg'] = round(($item['online_time'] * 8.5 / 60) /  $days, 2);

            array_push($array, $uItem);
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }
    else if ($_GET['method'] == 'getVehicles') {

        $settings = $qb->createQueryBuilder('page_settings')->selectSql()->executeQuery()->getSingleResult();
        if ($settings['api_get_vehicle']) {
            $idx = 0;
            $list = $qb->createQueryBuilder('cars')->selectSql()->where('with_delete = 0')->orderBy('name ASC')->executeQuery()->getResult();

            $array = [];
            foreach ($list as $item) {
                $uItem['id'] = $item['id'];
                $uItem['name'] = $item['name'];
                $uItem['price'] = $item['price'];
                $uItem['is_buy'] = $item['user_id'] > 0;
                array_push($array, $uItem);
            }
            echo json_encode($array, JSON_UNESCAPED_UNICODE);
        }
        else {
            $array['message'] = 'Engineering works';
            echo json_encode($array, JSON_UNESCAPED_UNICODE);
        }
    }
    else if ($_GET['method'] == 'getHouses') {
        $idx = 0;
        $list = $qb->createQueryBuilder('houses')->selectSql()->orderBy('address ASC')->executeQuery()->getResult();

        $array = [];
        foreach ($list as $item) {
            $uItem['address'] = $item['address'];
            $uItem['street'] = $item['street'];
            $uItem['number'] = $item['number'];
            $uItem['price'] = $item['price'];
            $uItem['max_roommate'] = $item['max_roommate'];
            $uItem['is_buy'] = $item['user_id'] > 0;
            $uItem['owner_name'] = $item['user_name'];
            array_push($array, $uItem);
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }
    else if ($_GET['method'] == 'getCondos') {
        $idx = 0;
        $list = $qb->createQueryBuilder('condos')->selectSql()->orderBy('address ASC')->executeQuery()->getResult();

        $array = [];
        foreach ($list as $item) {
            $uItem['address'] = $item['address'];
            $uItem['street'] = $item['street'];
            $uItem['number'] = $item['number'];
            $uItem['price'] = $item['price'];
            $uItem['is_buy'] = $item['user_id'] > 0;
            $uItem['owner_name'] = $item['user_name'];
            array_push($array, $uItem);
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }
    else if ($_GET['method'] == 'getStocks') {
        $idx = 0;
        $list = $qb->createQueryBuilder('stocks')->selectSql()->orderBy('address ASC')->executeQuery()->getResult();

        $array = [];
        foreach ($list as $item) {
            $uItem['address'] = $item['address'];
            $uItem['street'] = $item['street'];
            $uItem['number'] = $item['number'];
            $uItem['price'] = $item['price'];
            $uItem['type'] = $item['interior'];
            $uItem['is_buy'] = $item['user_id'] > 0;
            $uItem['owner_name'] = $item['user_name'];
            array_push($array, $uItem);
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }
    else if ($_GET['method'] == 'getYachts') {
        $idx = 0;
        $list = $qb->createQueryBuilder('yachts')->selectSql()->orderBy('name ASC')->executeQuery()->getResult();

        $array = [];
        foreach ($list as $item) {
            $uItem['name'] = $item['name'];
            $uItem['price'] = $item['price'];
            $uItem['is_buy'] = $item['user_id'] > 0;
            $uItem['owner_name'] = $item['user_name'];
            array_push($array, $uItem);
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }
    else if ($_GET['method'] == 'getBusiness') {
        $idx = 0;
        $list = $qb->createQueryBuilder('business')->selectSql()->orderBy('name ASC')->executeQuery()->getResult();

        $array = [];
        foreach ($list as $item) {
            $uItem['name'] = $item['name'];
            $uItem['price'] = $item['price'];
            $uItem['is_buy'] = $item['user_id'] > 0;
            $uItem['owner_name'] = $item['user_name'];
            array_push($array, $uItem);
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }
    else if ($_GET['method'] == 'getCrimeFraction') {
        $idx = 0;
        $list = $qb->createQueryBuilder('fraction_list')->selectSql()->executeQuery()->getResult();

        $array = [];
        foreach ($list as $item) {
            $uItem['id'] = $item['id'];
            $uItem['name'] = $item['name'];
            $uItem['has_leader'] = $item['owner_id'] > 0;
            array_push($array, $uItem);
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }
    else if ($_GET['method'] == 'getUserTopMoney') {
        $idx = 0;
        $list = $qb
            ->createQueryBuilder('users')
            ->selectSql('name, money, money_bank')
            ->orderBy('money + money_bank DESC')
            ->limit(100)
            ->where('login_date > ' . (time() - (60 * 60 * 24 * 30)))
            ->andWhere('admin_level = 0')
            ->executeQuery()
            ->getResult()
        ;
        $array = [];
        foreach ($list as $item) {
            $uItem['name'] = $item['name'];
            $uItem['money'] = $item['money'] + $item['money_bank'];
            array_push($array, $uItem);
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
    }
    else {
        $uItem['error'] = 'Method not found';
        echo json_encode($uItem, JSON_UNESCAPED_UNICODE);
    }
}
else {
    echo "Это публичное API для разработчиков\nЕсли замечу, что кто-то юзает это API чаще чем раз в 30 секунд для своих ботов, буду банить";

    echo "\n\nСписок доступных методов и их использование";
    echo "\nЧтобы использовать метод используйте GET запрос с параметром method=НАЗВАНИЕ МЕТОДА";
    echo "\nПример: https://dednet.ru/publicApi?method=getVehicles";
    echo "\n\nМетод: getVehicles\nПолучает список всех транспортных средств";
    echo "\n\nМетод: getHouses\nПолучает список всех домов";
    echo "\n\nМетод: getCondos\nПолучает список всех квартир";
    echo "\n\nМетод: getYachts\nПолучает список всех яхт";
    echo "\n\nМетод: getStocks\nПолучает список всех складов\nТип склада 0 - Малый, 1 - Средний, 2 - Большой";
    echo "\n\nМетод: getBusiness\nПолучает список всех бизнесов";
    echo "\n\nМетод: getUserTopMoney\nПолучает список всех топ богачей";
    echo "\n\nМетод: getUserInfo\nПолучает информацию о игроке, используйется дополнительный параметр GET запроса name\nПример: https://dednet.ru/publicApi?method=getUserInfo&name=Looney Moretti";
    echo "\n\nМетод: getVehicleImage\nПолучает ссылку на картинку малого и большого размера автомобиля, название автомобиля указать с большой буквы!\nПример: https://dednet.ru/publicApi?method=getVehicleImage&name=Nero2";
    echo "\n\nМетод: getOnline\nПолучает текущий онлайн";
    echo "\n\nМетод: getDateTime\nПолучает текущее время на сервере";
    echo "\n\nМетод: getAdList\nПолучает последние 1000 объявлений";
    echo "\n\nМетод: getGhettoList\nПолучает список всех гетто территорий";
    echo "\n\nМетод: getCrimeFraction\nПолучает список всех криминальных организаций";
    echo "\n\nМетод: getBanList\nПолучает спиоск из последних 1000 записей в бан листе, так же есть поиск\nПоиск по тому, кто забанил: https://dednet.ru/publicApi?method=getBanList&ban_from=Looney Moretti\nПоиск по тому, кто был забанен: https://dednet.ru/publicApi?method=getBanList&ban_to=Looney Moretti";

    /*$uItem['desc'] = 'For example: https://dednet.ru/publicApi?method=getVehicles';
    $uItem['getVehicles'] = 'Get All Vehicles';
    $uItem['getHouses'] = 'Get All Houses';
    $uItem['getBusiness'] = 'Get All Business';
    $uItem['getCondos'] = 'Get All Condos';
    $uItem['getYachts'] = 'Get All Yachts';
    $uItem['getStocks'] = 'Get All Stocks';
    $uItem['getUserTopMoney'] = 'Get user top list';
    $uItem['getUserInfo'] = 'Get user account info | For example: https://dednet.ru/publicApi?method=getVehicles&name=Looney Moretti';
    $uItem['getOnline'] = 'Get current online players';
    $uItem['getDateTime'] = 'Get datetime';
    $uItem['getAdList'] = 'Get lifeinvader ad list';
    $uItem['getGhettoList'] = 'Get ghetto list';
    $uItem['getCrimeFraction'] = 'Get ghetto list';
    $uItem['getBanList'] = 'Get ban list';
    print_r($uItem);*/
}