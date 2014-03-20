<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
require_once('flight/Flight.php');
require_once('model/db_picas_fijas.class.php');

/*
 * Register Class operant in two databases
 */
//Flight::register('picasFijas','PicasFijas');

class WebService
{
  /**
   * 
   */
  public static function crear_cuenta() {
    $picasFijas         = new PicasFijas();
    $request            = Flight::request();
    $user               = $request->data['user'];
    $password           = $request->data['password'];
    $mail               = $request->data['mail'];

    $output             = array();
    $output['status']   = true;
    $output['data']     = $picasFijas->crear_cuenta($user, $mail, $password);

    Flight::json($output);
  }
  public static function autenticate_user(){
    $picasFijas         = new PicasFijas();
    $request            = Flight::request();
    $user               = $request->data['user'];
    $password           = $request->data['password'];

    $output             = array();
    $output['status']   = true;
    $output['data']     = $picasFijas->autenticate_user($user, $password);

    Flight::json($output);
  }
  public static function validate_code(){
    $picasFijas         = new PicasFijas();
    $request            = Flight::request();
    $user               = $request->data['user_id'];
    $code               = $request->data['code'];

    $output             = array();
    $output['status']   = true;
    $output['data']     = $picasFijas->validate_code($user, $code);

    Flight::json($output); 
  }
  public static function delete_active_game(){
    $picasFijas         = new PicasFijas();
    $request            = Flight::request();
    $user               = $request->data['user_id'];
    $output             = array();
    $output['status']   = true;
    $output['data']     = $picasFijas->delete_active_game($user);

    Flight::json($output); 
  }

}

/*
 * Definición de la rutas o urls
 */
Flight::route('POST /crear_cuenta', array('WebService','crear_cuenta'));
Flight::route('POST /autenticate_user', array('WebService','autenticate_user'));
Flight::route('POST /validate_code', array('WebService','validate_code'));
Flight::route('POST /delete_active_game', array('WebService','delete_active_game'));


Flight::start();
?>
