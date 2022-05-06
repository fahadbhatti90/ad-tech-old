<?php

//	ERROR CODES

if( !defined( 'ccERR_OK'		) )	define( 'ccERR_OK',			0		);	//	everything went OK
if( !defined( 'ccERR_GENERAL'	) )	define( 'ccERR_GENERAL',	-1		);	//	general internal error
if( !defined( 'ccERR_STATUS'	) )	define( 'ccERR_STATUS',		-2		);	//	status is not correct
if( !defined( 'ccERR_NET_ERROR'	) )	define( 'ccERR_NET_ERROR',	-3		);	//	network data transfer error
if( !defined( 'ccERR_TEXT_SIZE'	) )	define( 'ccERR_TEXT_SIZE',	-4		);	//	text is not of an appropriate size
if( !defined( 'ccERR_OVERLOAD'	) )	define( 'ccERR_OVERLOAD',	-5		);	//	server's overloaded
if( !defined( 'ccERR_BALANCE'	) )	define( 'ccERR_BALANCE',	-6		);	//	not enough funds to complete the request
if( !defined( 'ccERR_TIMEOUT'	) )	define( 'ccERR_TIMEOUT',	-7		);	//	request timed out
if( !defined( 'ccERR_BAD_PARAMS') )	define( 'ccERR_BAD_PARAMS',	-8		);	//	provided parameters are not good for this function
if( !defined( 'ccERR_UNKNOWN'	) )	define( 'ccERR_UNKNOWN',	-200	);	//	unknown error

//	picture processing TIMEOUTS
if( !defined( 'ptoDEFAULT'		) )	define( 'ptoDEFAULT',		0		);	//	default timeout, server-specific
if( !defined( 'ptoLONG'			) )	define( 'ptoLONG',			1		);	//	long timeout for picture, server-specfic
if( !defined( 'pto30SEC'		) )	define( 'pto30SEC',			2		);	//	30 seconds timeout for picture
if( !defined( 'pto60SEC'		) )	define( 'pto60SEC',			3		);	//	60 seconds timeout for picture
if( !defined( 'pto90SEC'		) )	define( 'pto90SEC',			4		);	//	90 seconds timeout for picture

//	picture processing TYPES
if( !defined( 'ptUNSPECIFIED'		) )	define( 'ptUNSPECIFIED',		0	);	//	unspecified
if( !defined( 'ptASIRRA'			) )	define( 'ptASIRRA',				86	);	//	ASIRRA pictures
if( !defined( 'ptTEXT'				) )	define( 'ptTEXT',				83	);	//	TEXT questions
if( !defined( 'ptMULTIPART'			) )	define( 'ptMULTIPART',			82	);	//	MULTIPART quetions

// multi-picture processing specifics
if( !defined( 'ptASIRRA_PICS_NUM'	) ) define( 'ptASIRRA_PICS_NUM',	12	);
if( !defined( 'ptMULTIPRAT_PICS_NUM') ) define( 'ptMULTIPART_PICS_NUM',	20	);




if( !defined( 'CC_PROTO_VER'			) )	define(	'CC_PROTO_VER',				1		);		//	protocol version
if( !defined( 'CC_RAND_SIZE'			) )	define(	'CC_RAND_SIZE',				256		);		//	size of the random sequence for authentication procedure
if( !defined( 'CC_MAX_TEXT_SIZE'		) )	define(	'CC_MAX_TEXT_SIZE',			100		);		//	maximum characters in returned text for picture
if( !defined( 'CC_MAX_LOGIN_SIZE'		) )	define(	'CC_MAX_LOGIN_SIZE',		100		);		//	maximum characters in login string
if( !defined( 'CC_MAX_PICTURE_SIZE'		) )	define(	'CC_MAX_PICTURE_SIZE',		200000	);		//	200 K bytes for picture seems sufficient for all purposes
if( !defined( 'CC_HASH_SIZE'			) )	define(	'CC_HASH_SIZE',				32		);

