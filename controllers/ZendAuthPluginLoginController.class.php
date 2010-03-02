<?php

/**
* 
*/
class ZendAuthPluginLoginController extends sgBaseController
{
  public function GET()
  {
    header(ZendAuthPluginConfiguration::UNAUTHORIZED_HEADER);
    
    $route = sgContext::getCurrentRoute();
    $this->action = sgToolkit::url($route['path']);
    if (isset($_GET['destination']))
    {
      $this->destination = filter_var($_GET['destination'], FILTER_SANITIZE_URL);
    }
    return $this->render('login');
  }
  
  public function POST()
  {
    $data = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    $adapter = new Zend_Auth_Adapter_Digest(sgConfiguration::getPath('settings.ZendAuthPlugin.passwd_path'),
                                            '*',
                                            $data['username'],
                                            $data['password']);
    
    $result = Zend_Auth::getInstance()->authenticate($adapter);
    if ($result && $result->isValid())
    {
      if (isset($data['destination']))
      {
        header('Location: ' . sgToolkit::url(filter_var($data['destination'], FILTER_SANITIZE_URL)));
        exit();
      }
      
      header('Location: ' . sgContext::getRelativeBaseUrl());
      exit();
    }
    else
    {
      $this->errors = $result->getMessages();
      if (isset($data['destination']))
      {
        $_GET['destination'] = $data['destination'];
      }
      
      return self::GET();
    }
  }
}
