<?php

namespace Server\Core;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}
/**
 * Request
 */
class Request
{
    protected $arrayRequest = [
        'index',
        'login',
        'logout',
        //'start',
        'rules',
        'donate',
        'discord',
        'user-donate-',
        'map',
        //'gmap',
        //'dev',
        //'media',
        //'vote',
        'top',
        //'contest',
        'cars',
        'car-info-',
        'wheremypass',
        'heremypass',
        //'launcherApi',
        //'twitchApi',
        //'serverApi',
        //'publicApi',
        //'servers',
        'report',
        'report-id-',
        'renouncement',
        'offer',
        'payment',
        //'faq',
        //'faq-',
        'banlist',
        'personal',
        'market',
        'profile',
        'account-info-',
        //'info',
        'browser',
        'network/search',
        'network/search/bookmarks',
        'network/business',
        'network/business-info',
        'network/lspd',
        'network/lspd/log',
        'network/lspd/users',
        'network/lspd/info',
        'network/ems',
        'network/ems/log',
        'network/ems/users',
        'network/ems/info',
        'network/news',
        'network/news/log',
        'network/news/users',
        'network/news/map',
        'network/news/ads',
        'network/news/bio',
        'network/news/bioedit',
        'network/news/vehicles',
        'network/news/vehicle-',
        'network/usmc',
        'network/usmc/log',
        'network/usmc/users',
        'network/usmc/info',
        'network/sheriff',
        'network/sheriff/log',
        'network/sheriff/users',
        'network/sheriff/info',
        'network/gov',
        'network/gov/log',
        'network/gov/rules',
        'network/gov/users',
        'network/gov/info',
        'network/gov/consignment',
        'network/gov/consignment-info',
        'network/gov/consignment-create',
        'admin/accounts',
        'admin/users',
        'admin/stats',
        'admin/log-',
        'admin/editor/edit-',
        'admin/editor/insert-',
        'admin/editor/delete-',
        'admin/main',
        'debug'
    ];

    public function getRequest($params = []) {

        $result = [];
        //$params = array_merge($params, json_decode(file_get_contents('config/request.json'), true));

        $params = array_merge($params, $this->arrayRequest);

        if (empty($params)) return false;

        foreach ($params as $value) {
            if (preg_match('#/' . $value . '([^/?]+)#', $_SERVER['REQUEST_URI'], $match)) {
                $result[$value] = $match[1];
            }
            else if (preg_match('#^/?(' . $value . ')#', $_SERVER['REQUEST_URI'], $match)) {
                $result['p'] = $match[1];
            }
        }
        return $result;
    }

    public function getAjaxRequest($url, $params = []) {

        $result = [];
        //$params = array_merge($params, json_decode(file_get_contents('config/request.json'), true));

        $url = 'https://example.com/' . $url;
        $params = array_merge($params, $this->arrayRequest);

        if (empty($params)) return false;

        foreach ($params as $value) {
            if (preg_match('#/' . $value . '([^/?]+)#', $url, $match)) {
                $result[$value] = $match[1];
            }
            else if (preg_match('#^/?(' . $value . ')#', $url, $match)) {
                $result['p'] = $match[1];
            }
            else if ($url == $value)
                $result['p'] = $url;

            print_r($url);
        }
        return $result;
    }

