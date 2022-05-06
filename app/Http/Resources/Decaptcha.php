<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Config\decaptchaconstants;

class Decaptcha
{

    var	$status;
    var	$s;
    var $context;

    /**
     *
     */
/*    function index() {
        $this->status = 1;
    }*/
    function init() {
        //dd('here');
        //dd(1);
        $this->status = 1;

        //
    } // init()

    /**
     *
     */
    function login( $hostname, $port, $login, $pwd, $ssl = FALSE ) {
        $this->status = 1;

        $errnum = 0;
        $errstr = '';
        $transport = 'tcp';

        $this->context = stream_context_create();
        if( $ssl ) {
            $transport = 'ssl';
            $result = stream_context_set_option( $this->context, 'ssl', 'allow_self_signed', TRUE );
        }

        if(( $this->s = @stream_socket_client( "$transport://$hostname:$port", $errnum, $errstr, ini_get( "default_socket_timeout" ) , STREAM_CLIENT_CONNECT, $this->context )) === FALSE ) {
            print( 'We have a stream_socket_client() error: ' . $errstr . ' (' . $errnum . ')'."\n" );
            return -3;
        }

        $pack = new cc_packet();
        $pack->setVer( 1 );

        $pack->setCmd( 1 );
        $pack->setSize( strlen( $login ) );
        $pack->setData( $login );

        if( $pack->packTo( $this->s ) === FALSE ) {
            return -3;
        }

        if( $pack->unpackFrom( $this->s, 3, 256 ) === FALSE ) {
            return -3;
        }

        $shabuf = NULL;
        $shabuf .= $pack->getData();
        $shabuf .= md5( $pwd );
        $shabuf .= $login;

        $pack->setCmd( 4 );
        $pack->setSize( 32 );
        $pack->setData( hash( 'sha256', $shabuf, TRUE ) );

        if( $pack->packTo( $this->s ) === FALSE ) {
            return -3;
        }

        if( $pack->unpackFrom( $this->s, 7 ) === FALSE ) {
            return -3;
        }

        $this->status = 4;

        return 0;
    } // login()

    /**
     *
     */
    function picture2(
        $pict,				//	IN		picture binary data
        &$pict_to, 			//	IN/OUT	timeout specifier to be used, on return - really used specifier, see ptoXXX constants, ptoDEFAULT in case of unrecognizable
        &$pict_type, 		//	IN/OUT	type specifier to be used, on return - really used specifier, see ptXXX constants, ptUNSPECIFIED in case of unrecognizable
        &$text,				//	OUT	text
        &$major_id = NULL,	//	OUT	OPTIONAL	major part of the picture ID
        &$minor_id = NULL	//	OUT OPTIONAL	minor part of the picture ID
    ) {
        if( $this->status != 4 )
            return -2;

        $pack = new cc_packet();
        $pack->setVer( 1 );
        $pack->setCmd( 12 );

        $desc = new cc_pict_descr();
        $desc->setTimeout( 0 );
        $desc->setType( $pict_type );
        $desc->setMajorID( 0 );
        $desc->setMinorID( 0 );
        $desc->setData( $pict );
        $desc->calcSize();

        $pack->setData( $desc->pack() );
        $pack->calcSize();

        if( $pack->packTo( $this->s ) === FALSE ) {
            return -3;
        }

        if( $pack->unpackFrom( $this->s ) === FALSE ) {
            return -3;
        }

        switch( $pack->getCmd() ) {
            case 14:
                $desc->unpack( $pack->getData() );
                $pict_to	= $desc->getTimeout();
                $pict_type	= $desc->getType();
                $text		= $desc->getData();

                if( isset( $major_id ) )
                    $major_id	= $desc->getMajorID();
                if( isset( $minor_id ) )
                    $minor_id	= $desc->getMinorID();
                return 0;

            case 10:
                // balance depleted
                return -6;

            case 9:
                // server's busy
                return -5;

            case 11:
                // picture timed out
                return -7;

            case 8:
                // server's error
                return -1;

            default:
                // unknown error
                return -200;
        }

        return -200;
    } // picture2()

