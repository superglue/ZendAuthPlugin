<?php

/**
* 
*/
class ZendAuthPluginLogoutController extends sgBaseController
{
  function GET()
  {
    Zend_Auth::getInstance()->clearIdentity();
    header('Location: ' . sgContext::getRelativeBaseUrl());
  }
}
