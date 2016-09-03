<?php 

abstract class ServerEvents
{
    
    public $eventNames = [];    
    
    protected $current;
    
    protected $previous;
    
    protected $socket;

    
    
    function __construct(PHPWebSocket $phpsocket)
    {
        $this->socket = $phpsocket;
    }
    
    /*
    ** Takes an array with event names as key and equivalent anonymous function as key
    ** ```php
    **      $socket = new PHPWebSocket($address, $port);
    **      $socket->event->registerEvents([
    **          'add new' => function($socket, $params, $sender=null){
                    return ;
    **          },
    **     ]);
    */
    
    public function registerEvents($name = []) {
        
        foreach ($name as $event) { 
            $this->addEvent($event, $name["$event"]);
        }
        
    }
    
    public function addEvent($name, $callback) 
    {
        $this->eventNames["$name"] = $callback;
    }
    
    /*
    ** Implement these abstract methods if you are extending directly from this class
    ** Its is highly recommended to extend the Event class for more features. 
    */
    
    abstract public  static function beforeConnect(PHPWebSocket $phpsocket, WebSocketUser $user) ;
    
    abstract public static function whileConnecting(PHPWebSocket $phpsocket, WebSocketUser $user) ;
    
    abstract public static function afterConnect(PHPWebSocket $phpsocket, WebSocketUser $user) ;
    
    abstract public static function beforeDisconnect(PHPWebSocket $phpsocket, WebSocketUser $user) ;
    
    abstract public static function afterDisconnect(PHPWebSocket $phpsocket, WebSocketUser $user) ;
    
    
    /*
    ** Main use is to execute an event emit by a client (The event must be registered on the server)
    ** 
    ** When the client emits typing,
    ** It can be handled by the server as:
    ** ```php
    ** $socket = new PHPWebSocket($addr, $port);
    ** $socket->event->invoke('typing', $socket, $params, $sender);
    ** 
    ** The server will then executes this event 
    */
    public function invoke($name, $socket, $params = '', $sender = null) {
        if (!isset($this->eventNames["$name"])) // if the event name is invalid we return
            return ;
        
        $this->eventNames["$name"]($socket, $params, $sender);
    }
    
    /*
    ** Brodcast message to the current client
    */
    public function emitMessage($eventName,$data) {
        $this->socket->send($this->socket->user, $this->cmdwrap($eventName,$data));
    }

    private function cmdwrap($cmd,$data,$sender=null) {
        $response=array('cmd'=>$cmd,'data'=>$data,'sender'=>$this->socket->user->id);
        return json_encode($response);
    }

    /*
    ** Brodcast message to all the clients
    */
    public function broadcastMessage($cmd,$data,$self=false) {
        $data=$this->cmdwrap($cmd,$data);

        foreach($this->socket->users as $user) {
            if(!$self && $user==$this->socket->user) {continue;}
            $this->socket->send($user, $data);
        }
    }
    
}