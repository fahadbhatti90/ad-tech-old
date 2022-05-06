<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class cc_pict_descr
{
    var	$timeout	= ptoDEFAULT;
    var	$type		= ptUNSPECIFIED;
    var	$size		= 0;
    var	$major_id	= 0;
    var	$minor_id	= 0;
    var $data		= '';

    /**
     *
     */
    function pack() {
        return pack( 'VVVVV', $this->timeout, $this->type, $this->size, $this->major_id, $this->minor_id ) . $this->data;
    }

    /**
     *
     */
    function unpack( $bin ) {
        $arr = unpack( 'Vtimeout/Vtype/Vsize/Vmajor_id/Vminor_id', $bin );
        $this->timeout	= $arr['timeout'];
        $this->type		= $arr['type'];
        $this->size		= $arr['size'];
        $this->major_id	= $arr['major_id'];
        $this->minor_id	= $arr['minor_id'];
        if( strlen( $bin ) > SIZEOF_CC_PICT_DESCR ) {
            $this->data		= substr( $bin, SIZEOF_CC_PICT_DESCR );
        } else {
            $this->data = '';
        }
    }

    /**
     *
     */
    function setTimeout( $to ) {
        $this->timeout = $to;
    }

    /**
     *
     */
    function getTimeout() {
        return $this->timeout;
    }

    /**
     *
     */
    function setType( $type ) {
        $this->type = $type;
    }

    /**
     *
     */
    function getType() {
        return $this->type;
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
        return SIZEOF_CC_PICT_DESCR + $this->size;
    }

    /**
     *
     */
    function setMajorID( $major_id ) {
        $this->major_id = $major_id;
    }

    /**
     *
     */
    function getMajorID() {
        return $this->major_id;
    }

    /**
     *
     */
    function setMinorID( $minor_id ) {
        $this->minor_id = $minor_id;
    }

    /**
     *
     */
    function getMinorID() {
        return $this->minor_id;
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