    public function showPage($page, $ajax = false) {

        global $methods;
        global $tmp;
        global $qb;
        global $view;
        global $user;
        global $userInfo;
        global $server;
        global $browser;

        //print_r($page);

        if (strpos($page['p'], 'network') >= 0 && !$browser) {
            $tmp->showPage('index', 'Главная', $ajax);
            return;
        }

        switch ($page['p']) {
            case 'logout':
                $user->logout();
                break;
            case 'login':
                $tmp->showPage('login', 'Авторизация', $ajax);
                break;
            case 'discord':
                header('Location: https://discord.gg/84VerfZBGT');
                break;
            case 'start':
                $tmp->showPage('start', 'Как начать играть?', $ajax);
                break;
            case 'rules':
                $tmp->showPage('rules', 'Правила проекта', $ajax);
                break;
            case 'dev':
                $tmp->showPage('dev', 'Разработка', $ajax);
                break;
            case 'donate':
                $tmp->showPage('donate', 'Донат', $ajax);
                break;
            case 'top':
                if($user->isAdmin())
                    $tmp->showPage('top', 'Топ 100 игроков', $ajax);
                else
                    $tmp->showPage('index', 'Главная', $ajax);
                break;
            case 'renouncement':
                $tmp->showPage('renouncement', 'Отказ от ответственности', $ajax);
                break;
            case 'offer':
                $tmp->showPage('offer', 'Договор оферты', $ajax);
                break;
            case 'personal':
                $tmp->showPage('personal', 'Политика конфиденциальности', $ajax);
                break;
            case 'faq':
                $tmp->showPage('faq', 'FAQ', $ajax);
                break;
            case 'report':
                $tmp->showPage('report', 'Список жалоб', $ajax);
                break;
            case 'wheremypass':
                $tmp->showPage('wheremypass', 'Восстановление пароля', $ajax);
                break;
            case 'heremypass':
                $tmp->showPage('heremypass', 'Оо, а вот и мой пароль', $ajax);
                break;
            case 'media':
                $tmp->showPage('media', 'Media условия', $ajax);
                break;
            case 'servers':
                $tmp->showPage('servers', 'Статистика сервера', $ajax);
                break;
            case 'banlist':
                $tmp->showPage('banlist', 'Банлист', $ajax);
                break;
            case 'market':
                $tmp->showPage('market', 'Торговая площадка', $ajax);
                break;
            case 'payment':
                $tmp->showBlockPage('payment');
                break;
            /*case 'launcherApi':
                header('Content-Type: application/json');
                $srv1Online = $qb->createQueryBuilder('monitoring')->selectSql()->executeQuery()->getSingleResult();
                $currentStats['online'] = $srv1Online['online'];
                $currentStats['max_online'] = $srv1Online['max_online'];
                $currentStats['label'] = 'Python';
                $currentStats['branch'] = 'prerelease';
                $currentStats['is_online'] = true;
                echo json_encode($currentStats);
                break;
            case 'twitchApi':
                $tmp->showBlockPage('api/twitch');
                break;
            case 'publicApi':
                $tmp->showBlockPage('api/public');
                break;
            case 'serverApi':
                $tmp->showBlockPage('api/server');
                break;
            case 'debug':
                $tmp->setTitle('Debug');
                $tmp->showBlockPage('debug');
                break;
            case 'info':
                $tmp->setTitle('Info');
                $tmp->showBlockPage('info');
                break;

            case 'gmap':
                $tmp->setTitle('Карта');
                $view->set('isMap', true);
                $tmp->showBlockPage('header');
                $tmp->showBlockPage('gmap');
                break;
            case 'vote':
                $tmp->showPage('vote', 'Голосование за губернатора', $ajax);
                break;
                break;
            case 'contest':
                $tmp->showPage('contest', 'Конкурс', $ajax);
                break;*/
            case 'browser':
                $tmp->showBlockPage('browser');
                break;
            case 'network/lspd':
                $tmp->setTitle('Los Santos Police Department');
                $tmp->showBlockPage('network/lspd/header');
                $tmp->showBlockPage('network/lspd/index');
                $tmp->showBlockPage('network/lspd/footer');
                break;
            case 'network/lspd/log':
                if ($user->isLogin() && (($user->isLeader() || $user->isSubLeader()) && $user->isLspd() || $user->isAdmin())) {
                    $tmp->setTitle('Логи');
                    $tmp->showBlockPage('network/lspd/header');
                    $tmp->showBlockPage('network/all/log');
                    $tmp->showBlockPage('network/lspd/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/lspd/users':
                if ($user->isLogin() && ($user->isLspd() || $user->isAdmin())) {
                    $tmp->setTitle('Сотрудники');
                    $tmp->showBlockPage('network/lspd/header');
                    $tmp->showBlockPage('network/all/users');
                    $tmp->showBlockPage('network/lspd/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/lspd/info':
                if ($user->isLogin() && ($user->isLspd() || $user->isAdmin())) {
                    $tmp->setTitle('База данных');
                    $tmp->showBlockPage('network/lspd/header');
                    $tmp->showBlockPage('network/all/info');
                    $tmp->showBlockPage('network/lspd/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/news':

                if (isset($page['network/news/vehicle-'])) {
                    $carName = $server->charsString($page['network/news/vehicle-']);
                    $view->set('carName', $carName);
                    $tmp->setTitle($carName);
                    $tmp->showBlockPage('network/news/header');
                    $tmp->showBlockPage('network/news/vehicle');
                    $tmp->showBlockPage('network/news/footer');
                }
                else {
                    $tmp->showBlockPage('network/news/header');
                    $tmp->showBlockPage('network/news/index');
                    $tmp->showBlockPage('network/news/footer');
                }
                break;
            case 'network/news/vehicles':
                $tmp->setTitle('Список транспорта');
                $tmp->showBlockPage('network/news/header');
                $tmp->showBlockPage('network/news/vehicles');
                $tmp->showBlockPage('network/news/footer');
                break;
            case 'network/news/ads':
                $tmp->setTitle('Список объявлений');
                $tmp->showBlockPage('network/news/header');
                $tmp->showBlockPage('network/news/ads');
                $tmp->showBlockPage('network/news/footer');
                break;
            case 'network/news/bio':
                $tmp->setTitle('Биографии');
                $tmp->showBlockPage('network/news/header');
                $tmp->showBlockPage('network/news/bio');
                $tmp->showBlockPage('network/news/footer');
                break;
            case 'network/news/map':
                $tmp->setTitle('Карта');
                $view->set('isMap', true);
                $tmp->showBlockPage('header');
                $tmp->showBlockPage('network/news/map');
                break;
            case 'network/news/bioedit':
                $tmp->setTitle('Биографии');
                $tmp->showBlockPage('network/news/header');
                $tmp->showBlockPage('network/news/bioedit');
                $tmp->showBlockPage('network/news/footer');
                break;
            case 'network/news/log':
                if ($user->isLogin() && (($user->isLeader() || $user->isSubLeader()) && $user->isNews() || $user->isAdmin())) {
                    $tmp->setTitle('Логи');
                    $tmp->showBlockPage('network/news/header');
                    $tmp->showBlockPage('network/all/log');
                    $tmp->showBlockPage('network/news/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/news/users':
                if ($user->isLogin() && ($user->isNews() || $user->isAdmin())) {
                    $tmp->setTitle('Сотрудники');
                    $tmp->showBlockPage('network/news/header');
                    $tmp->showBlockPage('network/all/users');
                    $tmp->showBlockPage('network/news/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/ems':
                $tmp->setTitle('Служба спасения');
                $tmp->showBlockPage('network/ems/header');
                $tmp->showBlockPage('network/ems/index');
                $tmp->showBlockPage('network/ems/footer');
                break;
            case 'network/ems/log':
                if ($user->isLogin() && (($user->isLeader() || $user->isSubLeader()) && $user->isEms() || $user->isAdmin())) {
                    $tmp->setTitle('Логи');
                    $tmp->showBlockPage('network/ems/header');
                    $tmp->showBlockPage('network/all/log');
                    $tmp->showBlockPage('network/ems/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/ems/users':
                if ($user->isLogin() && ($user->isEms() || $user->isAdmin())) {
                    $tmp->setTitle('Сотрудники');
                    $tmp->showBlockPage('network/ems/header');
                    $tmp->showBlockPage('network/all/users');
                    $tmp->showBlockPage('network/ems/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/ems/info':
                if ($user->isLogin() && ($user->isEms() || $user->isAdmin())) {
                    $tmp->setTitle('База данных');
                    $tmp->showBlockPage('network/ems/header');
                    $tmp->showBlockPage('network/all/info');
                    $tmp->showBlockPage('network/ems/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/usmc':
                $tmp->setTitle('United States Marine Corps');
                $tmp->showBlockPage('network/usmc/header');
                $tmp->showBlockPage('network/usmc/index');
                $tmp->showBlockPage('network/usmc/footer');
                break;
            case 'network/usmc/log':
                if ($user->isLogin() && (($user->isLeader() || $user->isSubLeader()) && $user->isUsmc() || $user->isAdmin())) {
                    $tmp->setTitle('Логи');
                    $tmp->showBlockPage('network/usmc/header');
                    $tmp->showBlockPage('network/all/log');
                    $tmp->showBlockPage('network/usmc/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/usmc/users':
                if ($user->isLogin() && ($user->isUsmc() || $user->isAdmin())) {
                    $tmp->setTitle('Сотрудники');
                    $tmp->showBlockPage('network/usmc/header');
                    $tmp->showBlockPage('network/all/users');
                    $tmp->showBlockPage('network/usmc/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/usmc/info':
                if ($user->isLogin() && ($user->isUsmc() || $user->isAdmin())) {
                    $tmp->setTitle('База данных');
                    $tmp->showBlockPage('network/usmc/header');
                    $tmp->showBlockPage('network/all/info');
                    $tmp->showBlockPage('network/usmc/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/sheriff':
                $tmp->setTitle('Sheriff Department');
                $tmp->showBlockPage('network/sheriff/header');
                $tmp->showBlockPage('network/sheriff/index');
                $tmp->showBlockPage('network/sheriff/footer');
                break;
            case 'network/sheriff/log':
                if ($user->isLogin() && (($user->isLeader() || $user->isSubLeader()) && $user->isSheriff() || $user->isAdmin())) {
                    $tmp->setTitle('Логи');
                    $tmp->showBlockPage('network/sheriff/header');
                    $tmp->showBlockPage('network/all/log');
                    $tmp->showBlockPage('network/sheriff/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/sheriff/users':
                if ($user->isLogin() && ($user->isSheriff() || $user->isAdmin())) {
                    $tmp->setTitle('Сотрудники');
                    $tmp->showBlockPage('network/sheriff/header');
                    $tmp->showBlockPage('network/all/users');
                    $tmp->showBlockPage('network/sheriff/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/sheriff/info':
                if ($user->isLogin() && ($user->isSheriff() || $user->isAdmin())) {
                    $tmp->setTitle('База данных');
                    $tmp->showBlockPage('network/sheriff/header');
                    $tmp->showBlockPage('network/all/info');
                    $tmp->showBlockPage('network/sheriff/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/gov':
                $tmp->setTitle('Правительство');
                $tmp->showBlockPage('network/gov/header');
                $tmp->showBlockPage('network/gov/index');
                $tmp->showBlockPage('network/gov/footer');
                break;
            case 'network/gov/log':
                if ($user->isLogin() && (($user->isLeader() || $user->isSubLeader()) && $user->isGov() || $user->isAdmin())) {
                    $tmp->setTitle('Логи');
                    $tmp->showBlockPage('network/gov/header');
                    $tmp->showBlockPage('network/all/log');
                    $tmp->showBlockPage('network/gov/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/gov/users':
                if ($user->isLogin() && ($user->isGov() || $user->isAdmin())) {
                    $tmp->setTitle('Сотрудники');
                    $tmp->showBlockPage('network/gov/header');
                    $tmp->showBlockPage('network/all/users');
                    $tmp->showBlockPage('network/gov/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/gov/info':
                if ($user->isLogin() && ($user->isGov() || $user->isAdmin())) {
                    $tmp->setTitle('Информация');
                    $tmp->showBlockPage('network/gov/header');
                    $tmp->showBlockPage('network/all/info');
                    $tmp->showBlockPage('network/gov/footer');
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибочка', $ajax);
                }
                break;
            case 'network/gov/consignment':
                $tmp->setTitle('Партии');
                $tmp->showBlockPage('network/gov/header');
                $tmp->showBlockPage('network/gov/consignment');
                $tmp->showBlockPage('network/gov/footer');
                break;
            case 'network/gov/consignment-create':
                $tmp->setTitle('Создать партию');
                $tmp->showBlockPage('network/gov/header');
                $tmp->showBlockPage('network/gov/consignment-create');
                $tmp->showBlockPage('network/gov/footer');
                break;
            case 'network/gov/consignment-info':

                $fullNews = $qb
                    ->createQueryBuilder('rp_gov_party_list')
                    ->selectSql()
                    ->where('id = ' . intval($_GET['id']))
                    ->limit(1)
                    ->executeQuery()
                    ->getSingleResult()
                ;

                if(!is_numeric($_GET['id']) || empty($fullNews)) {
                    $view->set('error404', true);
                    $tmp->setTitle('Ошибка 404');
                    $tmp->showBlockPage('network/gov/header');
                    $tmp->showBlockPage('errors/404');
                    $tmp->showBlockPage('network/gov/footer');
                    return;
                }
                $view->set('news', $fullNews);
                $view->set('metaImg', htmlspecialchars_decode($fullNews['img']));
                $tmp->setTitle( htmlspecialchars_decode($fullNews['title']) . '');
                $tmp->showBlockPage('network/gov/header');
                $tmp->showBlockPage('network/gov/consignment-info');
                $tmp->showBlockPage('network/gov/footer');
                break;
            case 'network/gov/rules':
                $tmp->setTitle('Кодексы и законы');
                $tmp->showBlockPage('network/gov/header');
                $tmp->showBlockPage('network/gov/rules');
                $tmp->showBlockPage('network/gov/footer');
                break;
            case 'network/business':
                $tmp->setTitle('Arcadius Business Center');
                $tmp->showBlockPage('network/business/header');
                $tmp->showBlockPage('network/business/index');
                $tmp->showBlockPage('network/business/footer');
                break;
            case 'network/search':
                $tmp->setTitle('Поиск');
                $tmp->showBlockPage('network/search/header');
                $tmp->showBlockPage('network/search/index');
                $tmp->showBlockPage('network/search/footer');
                break;
            case 'network/search/bookmarks':
                $tmp->setTitle('Закладки');
                $tmp->showBlockPage('network/search/header');
                $tmp->showBlockPage('network/search/bookmarks');
                $tmp->showBlockPage('network/search/footer');
                break;
            case 'map':
                if ($user->isAdmin()) {
                    $tmp->setTitle('Карта');
                    $view->set('isMap', true);
                    $tmp->showBlockPage('header');
                    $tmp->showBlockPage('map');
                }
                else {

                    $tmp->showPage('index', 'Главная', $ajax);
                }

                break;
            case 'cars':
                $tmp->showPage('car-list', 'Список транспорта', $ajax);
                break;
            case 'admin/main':
                if ($user->isLogin() && $user->isAdmin()) {
                    $tmp->showPage('admin/index', 'Главная', $ajax);
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибка 404', $ajax);
                }
                break;
            case 'admin/stats':
                if ($user->isLogin() && $user->isAdmin()) {
                    $tmp->showPage('admin/stats', 'Статистика', $ajax);
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибка 404', $ajax);
                }
                break;
            case 'admin/accounts':
                if ($user->isLogin() && $user->isAdmin()) {
                    $tmp->showPage('admin/accounts', 'Аккаунт', $ajax);
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибка 404', $ajax);
                }
                break;
            case 'admin/users':
                if ($user->isLogin() && $user->isAdmin()) {
                    $tmp->showPage('admin/users', 'Персонаж', $ajax);
                }
                else {
                    $tmp->showPage('errors/404', 'Ошибка 404', $ajax);
                }
                break;
            case 'profile':
                if ($user->isLogin()) {
                    $tmp->showPage('profile', 'Профиль', $ajax);
                }
                else {
                    $tmp->showPage('login', 'Авторизация', $ajax);
                }
                break;
            default:

                if ($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/index.php' || $_SERVER['REQUEST_URI'] == 'index.php' || $_SERVER['REQUEST_URI'] == 'index') {
                    $view->set('overflowHidden', true);
                    $tmp->showPage('index', 'Главная', $ajax);
                } else {
                    if (isset($page['admin/editor/edit-']) && $user->isLogin() && $user->isAdmin()) {
                        $tmp->showPage('admin/editor/edit', 'Редактор', $ajax);
                    }
                    else if (isset($page['admin/editor/insert-']) && $user->isLogin() && $user->isAdmin()) {
                        $tmp->showPage('admin/editor/insert', 'Добавление', $ajax);
                    }
                    else if (isset($page['admin/editor/delete-']) && $user->isLogin() && $user->isAdmin()) {
                        $tmp->showPage('admin/editor/delete', 'Удаление', $ajax);
                    }
                    else if (isset($page['admin/log-']) && $user->isLogin() && $user->isAdmin()) {
                        $tmp->showPage('admin/log', 'Логи', $ajax);
                    }
                    else if (isset($page['report-id-'])) {
                        if (!$user->isLogin()) {
                            header('Location: /login');
                            $user->logout();
                            die("Hacking attempt");
                        }
                        $tmp->showPage('report-id', 'Жалоба #' . $page['report-id-'], $ajax);
                    }
                    else if (isset($page['car-info-'])) {
                        $carName = $server->charsString($page['car-info-']);
                        $view->set('carName', $carName);
                        $tmp->showPage('car-info', $carName, $ajax);
                    }
                    else if (isset($page['network/business-info'])) {
                        $id = intval($page['network/business-info']);
                        $view->set('bid', $id);
                        $tmp->setTitle('Информация о бизнесе');
                        $tmp->showBlockPage('network/business/header');
                        $tmp->showBlockPage('network/business/id');
                        $tmp->showBlockPage('network/business/footer');
                    }
                    else if (isset($page['account-info-']) && $user->isLogin()) {
                        $id = intval($page['account-info-']);
                        if ($id < 1 && isset($_GET['id'])) {
                            $id = intval($_GET['id']);
                        }
                        $accInfo = $user->getUserInfo($id);
                        if ($accInfo['social'] !== $userInfo['social'] && $userInfo['admin_level'] == 0) {
                            $tmp->showPage('errors/404', 'Ошибка 404', $ajax);
                        }
                        else {
                            $view->set('accInfo', $accInfo);
                            $tmp->showPage('account-info', $accInfo['name'] . ' | Информация об аккаунте', $ajax);
                        }
                    }
                    else if (isset($page['user-donate-']) && $user->isLogin()) {
                        $accInfo = $user->getUserInfo($page['user-donate-']);
                        if ($accInfo['social'] !== $userInfo['social']) {
                            $tmp->showPage('errors/404', 'Ошибка 404', $ajax);
                        }
                        else {
                            $view->set('accInfo', $accInfo);
                            $tmp->showPage('donate-id', $accInfo['name'] . ' | Донат', $ajax);
                        }
                    }
                    else if (isset($page['faq-'])) {
                        $faq = $qb->createQueryBuilder('faq_list')->selectSql()->where('id = \'' . intval($page['faq-']) . '\'')->executeQuery()->getSingleResult();

                        if (empty($faq)) {
                            $tmp->showPage('errors/404', 'Ошибка 404', $ajax);
                        }
                        else {
                            $qb->createQueryBuilder('faq_list')->updateSql(['views'], [++$faq['views']])->where('id = \'' . intval($page['faq-']) . '\'')->executeQuery()->getResult();
                            $view->set('faq', $faq);
                            $tmp->showPage('faq-id', $faq['title'] . ' | FAQ', $ajax);
                        }
                    }
                    else {
                        $view->set('overflowHidden', true);
                        $tmp->showPage('index', 'Главная', $ajax);
                    }
                }
        }
    }
}