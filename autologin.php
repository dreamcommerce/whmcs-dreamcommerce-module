<?php

if (!file_exists(dirname(__FILE__) . '/modules/servers/DreamCommerce/config.php')) {
    die;
}
$config = require_once dirname(__FILE__) .'/modules/servers/DreamCommerce/config.php';
$config = $config['whmcs'];

date_default_timezone_set($config['timezone']);

# Define WHMCS URL & AutoAuth Key
$whmcsurl = "/dologin.php";
$autoauthkey = $config['auto_auth_key']; #AutoAuth Key, ATTENTION: should be defined in configuration.php aswell!
$secret_key = $config['secret_key']; #Should be same as you defined in email template
$id = (int) $_GET['id'];
$promocode = $_GET['promocode'];
$product_name = $_GET['product_name'];
$plan = $_GET['plan'];
$email = $_GET['email']; # Clients Email Address to Login

if (isset($_GET['pid'])) {
    $pid = (int) $_GET['pid'];
} elseif (isset($plan)) {
    foreach ($config['products'] as $key => $val) {
        if ($plan == $key) {
            $pid = (int) $val['pid'];
        }
    }
} elseif (isset($product_name)) {
    foreach ($config['products'] as $product) {
        if ($product_name == $product['name']) {
            $pid = (int) $product['pid'];
        }
    }
} else {
    die;
}

if (md5($email . $secret_key) != $_GET['hash']) {
    die;//dying here because hash is not equal
}

$timestamp = time(); # Get current timestamp

if (in_array($pid, array(
    $config['products']['gold']['pid'],
    $config['products']['platinium']['pid'],
    $config['products']['diamond']['pid'],
))) {
    $goto = "upgrade.php?type=package&id=" . $id . "&billingcycle=annually&pid=" . $pid . "&promocode=" . $promocode . "&step=2"; # Here you can set default user page
} else {
    $goto = 'cart.php?a=add&pid=' . $pid;

    if (isset($_GET['license'])) {
        $license = $_GET['license'];
        $goto .= '&cf_license=' . $license;
    }
}

$hash = sha1($email . $timestamp . $autoauthkey); # Generate Hash

# Generate AutoAuth URL & Redirect
$url = $whmcsurl . "?email=$email&timestamp=$timestamp&hash=$hash&goto=" . urlencode($goto);
header("Location: $url");
exit;
