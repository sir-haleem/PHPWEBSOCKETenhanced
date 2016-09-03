<?php

require_once("/ServerEvents.php");
require_once("/DefinedEvents.php");

class Events extends ServerEvents
{
    
    const EVENT_BEFORE_CONNECT = 'beforeConnect';
    
    const EVENT_AFTER_CONNECT = 'afterConnect';
    
    const EVENT_WHILE_CONNECTING = 'whileConnecting';
    
    const EVENT_BEFORE_DISCONNECT = 'beforeDisconnect'; 
       
    const EVENT_AFTER_DISCONNECT = 'afterDisconnect';    
    
    public $defined_event;
    
    
    function __construct(PHPWebSocket $phpsocket)
    {
        parent::__construct($phpsocket);
        
        $this->defined_event = new DefinedEvents($this->socket, $this);
    }
    
    /*
    ** Override this to handle these events
    */
    
    public static function beforeConnect(PHPWebSocket $phpsocket, WebSocketUser $user) {
        
    }
    
    public static function afterConnect(PHPWebSocket $phpsocket, WebSocketUser $user) {
        
    }
    
    public static function whileConnecting(PHPWebSocket $phpsocket, WebSocketUser $user) {
        
    }
    
    public static function beforeDisconnect(PHPWebSocket $phpsocket, WebSocketUser $user) {
        
    }
    
    public static function afterDisconnect(PHPWebSocket $phpsocket, WebSocketUser $user) {
        
    }
    
}
