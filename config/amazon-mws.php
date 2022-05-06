<?php

return [
	'store' => [
		'store1' => [
			'merchantId' => '',
			'marketplaceId' => '',
			'keyId' => '',
			'secretKey' => '',
            'authToken' => '',
			//'amazonServiceUrl' => 'https://mws-eu.amazonservices.com/'
            'amazonServiceUrl' => 'https://mws.amazonservices.com/'
            /** Optional settings for SOCKS5 proxy
             *
            'proxy_info' => [
                'ip' => '127.0.0.1',
                'port' => 8080,
                'user_pwd' => 'user:password',
            ],
             */
		]
	],

	// Default service URL
	'AMAZON_SERVICE_URL' => 'https://mws.amazonservices.com/',

	'muteLog' => false
];
/*'merchantId' => 'A3MEMZVRRLCL7A',
			'marketplaceId' => 'ATVPDKIKX0DER',
			'keyId' => 'AKIAI4VW7BAXJPB72M5A',
			'secretKey' => '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',
            'authToken' => 'amzn.mws.54b37aea-faaf-9487-9e3d-6c1ae65d4ab4',
			//'amazonServiceUrl' => 'https://mws-eu.amazonservices.com/'
            'amazonServiceUrl' => 'https://mws.amazonservices.com/'*/