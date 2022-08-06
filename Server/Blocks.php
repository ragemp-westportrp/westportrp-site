<?php

namespace Server;

use Server\Core\EnumConst;
use Server\Core\QueryBuilder;
use Server\Core\Server;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * Blocks
 */
class Blocks
{
    /**
     * @param $item
     * @return string
     */
    public function getTopServerBlock($item) {

        global $lang;
        return '
            <li class="collection-item avatar" style="min-height: 64px;">
                <img src="/client/images/mp-logo/' . $this->getMpLogo($item['mp']) . '" style="object-fit: contain; border-radius: 0;" alt=""  class="circle">
                <a class="title black-text" href="/server-info-' . $item['id'] . '">' . htmlspecialchars_decode($item['name']) . '</a><br>
                <label>' . $lang['players'] . ': ' . $item['players'] . '/' . $item['slots'] . '</label>
                <a href="/server-info-' . $item['id'] . '" class="hide-on-small-only secondary-content blue-text tooltipped" data-position="left" data-delay="50" data-tooltip="' . $lang['rating'] . ': ' . $item['rating'] . '"><i class="material-icons">star</i></a>
            </li>
        ';
    }

    public function getPlayerComments($serverId) {

        global $qb;
        global $server;

        $result = $qb->createQueryBuilder('comments')->selectSql()->limit(50)->orderBy('id DESC')->where('server_id = \'' . $serverId . '\'')->executeQuery()->getResult();

        if (!empty($result)) {
            $comments = '<ul class="card collection" style="border: 0">';

            foreach ($result as $item) {

                $colors = [
                    'red',
                    'blue',
                    'indigo',
                    'purple',
                    'deep-orange',
                    'deep-purple',
                    'brown',
                    'black',
                    'teal',
                    'green',
                ];

                $comments .= '
                    <li class="collection-item avatar" style="min-height: 64px;">
                        <i class="material-icons circle ' . $colors[rand(0, 9)] . '">account_circle</i>
                        <b class="title">' . $item['nick'] . '</b>
                        <p>' . htmlspecialchars_decode(nl2br($item['text'])) . '
                        <br>
                        <label>' . $server->timeStampToTime($item['datetime']) . ' ' . $server->timeStampToDate($item['datetime']) . ' (UTC)</label>
                        </p>
                        ';

                        if ((isset($_COOKIE['_ym_uid']) && $_COOKIE['_ym_uid'] == $item['cookie']) || $server->getClientIp() == $item['ip']) {
                            $comments .= '<form method="post">
                                <input type="hidden" name="id" value="' . $item['id'] . '">
                                <button name="delete-comment" class="secondary-content white z-depth-0 btn" style="padding: 0 10px; border-radius: 50%;"><i class="material-icons black-text">close</i></button>
                            </form>';
                        }

                  $comments .= '</li>';
            }

            $comments .= '</ul>';

            return $comments;
        }

        return '';
    }

    public function getMpName($type) {
        switch ($type) {
            case 2:
                return '<a target="_blank" href="https://gt-mp.net/">GT-MP</a>';
            case 3:
                return '<a target="_blank" href="https://gtanet.work/">GTA:Network</a>';
            case 4:
                return '<a target="_blank" href="https://rage.mp/">Rage MP</a>';
            case 5:
                return '<a target="_blank" href="https://fivem.net/">FiveM</a>';
            case 6:
                return '<a target="_blank" href="https://gta-orange.net">Orange</a>';
            default:
                return 'Unknown';
        }
    }

    public function getMpNameNotLink($type) {
        switch ($type) {
            case 2:
                return 'GT-MP';
            case 3:
                return 'GTA:Network';
            case 4:
                return 'Rage MP';
            case 5:
                return 'FiveM';
            case 6:
                return 'Orange';
            default:
                return 'Unknown';
        }
    }

    public function getMpWebSite($type) {
        switch ($type) {
            case 2:
                return 'https://gt-mp.net/';
            case 3:
                return 'https://gtanet.work/';
            case 4:
                return 'https://rage.mp/';
            case 5:
                return 'https://fivem.net/';
            case 6:
                return 'https://gta-orange.net';
            default:
                return 'Unknown';
        }
    }

    public function getMpLogo($type) {
        switch ($type) {
            case 2:
                return 'gtmp.png';
            case 3:
                return 'gtan.png';
            case 4:
                return 'rage.png';
            case 5:
                return 'fivem.png';
            case 6:
                return 'orange.png';
            default:
                return '';
        }
    }

    public function getGameModeType($type) {
        switch ($type) {
            case 1:
                return 'Role Play';
            case 2:
                return 'Death Match';
            case 3:
                return 'Drift';
            default:
                return 'Unknown';
        }
    }
}