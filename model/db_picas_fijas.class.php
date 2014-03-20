<?php
require_once('db.inc.php');
class PicasFijas extends Db
{
  
  public function __construct()
  {
    parent::init('localhost','root','1qazxsw2','picas_fijas');	
  }

  public function crear_cuenta($user, $mail, $password)
  {
    $fields     = array('user'); 
    $conditions = array(array('field'=>'user', 'condition'=>'=', 'comparation'=>$user));
    // Se valida que no exista un usuario con el mismo user
    $exist      = parent::dbSelect('users', $fields, $conditions);
    if(empty($exist))
    {
      $values = array('user'=>$user, 'mail'=> $mail, 'password'=> md5($password));
      $result = parent::dbInsert('users', $values);
      if($result){
        $return = array('register'=> true, 'mns'=>'ok');
      }else{
        $return = array('register'=> false, 'mns'=>'No se pudo crear el usuario');
      }
      return  $return ;
    } else {
      return array('register'=> false, 'mns'=>'El ususario ya existe');
    }
}
  public function autenticate_user($user, $password){
    $fields     = array('id','password'); 
    $conditions = array(array('field'=>'user', 'condition'=>'=', 'comparation'=>$user));
    $exist      = parent::dbSelect('users', $fields, $conditions);
    if(!empty($exist))
    {
      if($exist[0]['password']== md5($password)){
        return array('autenticate'=>true, 'uid'=>base64_encode($exist[0]['id']));
      }else{
        return array('autenticate'=>false, 'mns'=>'La contraseña es incorrecta');  
      }
    } else {
      return array('autenticate'=>false, 'mns'=>'No Existe El Usuario');
    } 
  }
  public function validate_code($user, $code){
    $id_user  = base64_decode($user);
    $fields   = array('id', 'codigo');
    $conditions = array(
      array(
        'field'=>'user_id', 
        'condition'=>'=', 
        'comparation'=> (int)$id_user
      ),
      array(
        'field'=>'state', 
        'condition'=>'=', 
        'comparation'=> 1
        ),
       );

    $juego      = parent::dbSelect('juego', $fields, $conditions);
    if(empty($juego))
    {
      $codeHide = $this->generate_code();
      $state    = 1;
      $result   = $this->compare_codes($code, $codeHide);
      if($result['fijas']==4){
        $state    = 0;
      }
      $time   = date('Y-m-d H:i:s', time());
      $values = array('user_id'=>(int)$id_user, 'codigo'=> $codeHide, 'f_inicio'=> $time, 'f_fin'=> $time, 'state'=>1);
      parent::dbInsert('juego', $values);
    } else 
    {
      $codeHide = $juego[0]['codigo'];
      $result   = $this->compare_codes($code, $codeHide);

      if($result['fijas']==4){
        $id_juego =  $juego[0]['id'];
        $time = date('Y-m-d H:i:s', time());
        $sets = array('f_fin'=> $time, 'state'=>0);
        $conditions = array(
          array(
            'field'=>'id', 
            'condition'=>'=', 
            'comparation'=> (int)$id_juego
          ));
        parent::dbUpdate('juego', $sets, $conditions);
      }
    }
    return $result;
  }

  public function delete_active_game($user){
    $id_user  = base64_decode($user);
    $fields   = array('id', 'codigo');
    $conditions = array(
      array(
        'field'=>'user_id', 
        'condition'=>'=', 
        'comparation'=> (int)$id_user
      ),
      array(
        'field'=>'state', 
        'condition'=>'=', 
        'comparation'=> 1
        ),
       );
    $juego      = parent::dbSelect('juego', $fields, $conditions);
    if(!empty($juego))
    {
      $id_juego =  $juego[0]['id'];
      $time = date('Y-m-d H:i:s', time());
      $sets = array('f_fin'=> $time, 'state'=>0);
      $conditions = array(
        array(
          'field'=>'id', 
          'condition'=>'=', 
          'comparation'=> (int)$id_juego
        ));
      return parent::dbUpdate('juego', $sets, $conditions);
    }
    return false;
  }
  private function compare_codes($code, $codeHide){
    $picas    = 0;
    $fijas    = 0;
    $code     = explode('-', $code);
    $codeHide = explode('-', $codeHide);
    foreach ($code as $key => $index) {
      if(in_array($index, $codeHide)){
        if($code[$key]== $codeHide[$key]){
          $fijas++;
        }else{
          $picas++;
        }
      }
    }
    return array('picas' => $picas, 'fijas' => $fijas);
  }

  private function append_jugada($juego_id, $code){

  }

  private function generate_code(){
    $code = array();
    while(count($code)<4){
      $index = rand(1,9);
      if(!in_array($index, $code)){
        $code[] = $index;
      }
    }
    return implode('-', $code);
  }

}