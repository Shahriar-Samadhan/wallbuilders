<?php

namespace Samadhan;


use WC_API_Client;

class GetOrder
{

    protected static function smdn_order_API_call()
    {

        $consumer_key = get_option('SAMADHAN_STORE_CONSUMER_KEY');
        $consumer_secret = get_option('SAMADHAN_STORE_CONSUMER_SECRET');
        $store_url = get_option('SAMADHAN_STORE_API_ENDPOINT');

        $wc_api = new WC_API_Client($consumer_key, $consumer_secret, $store_url, true);

        return $wc_api;


    }
}

new GetOrder();
