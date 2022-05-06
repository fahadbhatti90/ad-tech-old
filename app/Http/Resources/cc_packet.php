<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class cc_packet
{

    var	$ver	= CC_PROTO_VER;	//	version of the protocol
    var	$cmd	= cmdCC_BYE;	//	command, see cc_cmd_t
    var	$size	= 0;			//	data size in consequent bytes
    var	$data	= '';			//	packet payload

    /**
     *
     */
    function checkPackHdr( $cmd = NULL, $size = NULL ) {
        if( $this->ver != CC_PROTO_VER )
            return FALSE;
        if( isset( $cmd ) && ($this->cmd != $cmd) )
            return FALSE;
        if( isset( $size ) && ($this->size != $size) )
            return FALSE;

        return TRUE;
    }

    /**
     *
     */
    function pack() {
        return pack( 'CCV', $this->ver, $this->cmd, $this->size ) . $this->data;
    }

    /**
     *
     */
    function packTo( $handle ) {
        return fwrite( $handle, $this->pack(), SIZEOF_CC_PACKET + strlen( $this->data ) );
    }

    /**
     *
     */
    function unpackHeader( $bin ) {
        $arr = unpack( 'Cver/Ccmd/Vsize', $bin );
        $this->ver	= $arr['ver'];
        $this->cmd	= $arr['cmd'];
        $this->size	= $arr['size'];
    }

    /**
     *
     */
    function unpackFrom( $handle, $cmd = NULL, $size = NULL ) {
        if( ($bin = stream_get_contents( $handle, SIZEOF_CC_PACKET )) === FALSE ) {
            return FALSE;
        }

        if( strlen( $bin ) < SIZEOF_CC_PACKET ) {
            return FALSE;
        }

        $this->unpackHeader( $bin );

        if( $this->checkPackHdr( $cmd, $size ) === FALSE ) {
            return FALSE;
        }

        if( $this->size > 0 ) {
            if( ($bin = stream_get_contents( $handle, $this->size )) === FALSE ) {
                return FALSE;
            }
            $this->data = $bin;
        } else {
            $this->data = '';
        }

        return TRUE;
    }

    /**
     *
     */
    function setVer( $ver ) {
        $this->ver = $ver;
    }

    /**
     *
     */
    function getVer() {
        return $this->ver;
    }

    /**
     *
     */
    function setCmd( $cmd ) {
        $this->cmd = $cmd;
    }

    /**
     *
     */
    function getCmd() {
        return $this->cmd;
    }

    /**
     *
     */
    function setSize( $size ) {
        $this->size = $size;
    }

    /**
     *
     */
    function getSize() {
        return $this->size;
    }

    /**
     *
     */
    function calcSize() {
        $this->size = strlen( $this->data );
        return $this->size;
    }

    /**
     *
     */
    function getFullSize() {
        return SIZEOF_CC_PACKET + $this->size;
    }

    /**
     *
     */
    function setData( $data ) {
        $this->data = $data;
    }

    /**
     *
     */
    function getData() {
        return $this->data;
    }

}
