<?php

namespace Server;

use Server\Core\EnumConst;
use Server\Core\QueryBuilder;
use Server\Core\Server;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * User
 */
class User
{
    protected $qb;
    protected $valid;
    protected $server;
    protected $accountsResult = [];

    function __construct(QueryBuilder $qb, $check = null, $param = null)
    {
        $this->qb = $qb;
        $this->server = new Server($qb);
    }

    public function logout() {
        setcookie('user', '', time()-3600, '/');
        header( "refresh:0; url=/" );
    }

    public function getUserCookie($name) {
        return $_COOKIE[$name];
    }

    public function isLogin() {
        if (isset($_COOKIE['user'])) {
            global $userInfo;
            if($_COOKIE['user'] == $userInfo['token'])
                return true;
        }
        else if (isset($_GET['login']) && isset($_GET['password']))
            return !empty($userInfo);
        return false;
    }

    public function loadPlayers() {
        global $userInfo;
        $this->accountsResult = $this->qb->createQueryBuilder('users')->selectSql()->where('social = \'' . $userInfo['social'] . '\'')->limit(3)->executeQuery()->getResult();
    }

    public function getPlayers() {
        return $this->accountsResult;
    }

    public function getBusinessName($type) {

        $types = [
            "Банки", //0
            "Автомастерские", //1
            "Пункты аренды", //2
            "Заправочные станции", //3
            "Парикмахерские", //4
            "Тату салоны", //5
            "Развлечения", //6
            "Компании", //7
            "Остальное", //8
            "Магазины", //9
            "Магазины продуктов", //10
            "Магазины одежды", //11
            "Магазины оружия", //12
            "Станции тех. обслуживания", //13
            "Заправочные станции для воздушного ТС", //14
            "Заправочные станции для водного ТС", //15
        ];

        return $types[$type];
    }

    public function getBusinessPriceName($type) {
        if ($type < 1.8)
            return 'Низкие';
        if ($type < 2.7)
            return 'Средние';
        return 'Высокие';
    }

    public function isAdmin($adminLevel = 1) {
        if (isset($_COOKIE['user'])) {
            global $userInfo;
            if($userInfo['admin_level'] >= $adminLevel)
                return true;
        }
        return false;
    }

    public function getUserInfo($where) {

        global $server;

        if($where == '' || empty($where) || is_null($where)) return false;

        $where = $server->charsString($where);

        return $this->qb
            ->createQueryBuilder(EnumConst::USERS)
            ->selectSql()
            ->where('name = \'' . $where . '\'')
            ->orWhere('social = \'' . $where . '\'')
            ->orWhere('id = \'' . $where . '\'')
            ->executeQuery()
            ->getSingleResult()
            ;
    }

    public function getAccountInfo($where) {

        global $server;

        if($where == '' || empty($where) || is_null($where)) return false;

        if (is_numeric($where)) {
            return $this->qb
                ->createQueryBuilder('accounts')
                ->selectSql()
                ->where('id = \'' . $where . '\'')
                ->executeQuery()
                ->getSingleResult()
                ;
        }

        $where = $server->charsString($where);

        return $this->qb
            ->createQueryBuilder('accounts')
            ->selectSql()
            ->where('login = \'' . $where . '\'')
            ->orWhere('email = \'' . $where . '\'')
            ->orWhere('social = \'' . $where . '\'')
            ->orWhere('token = \'' . $where . '\'')
            ->orWhere('serial = \'' . $where . '\'')
            ->orWhere('hash_acc = \'' . $where . '\'')
            ->executeQuery()
            ->getSingleResult()
        ;
    }

    public function getAccountInfoOld($where) {

        global $server;

        if($where == '' || empty($where) || is_null($where)) return false;

        if (is_numeric($where)) {
            return $this->qb
                ->createQueryBuilder('accounts')
                ->selectSql()
                ->where('id = \'' . $where . '\'')
                ->executeQuery()
                ->getSingleResult()
                ;
        }

        $where = $server->charsString($where);

        return $this->qb
            ->createQueryBuilder('old_accounts')
            ->selectSql()
            ->where('login = \'' . $where . '\'')
            ->orWhere('email = \'' . $where . '\'')
            ->orWhere('social = \'' . $where . '\'')
            ->orWhere('token = \'' . $where . '\'')
            ->orWhere('serial = \'' . $where . '\'')
            ->executeQuery()
            ->getSingleResult()
            ;
    }