if( !defined( 'cmdCC_UNUSED'			) )	define( 'cmdCC_UNUSED',				0		);
if( !defined( 'cmdCC_LOGIN'				) )	define(	'cmdCC_LOGIN',				1		);		//	login
if( !defined( 'cmdCC_BYE'				) )	define(	'cmdCC_BYE',				2		);		//	end of session
if( !defined( 'cmdCC_RAND'				) )	define(	'cmdCC_RAND',				3		);		//	random data for making hash with login+password
if( !defined( 'cmdCC_HASH'				) )	define(	'cmdCC_HASH',				4		);		//	hash data
if( !defined( 'cmdCC_PICTURE'			) )	define(	'cmdCC_PICTURE',			5		);		//	picture data, deprecated
if( !defined( 'cmdCC_TEXT'				) )	define(	'cmdCC_TEXT',				6		);		//	text data, deprecated
if( !defined( 'cmdCC_OK'				) )	define(	'cmdCC_OK',					7		);		//
if( !defined( 'cmdCC_FAILED'			) )	define(	'cmdCC_FAILED',				8		);		//
if( !defined( 'cmdCC_OVERLOAD'			) )	define(	'cmdCC_OVERLOAD',			9		);		//
if( !defined( 'cmdCC_BALANCE'			) )	define(	'cmdCC_BALANCE',			10		);		//	zero balance
if( !defined( 'cmdCC_TIMEOUT'			) )	define(	'cmdCC_TIMEOUT',			11		);		//	time out occured
if( !defined( 'cmdCC_PICTURE2'			) )	define( 'cmdCC_PICTURE2',			12		);		//	picture data
if( !defined( 'cmdCC_PICTUREFL'			) )	define( 'cmdCC_PICTUREFL',			13		);		//	picture failure
if( !defined( 'cmdCC_TEXT2'				) )	define( 'cmdCC_TEXT2',				14		);		//	text data
if( !defined( 'cmdCC_SYSTEM_LOAD'		) )	define( 'cmdCC_SYSTEM_LOAD',		15		);		//	system load
if( !defined( 'cmdCC_BALANCE_TRANSFER'	) )	define(	'cmdCC_BALANCE_TRANSFER',	16		);		//	zero balance

if( !defined( 'SIZEOF_CC_PACKET'				) )	define( 'SIZEOF_CC_PACKET',					6			);
if( !defined( 'SIZEOF_CC_PICT_DESCR'			) )	define(	'SIZEOF_CC_PICT_DESCR',				20			);
if( !defined( 'SIZEOF_CC_BALANCE_TRANSFER_DESC'	) )	define(	'SIZEOF_CC_BALANCE_TRANSFER_DESC',	8			);

if( !defined( 'CC_I_MAGIC'	) )	define( 'CC_I_MAGIC',						268435455	);
if( !defined( 'CC_Q_MAGIC'	) )	define( 'CC_Q_MAGIC',						268435440	);

defined('DECAPTURE_HOST')     OR define('DECAPTURE_HOST','api.de-captcher.com');
defined('DECAPTURE_USERNAME')     OR define('DECAPTURE_USERNAME','helotek');
defined('DECAPTURE_PASSWORD')     OR define('DECAPTURE_PASSWORD','2$xLKuso*i!7');
defined('DECAPTURE_PORT')     OR define('DECAPTURE_PORT',	36541);
defined('CAPTCHA_STATUS')     	OR define('CAPTCHA_STATUS',-1);
defined('VENDOR_DOMAIN')     OR define('VENDOR_DOMAIN',	'vendorcentral.amazon.com');

ini_set( 'max_execution_time', 0 );

define(	'sCCC_INIT',		1		);		//	initial status, ready to issue LOGIN on client
define(	'sCCC_LOGIN',		2		);		//	LOGIN is sent, waiting for RAND (login accepted) or CLOSE CONNECTION (login is unknown)
define(	'sCCC_HASH',		3		);		//	HASH is sent, server may CLOSE CONNECTION (hash is not recognized)
define(	'sCCC_PICTURE',		4		);

//require_once( 'api_consts.inc.php' );

