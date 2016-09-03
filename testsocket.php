<?php 
require_once('/server/sockets/PHPWebSockets.php');
$address = "0.0.0.0";

/**
 * The port to run this socket on
 */
$server_port="2000";

//initialize socket service
$socket=new PHPWebSocket($address,$server_port);


$socket->create_socket();
$socket->event->defined_event->connect();
$socket->event->defined_event->disconnect();
$socket->event->defined_event->add_user();
$socket->event->defined_event->typing();
$socket->event->defined_event->stop_typing();
$socket->event->defined_event->chat_message();
$socket->event->defined_event->new_message();
$socket->event->defined_event->get_all_users();


$socket->listen();