    /**
     *
     */
    function picture_multipart(
        $pics,				//	IN array of pictures binary data
        $questions,			//	IN array of questions
        &$pict_to, 			//	IN/OUT	timeout specifier to be used, on return - really used specifier, see ptoXXX constants, ptoDEFAULT in case of unrecognizable
        &$pict_type, 		//	IN/OUT	type specifier to be used, on return - really used specifier, see ptXXX constants, ptUNSPECIFIED in case of unrecognizable
        &$text,				//	OUT	text
        &$major_id,			//	OUT	major part of the picture ID
        &$minor_id			//	OUT minor part of the picture ID
    ) {

        if( !isset( $pics ) ) {
            // $pics - should have a pic
            return -8;
        }

        if( !is_array( $pics ) ) {
            // $pics should be an array
            $pics = array( $pics );
        }

        if( isset( $questions ) && !is_array( $questions ) ) {
            $questions = array( $questions );
        }

        $pack = '';

        switch( $pict_type ) {

            case 86:
                // ASIRRA must have ptASIRRA_PICS_NUM pictures
                if( count( $pics ) != 12 ) {
                    return -8;
                }

                // combine all images into one bunch
                $pack = '';
                foreach( $pics as &$pic ) {
                    $pack .= pack( "V", strlen( $pic ) );
                    $pack .= $pic;
                }
                break;

            case ptMULTIPART:
                // MULTIPART image should have reasonable number of pictures
                if( count( $pics ) > 20 ) {
                    return -8;
                }

                if( is_array( $questions ) && (count( $questions ) > 20) ) {
                    return -8;
                }

                // combine all images into one bunch
                $size = count( $pics ) * 4;
                foreach( $pics as &$pic ) {
                    $size += strlen( $pic );
                }

                $pack = '';
                $pack .= pack( "V", 268435455 );		// i_magic
                $pack .= pack( "V", count( $pics ) );	// N
                $pack .= pack( "V", $size );			// size
                foreach( $pics as &$pic ) {
                    $pack .= pack( "V", strlen( $pic ) );
                    $pack .= $pic;
                }

                if( is_array( $questions ) ) {
                    // combine all questions into one bunch
                    $size = count( $questions ) * 4;
                    foreach( $questions as &$question ) {
                        $size += strlen( $question );
                    }

                    $pack .= pack( "V", 268435440 );			// q_magic
                    $pack .= pack( "V", count( $questions ) );	// N
                    $pack .= pack( "V", $size );				// size
                    foreach( $questions as &$question ) {
                        $pack .= pack( "V", strlen( $question ) );
                        $pack .= $question;
                    }
                } // if( is_array( $texts ) )
                break;

            default:
                // we serve only ASIRRA multipart pictures so far
                return -8;
                break;
        } // switch( pict_type )


        return $this->picture2( $pack, $pict_to, $pict_type, $text, $major_id, $minor_id );
    } // picture_asirra()

    /**
     *
     */
    function picture_bad2( $major_id, $minor_id ) {
        $pack = new cc_packet();

        $pack->setVer( 1 );
        $pack->setCmd( 13 );

        $desc = new cc_pict_descr();
        $desc->setTimeout( 0 );
        $desc->setType( 0 );
        $desc->setMajorID( $major_id );
        $desc->setMinorID( $minor_id );
        $desc->calcSize();

        $pack->setData( $desc->pack() );
        $pack->calcSize();

        if( $pack->packTo( $this->s ) === FALSE ) {
            return -3;
        }

        return 0;
    } // picture_bad2()

    /**
     *
     */
    function balance( &$balance ) {
        if( $this->status != 4 )
            return -2;

        $pack = new cc_packet();
        $pack->setVer( 1 );
        $pack->setCmd( 10 );
        $pack->setSize( 0 );

        if( $pack->packTo( $this->s ) === FALSE ) {
            return -3;
        }

        if( $pack->unpackFrom( $this->s ) === FALSE ) {
            return -3;
        }

        switch( $pack->getCmd() ) {
            case 10:
                $balance = $pack->getData();
                return 0;

            default:
                // unknown error
                return -200;
        }
    } // balance()

