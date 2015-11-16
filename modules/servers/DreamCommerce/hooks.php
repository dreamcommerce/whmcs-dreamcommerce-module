<?php

include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.php';
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'DreamCommerce_Loader.php';

$config = require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.php';

add_hook('AcceptOrder', 1, function (array $params) use ($config) {
    $log = dirname(__FILE__) . '/hooks.log.txt';

    $orders = localAPI('getorders', array(
        'id' => $params['orderid'],
    ));

    if ($orders['totalresults'] == 1) {
        $order = $orders['orders']['order'][0];
        $item = $order['lineitems']['lineitem'][0];

        if ($item['producttype'] == 'Other Product/Service' && strpos($item['product'], 'Designing Services') === 0) {
            $products = localAPI('getclientsproducts', array(
                'clientid' => $order['userid'],
                'serviceid' => $item['relid'],
            ));

            if ($products['totalresults'] == 1) {
                $product = $products['products']['product'][0];
                if (isset($product['customfields']['customfield'][0])) {
                    $field = $product['customfields']['customfield'][0];

                    if ($field['name'] == 'license') {
                        $license = $field['value'];

                        $api = new DreamCommerce_API($config['lms']['host'], $config['lms']['user'], $config['lms']['pass'], false);

                        try {
                            $api->testConnection();
                            $api->login();
                            $api->installLicenseSkin(null, $license, $product['pid']);

                            file_put_contents($log, date('Y-m-d H:i:s') . ' InvoicePaid SUCCESS: skin: ' . $product['pid'] . ' license: ' . $license . PHP_EOL, FILE_APPEND);
                        } catch (Exception $e) {
                            file_put_contents($log, date('Y-m-d H:i:s') . ' InvoicePaid ERROR: skin: ' . $product['pid'] . ' license: ' . $license . PHP_EOL . 'Exception: ' . $e . PHP_EOL, FILE_APPEND);
                        }
                    }
                }
            }
        }
    }
});

add_hook('InvoicePaid', 1, function(array $params) use ($config) {
    $log = dirname(__FILE__) . '/hooks.log.txt';

    $invoice = localAPI('getinvoice', array(
        'invoiceid' => $params['invoiceid'],
    ));

    $products = localAPI('getclientsproducts', array(
        'clientid' => $invoice['userid'],
        'serviceid' => $invoice['items']['item'][0]['relid'],
    ));

    if ($products['totalresults'] == 1) {
        $product = $products['products']['product'][0];
        if (isset($product['customfields']['customfield'][0])) {

            $field = $product['customfields']['customfield'][0];

            if ($field['name'] == 'license') {
                $license = $field['value'];

                $api = new DreamCommerce_API($config['lms']['host'], $config['lms']['user'], $config['lms']['pass'], false);
                try {
                    $api->testConnection();
                    $api->login();
                    $api->installLicenseSkin(null, $license, $product['pid']);

                    file_put_contents($log, date('Y-m-d H:i:s') . ' InvoicePaid SUCCESS: skin: ' . $product['pid'] . ' license: ' . $license . PHP_EOL, FILE_APPEND);
                } catch (Exception $e) {
                    file_put_contents($log, date('Y-m-d H:i:s') . ' InvoicePaid ERROR: skin: ' . $product['pid'] . ' license: ' . $license . PHP_EOL . 'Exception: ' . $e . PHP_EOL, FILE_APPEND);
                }
            }
        }
    }
});