    public function giveVipAccount($userId, $type, $days) { // TODO переписать donate log

        $uInf = $this->getUserInfo(intval($userId));

        if($uInf['is_online'] == 1) {
            return 'Необходимо выйти с сервера.';
        }

        $vipTime = 0;
        $vipType = $type;

        if ($days > 0 && $uInf['vip_type'] > 0 && $uInf['vip_time'] > 0)
            $vipTime = intval($days * 86400) + $uInf['vip_time'];
        else if ($days > 0)
            $vipTime = intval($days * 86400) + time();

        $this->qb
            ->createQueryBuilder('users')
            ->updateSql(['vip_type', 'vip_time'], [$vipType, $vipTime])
            ->where('id = \'' . $uInf['id'] . '\'')
            ->executeQuery()
            ->getSingleResult()
        ;

        if ($vipType == 1)
            return 'Вы вам выдали VIP LIGHT на ' . $days . 'д.';
        return 'Вы вам выдали VIP HARD на ' . $days . 'д.';
    }

    public function addWorkExp($userId, $exp) {

        $uInf = $this->getUserInfo(intval($userId));

        if($uInf['is_online'] == 1) {
            return 'Необходимо выйти с сервера.';
        }

        $exp = $uInf['work_exp'] + $exp;

        if ($exp >= $uInf['work_lvl'] * 500) {

            $offsetLvl = 1;
            $offsetExp = $exp - $uInf['work_lvl'] * 500;

            for ($i = 1; $i < 10; $i++) {
                if ($offsetExp >= ($uInf['work_lvl'] + $i) * 500) {
                    $offsetExp = $offsetExp - ($uInf['work_lvl'] + $i) * 500;
                    $offsetLvl++;
                }
            }

            $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['work_lvl', 'work_exp'], [$uInf['work_lvl'] + $offsetLvl, $offsetExp])
                ->where('id = \'' . $uInf['id'] . '\'')
                ->executeQuery()
                ->getSingleResult()
            ;
        }
        else if ($exp < 0) {
            $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['work_exp'], [0])
                ->where('id = \'' . $uInf['id'] . '\'')
                ->executeQuery()
                ->getSingleResult()
            ;
        }
        else {
            $this->qb
                ->createQueryBuilder('users')
                ->updateSql(['work_exp'], [intval($exp)])
                ->where('id = \'' . $uInf['id'] . '\'')
                ->executeQuery()
                ->getSingleResult()
            ;
        }

        return 'Вы получили ' . $exp . ' опыта рабочего стажа';
    }

    public function getSignature($method, array $params, $secretKey) {
        ksort($params);
        unset($params['sign']);
        unset($params['signature']);
        array_push($params, $secretKey);
        array_unshift($params, $method);
        return hash('sha256', join('{up}', $params));
    }

    public function insertHistory($userId, $type, $reason) {

        $dateTime = $this->qb
            ->createQueryBuilder('daynight')
            ->selectSql()
            ->executeQuery()
            ->getSingleResult()
        ;

        if (mb_strlen($dateTime['hour'],'UTF-8') == 1)
            $dateTime['hour'] = '0' . $dateTime['hour'];
        if (mb_strlen($dateTime['minute'],'UTF-8') == 1)
            $dateTime['minute'] = '0' . $dateTime['minute'];
        if (mb_strlen($dateTime['day'],'UTF-8') == 1)
            $dateTime['day'] = '0' . $dateTime['day'];
        if (mb_strlen($dateTime['month'],'UTF-8') == 1)
            $dateTime['month'] = '0' . $dateTime['month'];

        $this->qb
            ->createQueryBuilder('log_player')
            ->insertSql(
                ['user_id', 'datetime', 'type', 'do'],
                [$userId, $dateTime['day'] . '/' . $dateTime['month'] . '/' . $dateTime['year'] . ' ' . $dateTime['hour'] . ':' . $dateTime['minute'], $type, $reason]
            )
            ->executeQuery()
            ->getSingleResult()
        ;
    }

    public function getRareName($proc = 50) {
        if ($proc === 0)
            return 'Обычная';
        if ($proc <= 10)
            return 'Легендарная';
        if ($proc <= 20)
            return 'Засекреченная';
        if ($proc <= 30)
            return 'Мистическая';
        if ($proc <= 40)
            return 'Элитная';
        if ($proc <= 50)
            return 'Невероятно редкая';
        if ($proc <= 60)
            return 'Очень редкая';
        if ($proc <= 70)
            return 'Редкая';
        if ($proc <= 80)
            return 'Необычная';
        if ($proc <= 90)
            return 'Ширпотреб';
        return 'Обычная';
    }

    public function giveRandomMask($userId, $proc = 50) {
        global $maskList;

        $maskId = 0;

        if ($proc > 0) {
            $maskTemp = [];
            $idx = 0;
            foreach ($maskList as $mask) {
                if ($mask[14] < $proc && $mask[14] > 0)
                    array_push($maskTemp, $idx);
                $idx++;
            }

            $maskId = $maskTemp[rand(0, count($maskTemp) - 1)];
            $this->giveMask($userId, $maskId);
        }
        else {
            $maskId = rand(0, count($maskList) - 1);
            $this->giveMask($userId, $maskId);
        }
    }

    public function getRandomMask($proc = 50) {
        global $maskList;

        $maskId = 0;

        if ($proc > 0) {
            $maskTemp = [];
            $idx = 0;
            foreach ($maskList as $mask) {
                if ($mask[14] < $proc && $mask[14] > 0)
                    array_push($maskTemp, $idx);
                $idx++;
            }

            $maskId = $maskTemp[rand(0, count($maskTemp) - 1)];
        }
        else {
            $maskId = rand(0, count($maskList) - 1);
        }
        return $maskId;
    }

    public function giveRandomMaskRange($userId, $proc = 30, $proc2 = 0) {
        global $maskList;

        $maskTemp = [];
        $idx = 0;
        foreach ($maskList as $mask) {
            if ($mask[14] < $proc && $mask[14] > $proc2)
                array_push($maskTemp, $idx);
            $idx++;
        }

        $maskId = $maskTemp[rand(0, count($maskTemp) - 1)];
        $this->giveMask($userId, $maskId);
    }

    public function giveMask($userId, $maskId) {

        global $maskList;

        $mask = $maskList[$maskId];
        $itemName = $mask[1];

        $params = '{"name":"' . str_replace('"', '', str_replace("'", '', $itemName)) . '","mask":' . $maskId . ',"desc":"' . $this->getRareName($mask[14]) . '"}';

        $this->qb
            ->createQueryBuilder('items')
            ->insertSql(
                ['item_id', 'owner_type', 'owner_id', 'params'],
                [274, 1, $userId, $params]
            )
            ->executeQuery(true)
            ->getResult()
        ;

        return $params;
    }
    public function getAdminName() {
        global $userInfo;
        foreach ($this->accountsResult as $acc) {
            if ($acc['admin_level'] > 0)
                return $acc['name'];
        }
        return $userInfo['social'];
    }

    public function isMedia() {
        foreach ($this->accountsResult as $acc) {
            if ($acc['status_media'])
                return true;
        }
        return false;
    }

    public function isLeader() {
        foreach ($this->accountsResult as $acc) {
            if ($acc['is_leader'])
                return true;
        }
        return false;
    }

    public function isSubLeader() {
        foreach ($this->accountsResult as $acc) {
            if ($acc['is_sub_leader'])
                return true;
        }
        return false;
    }

    public function isFraction($fractionId = -1) {
        foreach ($this->accountsResult as $acc) {
            if ($acc['fraction_id'] == $fractionId)
                return true;
        }
        return false;
    }

    public function isGetFraction($fractionId = -1) {
        foreach ($this->accountsResult as $acc) {
            if ($acc['fraction_id'] == $fractionId)
                return $fractionId;
        }
        return 0;
    }

    public function isGov() {
        foreach ($this->accountsResult as $acc) {
            if ($acc['fraction_id'] == 1)
                return true;
        }
        return false;
    }

    public function isLspd() {
        foreach ($this->accountsResult as $acc) {
            if ($acc['fraction_id'] == 2)
                return true;
        }
        return false;
    }

    public function isFib() {
        foreach ($this->accountsResult as $acc) {
            if ($acc['fraction_id'] == 3)
                return true;
        }
        return false;
    }

    public function isUsmc() {
        foreach ($this->accountsResult as $acc) {
            if ($acc['fraction_id'] == 4)
                return true;
        }
        return false;
    }

    public function isSheriff() {
        foreach ($this->accountsResult as $acc) {
            if ($acc['fraction_id'] == 5)
                return true;
        }
        return false;
    }

    public function isNews() {
        foreach ($this->accountsResult as $acc) {
            if ($acc['fraction_id'] == 7)
                return true;
        }
        return false;
    }

    public function isEms() {
        foreach ($this->accountsResult as $acc) {
            if ($acc['fraction_id'] == 6)
                return true;
        }
        return false;
    }
}