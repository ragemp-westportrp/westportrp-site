<?php
if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}
global $serverName;
global $serverNameColor;
global $user;


if(isset($_GET['q']) && !empty($_GET['q'])) {

    global $qb;
    global $server;

    $_GET['q'] = $server->charsString($_GET['q']);
    $keys = explode(' ', $_GET['q']);

    $qb
        ->createQueryBuilder('')
        ->otherSql("set session sql_mode=''", false)
        ->executeQuery()
        ->getResult()
    ;

    $newsLast = $qb->createQueryBuilder('rp_news')->selectSql()->limit(5)->orderBy('id DESC')->executeQuery()->getResult();

    foreach ($keys as $key) {

        $result = $qb
            ->createQueryBuilder('rp_web_search')
            ->selectSql()
            ->where('website_name LIKE \'%' . $key . '%\'')
            ->orWhere('website_title LIKE \'%' . $key . '%\'')
            ->orWhere('website_about LIKE \'%' . $key . '%\'')
            ->orWhere('tags LIKE \'%' . $key . '%\'')
            ->limit(20)
            ->executeQuery()
            ->getResult()
        ;

        if(!empty($result)) {
            foreach ($result as $forKey => $value)
                $result[$forKey]['keyword'] = $key;
            $searchList[] = $result;
        }

        $result = $qb
            ->createQueryBuilder('rp_news')
            ->selectSql()
            ->where('title LIKE \'%' . $key . '%\'')
            ->orWhere('text LIKE \'%' . $key . '%\'')
            ->orWhere('author_name LIKE \'%' . $key . '%\'')
            ->leftJoin('rp_web_search ON rp_news.fraction = rp_web_search.fraction_id')
            ->orderBy('rp_news.id DESC')
            ->limit(30)
            ->executeQuery()
            ->getResult()
        ;

        if(!empty($result)) {
            foreach ($result as $forKey => $value)
                $result[$forKey]['keyword'] = $key;
            $searchList2[] = $result;
        }

        $result = $qb
            ->createQueryBuilder('rp_business_news')
            ->selectSql()
            ->where('rp_business_news.title LIKE \'%' . $key . '%\'')
            ->orWhere('rp_business_news.text LIKE \'%' . $key . '%\'')
            ->orWhere('rp_business_news.author_name LIKE \'%' . $key . '%\'')
            ->leftJoin('business ON rp_business_news.fraction = business.id')
            ->orderBy('rp_business_news.id DESC')
            ->limit(30)
            ->executeQuery()
            ->getResult()
        ;

        if(!empty($result)) {
            foreach ($result as $forKey => $value)
                $result[$forKey]['keyword'] = $key;
            $searchList2[] = $result;
        }

        $result = $qb
            ->createQueryBuilder('rp_gov_party_list')
            ->selectSql()
            ->where('title LIKE \'%' . $key . '%\'')
            ->orWhere('content LIKE \'%' . $key . '%\'')
            ->orWhere('content_desc LIKE \'%' . $key . '%\'')
            ->orWhere('user_owner LIKE \'%' . $key . '%\'')
            ->orderBy('id DESC')
            ->limit(30)
            ->executeQuery()
            ->getResult()
        ;

        if(!empty($result)) {
            foreach ($result as $forKey => $value)
                $result[$forKey]['keyword'] = $key;
            $searchList2[] = $result;
        }

        $result = $qb
            ->createQueryBuilder('business')
            ->selectSql()
            ->where('name LIKE \'%' . $key . '%\'')
            ->orWhere('user_name LIKE \'%' . $key . '%\'')
            ->limit(20)
            ->executeQuery()
            ->getResult()
        ;

        //print_r($result);

        if(!empty($result)) {
            foreach ($result as $forKey => $value)
                $result[$forKey]['keyword'] = $key;
            $searchList2[] = $result;
        }

        $searchListCount[] = $qb
            ->createQueryBuilder('rp_web_search')
            ->selectSql('count(*)')
            ->where('website_name LIKE \'%' . $key . '%\'')
            ->orWhere('website_title LIKE \'%' . $key . '%\'')
            ->orWhere('website_about LIKE \'%' . $key . '%\'')
            ->orWhere('tags LIKE \'%' . $key . '%\'')
            ->executeQuery()
            ->getSingleResult()
        ;

        $searchListCount2[] = $qb
            ->createQueryBuilder('rp_news')
            ->selectSql('count(*)')
            ->where('title LIKE \'%' . $key . '%\'')
            ->orWhere('text LIKE \'%' . $key . '%\'')
            ->orWhere('author_name LIKE \'%' . $key . '%\'')
            ->leftJoin('rp_web_search ON rp_news.fraction = rp_web_search.fraction_id')
            ->executeQuery()
            ->getSingleResult()
        ;
    }
    /*$searchList3 = $qb
        ->createQueryBuilder('rp_trade')
        ->selectSql()
        ->where('title LIKE \'%' . $_GET['q'] . '%\'')
        ->orWhere('text LIKE \'%' . $_GET['q'] . '%\'')
        ->orWhere('author_name LIKE \'%' . $_GET['q'] . '%\'')
        ->orWhere('trade_name LIKE \'%' . $_GET['q'] . '%\'')
        ->orWhere('trade_phone LIKE \'%' . $_GET['q'] . '%\'')
        ->orWhere('trade_email LIKE \'%' . $_GET['q'] . '%\'')
        ->orderBy('id DESC')
        ->limit(30)
        ->executeQuery()
        ->getSingleResult()
    ;*/
    /*$searchListCount3 = $qb
        ->createQueryBuilder('rp_trade')
        ->selectSql('count(*)')
        ->where('title LIKE \'%' . $_GET['q'] . '%\'')
        ->orWhere('text LIKE \'%' . $_GET['q'] . '%\'')
        ->orWhere('author_name LIKE \'%' . $_GET['q'] . '%\'')
        ->orWhere('trade_name LIKE \'%' . $_GET['q'] . '%\'')
        ->orWhere('trade_phone LIKE \'%' . $_GET['q'] . '%\'')
        ->orWhere('trade_email LIKE \'%' . $_GET['q'] . '%\'')
        ->executeQuery()
        ->getSingleResult()
    ;*/

    $countResult = 0;

    foreach ($searchListCount as $count) {
        $countResult += reset($count);
    }
    foreach ($searchListCount2 as $count) {
        $countResult += reset($count);
    }

    $searchListReserve = [];
    if (!empty($searchList) && !empty($searchList2))
        $searchListReserve = array_merge($searchList, $searchList2);
    else if (!empty($searchList))
        $searchListReserve = $searchList;
    else if (!empty($searchList2))
        $searchListReserve = $searchList2;

    $searchList = [];
    $searchListUnion = [];
    foreach ($searchListReserve as $array) {
        $searchList = array_merge($searchList, $array);
    }

    $countResult = count($searchList);

    //$searchList = array_unique($searchList, ['website_title']); TODO отбросить хуйню

    $countSort = [];
    foreach ($searchList as $key => $value){
        foreach ($value as $key2 => $value2){
            if (array_key_exists($key2, $countSort) && array_key_exists($value2, $countSort[$key2])){
                $countSort[$key2][$value2]++;
            } else {
                $countSort[$key2][$value2] = 1;
            }
        }
    }

    echo '
    <div class="section no-pad-bot" id="index-banner">
        <div class="container">
            <div class="row">
                <div class="col s12 l7">';
                    if($countResult != 0) {

                        //echo '<label>Нашлось всего результатов: ' . ($countResult) . '</label>';

                        /*foreach ($searchList as $items) {
                            foreach ($items as $item) {
                                echo '
                                <div class="card white">
                                    <div class="card-content black-text">
                                        <span class="card-title"><a target="_blank" href="' . $item['uri'] . '" style="cursor: pointer;">' . $item['website_title'] . '</a></span>
                                        <a target="_blank" class="green-text" href="' . $item['uri'] . '">https://' . $item['website_name'] . '/</a>
                                        <p>' . $item['website_about'] . '</p>
                                    </div>
                                </div>
                                ';
                            }
                        }*/

                        foreach ($countSort as $key => $value) {
                            foreach ($searchList as $item) {

                                if (isset($item['content_desc'])) {
                                    $textNews = isset($item['content_desc']) ? $item['content_desc'] : $item['website_about'];
                                    if (!in_array((isset($item['title']) ? $item['title'] : $item['website_title']), $searchListUnion)) {
                                        array_push($searchListUnion, (isset($item['title']) ? $item['title'] : $item['website_title']));
                                        echo '
                                        <div class="card white" style="border-radius: 8px; border: 1px #efefef solid">
                                            <div class="card-content black-text">
                                                <span class="card-title" style="background: none"><a href="/network/gov/consignment-info?id=' . $item['id'] . '" style="cursor: pointer;">' . htmlspecialchars_decode(isset($item['title']) ? $item['title'] : $item['website_title']) . '</a></span>
                                                <a class="green-text" href="/network/gov/consignment-info?id=' . $item['id'] . '">gov.sa/consignment' . $item['id'] . '</a>
                                                <p>' . htmlspecialchars_decode($textNews) . '...</p>
                                            </div>
                                        </div>
                                        ';
                                    }
                                }
                                else if (isset($item['website_about'])) {

                                    if (!in_array($item['website_title'], $searchListUnion)) {
                                        array_push($searchListUnion, $item['website_title']);
                                        $textNews = $item['website_about'];
                                        echo '
                                            <div class="card white" style="border-radius: 8px; border: 1px #efefef solid">
                                                <div class="card-content black-text">
                                                    <span class="card-title" style="background: none"><a href="' . $item['uri'] . '" style="cursor: pointer;">' . $item['website_title'] . '</a></span>
                                                    <a class="green-text" href="' . $item['uri'] . '">' . $item['website_name'] . '</a>
                                                    <p>' . htmlspecialchars_decode($textNews) . '</p>
                                                </div>
                                            </div>
                                        ';
                                    }
                                }
                                else if (isset($item['user_name'])) {
                                    if (!in_array((htmlspecialchars_decode($item['name'])), $searchListUnion)) {
                                        array_push($searchListUnion, (htmlspecialchars_decode($item['name'])));
                                        echo '
                                        <div class="card white" style="border-radius: 8px; border: 1px #efefef solid">
                                            <div class="card-content black-text">
                                                <span class="card-title" style="background: none"><a href="/network/business-info' . $item['id'] . '" style="cursor: pointer;">' . (htmlspecialchars_decode($item['name'])) . '</a></span>
                                                <a class="green-text" href="/network/business-info' . $item['id'] . '">business.sa/' . strtolower(str_replace(' ', '-', $item['name'])) . '</a>
                                                <p>Цены: ' . $user->getBusinessPriceName($item['price_product']) . '</p>
                                            </div>
                                        </div>
                                        ';
                                    }
                                }
                            }
                        }

                        /*foreach ($resultBusiness as $item) {
                            echo '
                                <div class="card white">
                                    <div class="card-content black-text">
                                        <span class="card-title"><a target="_blank" href="/network/business/id' . $item['id'] . '" style="cursor: pointer;">' . (isset($item['title']) ? $item['title'] : $item['website_title']) . '</a></span>
                                        <a target="_blank" class="green-text" href="/network/business/id' . $item['id'] . '">https://gov.sa/party' . $item['id'] . '</a>
                                    </div>
                                </div>
                            ';
                        }*/
                    }
                    else {
                        echo '
                        <div class="white">
                            <div class="black-text">
                                <h4 style="margin-top: 0">Ошибка 404</h4>
                                <p>По вашему запросу ничего не найдено</p>
                            </div>
                        </div>
                        ';
                    }
            echo '</div>
                <div class="col s12 l1"></div>
                <div class="col s12 l4">
                    <h5 class="grey-text" style="margin-top: 0; margin-bottom: 20px">Последние новости</h5>
                    <hr>
                    ';
                    foreach ($newsLast as $item) {
                        echo '
                        <div class="row">
                            <br>
                            <div class="col s12 l3">
                                <a href="/network/gov?newsId=' . $item['id'] . '"><img loading="lazy" style="width: 100%; border-radius: 12px; object-fit: contain;" src="' . htmlspecialchars_decode($item['img']) . '"></a>
                            </div>
                            <div class="col s12 l9">
                                <div class="black-text">
                                    <label style="font-size: 1.2rem; font-weight: 400" class="grey-text text-darken-3"><a class="grey-text text-darken-3" href="/network/gov?newsId=' . $item['id'] . '">' . htmlspecialchars_decode($item['title']) . '</a></label>';
                                echo '
                                </div>
                                <label class="black-text" style="font-size: 1rem">
                                   <label>Автор: ' . htmlspecialchars_decode($item['author_name']) . '</label>
                                </label>
                            </div>
                        </div>
                        <hr>
                    ';
                     }
                    echo'
                </div>
            </div>
        </div>
    </div>
    ';
}
else {
    echo '
    <div class="section" style="margin-top: 10%">
        <div class="container search-container">
            <div class="row">
                <div class="col s12 m1 l3">
                </div>
                <div class="col s12 m10 l6">
                    <h2 class="center"><b>' . $serverNameColor . '</b></h2>
                    <form class="card-panel" style="padding: 0; border-radius: 2px;">
                        <div class="input-field">
                            <input style="text-align: center;  border: 1px #efefef solid; border-radius: 8px" id="search" type="search" name="q" placeholder="Введите поисковый запрос" required="">
                        </div>
                    </form>
                    <div class="center"><label>Ищи информацию - просто!</label></div>
                </div>
            </div>
        </div>
    </div>
    ';
}
?>