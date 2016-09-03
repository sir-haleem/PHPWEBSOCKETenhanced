<?php 

class DefinedEvents
{
    /*
    ** This class contains some predefined functions that add events to PHPSocket reference
    ** variable. The events emits and broadcasts some events. A good look at the functions docs
    ** can introduce the sent messages.
    
    ** There is no need to initilize this class as its already initialized in PHPWebSocket
    ** It can be accessed with...
    ** `php`````
    
    ** $socket = new PHPWebSocket($address, $port);
    ** $socket->event->defined_events->connect();
    
    ** .....
    ** The above code will initialize the connect event and emits a connect message
    */
    private $socket; 
    
    function __construct($phpsocket, $event)
    { 
        $this->socket = $phpsocket;
    }
    
    /*
    ** Handles the "connect" event invoked by the client and emits it to all
    ** @emitMessage "connect"
    **
    */
    
    public function connect() {
        $this->socket->event->addEvent("connect",function($socket,$uid) {
            $socket->event->emitMessage('connect',$uid);
        });
    }
    
    /*
    ** Handles the "disconnect" event invoked by the client and broadcast it to all
    ** @broadcastMessage "user left"
    ** 
    */
    
    public function disconnect() {
        $this->socket->event->addEvent("disconnect",function($socket,$data) {
            // remove the username from global usernames list
            if ($socket->addedUser) {
                unset($socket->usernames[$socket->username]);
                unset($socket->socketID[$socket->user->id]);
                $socket->numUsers--;

				// echo globally that this client has left
				$socket->event->broadcastMessage('user left', array(
					'username'=> $socket->username,
					'numUsers'=> $socket->numUsers
				));
            
            }
        
        });
    }
    
    /* 
    ** Handles the "add_user" event invoked by the client. Emits and broadcasts it to all
    ** @emitMessage "login"
    ** @broadcastMessage "user joined"
    */
    
    public function add_user () {
        $this->socket->event->addEvent("add user",function($socket, $username,$userid) {
            $socket->username=$username;
            
            //add the client's username to the global list
            $socket->usernames["$username"] = $username;
            $socket->socketID["$userid"] = $username;
            $socket->numUsers++;
            
            //inform me that my login was successful
            $socket->event->emitMessage('login', array(
            'numUsers'=>$socket->numUsers,      
            'users' =>$socket->ids_to_username_keys(),
            ));

            //broadcast to others that i have joined
            $socket->event->broadcastMessage('user joined', array(
            'username'=>$socket->username,
            'numUsers'=>$socket->numUsers,
            'usersnames' =>$socket->ids_to_username_keys()
            ));
            
            $socket->addedUser=true;

        });

    }
    
    /*
    ** Handles the typing event invoked by the client and broadcast it to all
    ** @emitMessage "typing"
    ** 
    */
    public function typing () {

        // when the client emits 'typing', we broadcast it to others
        $this->socket->event->addEvent('typing', function ($socket,$data) {
            $socket->event->broadcastMessage('typing', array(
                'username'=>$socket->socketID[$socket->user->id],
            ));

        });

    }
    
    /*
    ** Handles the stop typing event invoked by the client and broadcast it to all
    ** @emitMessage "stop typing"
    ** 
    */
    public function stop_typing () {

        // when the client emits 'stop typing', we broadcast it to others
        $this->socket->event->addEvent('stop typing', function ($socket,$data) {

            $socket->event->broadcastMessage('stop typing', array(
                'username'=>$socket->socketID[$socket->user->id],
            ));

        });

    }
    
    /*
    ** Handles the chat message event invoked by the client and broadcast it to all
    ** @broadcastMessage "chat message"
    ** 
    */
    public function chat_message () {
        //for client 2 example only
        $this->socket->event->addEvent('chat message', function ($socket,$data,$sender) {
            $socket->event->broadcastMessage('chat message', $data,true);
        });
    }
    
    
    /*
    ** Handles the 'new message' event invoked by the client and broadcast it to all
    ** @broadcastMessage "new message"
    ** 
    */
    public function new_message () {
        // when the client emits 'new message', this listens and executes
        $this->socket->event->addEvent('new message', function ($socket,$data,$sender) {
            // we tell the client to execute 'new message'
            $socket->event->broadcastMessage('new message', array(
            'username'=>$socket->socketID[$socket->user->id],
            'message'=>$data
            ));
            
        });


    }
    
    public function get_all_users () {
        
        $this->socket->event->addEvent('all users', function ($socket, $data, $sender) {
            $socket->event->broadcastMessage('all users', $socket->get_all_users() );$socket->get_all_usernames();
        });
    }
    
}