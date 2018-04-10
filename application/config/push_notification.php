<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$config['sandbox'] = FALSE;

// apple ios
$config['apple']['certificate'] = $config['sandbox'] ? APPPATH.'third_party/NotificationPusher/apns-dev-certificate.pem' : APPPATH.'third_party/NotificationPusher/apns-dist-certificate.pem';
$config['apple']['passPhrase'] = $config['sandbox'] ? 'ebpearls' : 'ebpearls';

// android
$config['google']['url'] = $config['sandbox'] ? 'https://fcm.googleapis.com/fcm/send' : 'https://fcm.googleapis.com/fcm/send';
$config['google']['apiKey'] = $config['sandbox'] ? 'AAAALghgf68:APA91bFEXfc3djT9yvzaVXL9pWpnqakqjn7Kj41PoZZUTHr1zzZFpvhlqGDvUto5gQAPJWAFAfUuezn8FLoyZhMhgbF5gmP_LFMBFyc-UwoHowhy1XS0wLd1QHyGKC_7Vhfk9rsX-kMfiLqJaApC8Z3bUD30KeaXcQ' : 'AAAALghgf68:APA91bFEXfc3djT9yvzaVXL9pWpnqakqjn7Kj41PoZZUTHr1zzZFpvhlqGDvUto5gQAPJWAFAfUuezn8FLoyZhMhgbF5gmP_LFMBFyc-UwoHowhy1XS0wLd1QHyGKC_7Vhfk9rsX-kMfiLqJaApC8Z3bUD30KeaXcQ';
    