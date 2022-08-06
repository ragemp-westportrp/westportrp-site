<?php

namespace Server\Core;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * Constant class
 */
class EnumConst
{
    const VERSION = '2.1';
    /*
    * ERRORS
    */
    const ERROR_EMAIL_IS_CREATE = 'This email is create';
    const ERROR_SQL_ARRAY = 'Error params';

    /*
    * DATA BASE CONNECT PARAMS
    */
    const DB_HOST = '';
    const DB_NAME = '';
    const DB_USER = '';
    const DB_PASS = '';

    /*
    * DATA BASE TABLE
    */
    const NEWS = 'h_news';
    const STATS = 'fw_statistic';
    const STATS_DAY = 'fw_stats_day';
    const USERS = 'users';
    const INVITES = 'a_invites';
    const USER_BLOG = 'a_user_blog';
    const USER_NEWS = 'h_user_news';
    const USER_NEWS_COMMENTS = 'h_user_news_comments';
    const NOTIFICATION = 'h_notification';
    const FRIENDS = 'h_friends';
    const DIALOGS = 'h_dialogs';
    const CHANGELOG = 'h_changelog';
    const COMMENTS_NEWS = 'h_comments';
    const PARTNER = 'h_rerrers';
    const CONTACTS = 'h_contacts';
    const USER_LIKES = 'h_user_like_news';
    const POST = 'h_post';
    const POST_LIKES = 'h_post_like';
    const REPORTS = 'h_reports';
    const QUOTE = 'h_quote';
    const CITY = 'h_city';
    const CITY_NEWS = 'h_city_news';

    /*
	* NAMESPACE
	*/
    const NS_USER = 'user:';
    const NS_FRIENDS = 'friends:';
    const NS_MESSAGE = 'messages:';

    /*
    * DATA BASE COLUMN NAME
    */
    const ID = 'id';

    const U_NAME = 'name';
    const U_SURNAME = 'surname';
    const U_LASTNAME = 'lastname';
    const U_EMAIL = 'email';
    const U_LOGIN = 'login';
    const U_PASSWORD = 'password';
    const U_TOKEN = 'token';

    const ST_IP = 'ip';
    const ST_COUNT = 'count';
    const ST_LAST_CONNECT = 'last_connect';
    const ST_REFERER = 'http_referer';

    const ST_D_ALL_COUNT = 'all_count';
    const ST_D_COUNT = 'count';
    const ST_D_DAY = 'day';
    const ST_D_MONTH = 'month';
    const ST_D_YEAR = 'year';
}