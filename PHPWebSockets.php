<?php 
require("/../events/Events.php");
require_once("/WebSocketServer.php");
require_once("/WebSocketUser.php");


class PHPWebSocket  extends WebSocketServer{
    public $commands;
    
    public $event; // a reference to the class event initialized in the __construct function
    
    const CONNECTED = 'connected';
    
    const DISCONNECTED = 'disconnected';

    public $usernames=Array();
    public $socketID=Array();
    public $numUsers=0;
    
    function __construct($addr, $port, $bufferLength = 2048)
    {
        parent::__construct($addr, $port, $bufferLength = 2048);
        
        $this->event = new Events($this);
        
        $this->commands = $this->event;
    }
    
    protected function process ($user, $message)
    {
        $this->user=$user;

        $message=json_decode($message);

        $message->data= isset($message->data) ? $message->data : '';
        $message->sender= isset($message->sender) ? $message->sender : 0;

        if($message->broadcast) {
            //broadcast message
            $message->broadcast=false;
            $data=json_encode($message);

            foreach($this->users as $user) {
                $this->send($user, $data);
            }

        } else {
            //non-broadcast message
            $this->event->invoke($message->cmd, $this, $message->data,$message->sender);
        }
    }

    /**
    This is run when socket connection is established. Send a greeting message
     */
    protected function connected ($user)
    {
        $this->user=$user;
        $this->event->invoke('connect', $this, $user->id);
    }

    protected function disconnect($socket, $triggerClosed = true, $sockErrNo = null) {
        $this->event->invoke("disconnect", $this, $this->user->id);
        parent::disconnect($socket, $triggerClosed, $sockErrNo);
    }

    /**
    This is where cleanup would go, in case the user had any sort of
    open files or other objects associated with them.  This runs after the socket 
    has been closed, so there is no need to clean up the socket itself here.
     */
    protected function closed ($user)
    {
    }

    //send message to current user
    
    

    //get all user ids
    public function get_all_users() {
        $users=Array();
        foreach($this->users as $user) {
            $users[]=$user->id;
        }
        return $users;
    }

    public function listen() {
        try {
            $this->run();
        }
        catch (Exception $e) {
            $this->stdout($e->getMessage());
        }
    }
    
    public function get_all_usernames() {
        
        return $this->usernames;
    }
    
    public function get_all_socketIDs() {
        
        return $this->socketID;
    }
    
    private function userid_to_indexed_array() {
        $new_ids = [];
        $length = 0;        
        
        foreach ($this->get_all_users() as $id) {
            $new_ids[$length] = $id;
            $length++;
        }
        
        return $new_ids;
    }
    
    private function usernames_to_indexed_array() {
        $new_usernames = [];
        $length = 0;        
        
        foreach ($this->usernames as $username) {
            $new_usernames[$length] = $username;
            $length++;
        }
        
        return $new_usernames;
    }
    
    public function ids_to_username_keys() {
        $users_list = [
            'ids' => $this->userid_to_indexed_array(),
            'usernames' => $this->usernames_to_indexed_array(),
        ];
        
        
        return $users_list;
        
    }

}