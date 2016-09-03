<?php
/**
 * Created by PhpStorm.
 * User: Haleem
 * Date: 09-Aug-16
 * Time: 3:59 AM
 */

/*
From https://github.com/ghedipunk/PHP-Websockets
*/

class WebSocketUser {
    public $socket;
    public $id;
    public $headers = array();
    public $handshake = false;

    public $handlingPartialPacket = false;
    public $partialBuffer = "";

    public $sendingContinuous = false;
    public $partialMessage = "";

    public $hasSentClose = false;

    function __construct($id, $socket) {
        $this->id = $id;
        $this->socket = $socket;
    }
}