    /**
     * $sum should be int
     * $to should be string
     */
    function balance_transfer( $sum, $to ) {
        if( $this->status != 4 )
            return -2;

        if( !is_int( $sum ) )
            return -8;

        if( !is_string( $to ) )
            return -8;

        if( $sum <= 0 ) {
            return -8;
        }

        $pack = new cc_packet();
        $pack->setVer( 1 );
        $pack->setCmd( 16 );

        $desc = new cc_balance_transfer_descr();
        $desc->setTo( $to );
        $desc->setSum( $sum );
        $desc->calcSize();

        $pack->setData( $desc->pack() );
        $pack->calcSize();

        if( $pack->packTo( $this->s ) === FALSE ) {
            return -3;
        }

        if( $pack->unpackFrom( $this->s ) === FALSE ) {
            return -3;
        }

        switch( $pack->getCmd() ) {
            case 7:
                return 0;

            default:
                // unknown error
                return -1;
        }

    } // balance_tansfer()

    /**
     *
     */
    function system_load( &$system_load ) {
        if( $this->status != 4 )
            return -2;

        $pack = new cc_packet();
        $pack->setVer( 1 );
        $pack->setCmd( 15 );
        $pack->setSize( 0 );

        if( $pack->packTo( $this->s ) === FALSE ) {
            return -3;
        }

        if( $pack->unpackFrom( $this->s ) === FALSE ) {
            return -3;
        }

        if( $pack->getSize() != 1 ) {
            return -200;
        }

        switch( $pack->getCmd() ) {
            case 15:
                $arr = unpack( 'Csysload', $pack->getData() );
                $system_load = $arr['sysload'];
                return 0;

            default:
                // unknown error
                return -200;
        }
    } // system_load()

    /**
     *
     */
    function close() {
        $pack = new cc_packet();
        $pack->setVer( 1 );

        $pack->setCmd( 2 );
        $pack->setSize( 0 );

        if( $pack->packTo( $this->s ) === FALSE ) {
            // return -3;
        }

        fclose( $this->s );
        $this->status = 1;

        return -3;
    } // close()

    /**
     *
     */
    function closes() {
        $pack = new cc_packet();
        $pack->setVer( 1 );

        $pack->setCmd( 7 );
        $pack->setSize( 0 );

        if( $pack->packTo( $this->s ) === FALSE ) {
            return -3;
        }

        if( $pack->unpackFrom( $this->s, 7 ) === FALSE ) {
            // return -3;
        }

        fclose( $this->s );
        $this->status = 1;

        return -3;
    } // close()

    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////

    /**
     *	deprecated functions section. still operational, but better not to be used
     */

    /**
     *
     */
    function picture( $pict, &$text ) {
        if( $this->status != 4 )
            return -2;

        $pack = new cc_packet();
        $pack->setVer( 1 );

        $pack->setCmd( 5 );
        $pack->setSize( strlen( $pict ) );
        $pack->setData( $pict );

        if( $pack->packTo( $this->s ) === FALSE ) {
            return -3;
        }

        if( $pack->unpackFrom( $this->s ) === FALSE ) {
            return -3;
        }

        switch( $pack->getCmd() ) {
            case 6:
                $text = $pack->getData();
                return 0;

            case 10:
                // balance depleted
                return -6;

            case 9:
                // server's busy
                return -5;

            case 11:
                // picture timed out
                return -7;

            case 8:
                // server's error
                return -1;

            default:
                // unknown error
                return -200;
        }
    } // picture()

    /**
     *
     */
    function picture_bad() {
        $pack = new cc_packet();
        $pack->setVer( 1 );

        $pack->setCmd( 8 );
        $pack->setSize( 0 );

        if( $pack->packTo( $this->s ) === FALSE ) {
            return -3;
        }

        return -3;
    } // picture_bad()
}
