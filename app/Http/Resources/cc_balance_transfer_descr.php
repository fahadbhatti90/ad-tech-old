<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class cc_balance_transfer_descr
{
    var	$sum		= 0;
    var $to_length	= 0;
    var	$to			= '';

    /**
     *
     */
    function pack() {
        return pack( 'VV', $this->sum, $this->to_length ) . $this->to;
    }

    /**
     *
     */
    function unpack( $bin ) {
        $arr = unpack( 'Vsum/Vto_length', $bin );
        $this->sum			= $arr['sum'];
        $this->to_length	= $arr['to_length'];
        if( strlen( $bin ) > SIZEOF_CC_BALANCE_TRANSFER_DESC ) {
            $this->to		= substr( $bin, SIZEOF_CC_BALANCE_TRANSFER_DESC );
        } else {
            $this->to = '';
        }
    }

    /**
     *
     */
    function setSum( $sum ) {
        $this->sum = $sum;
    }

    /**
     *
     */
    function getSum() {
        return $this->sum;
    }

    /**
     *
     */
    function setTo( $to ) {
        $this->to = $to;
    }

    /**
     *
     */
    function getTo() {
        return $this->to;
    }

    /**
     *
     */
    function calcSize() {
        $this->to_length = strlen( $this->to );
        return $this->to_length;
    }

    /**
     *
     */
    function getFullSize() {
        return SIZEOF_CC_BALANCE_TRANSFER_DESC + $this->to_length;
    }


}
