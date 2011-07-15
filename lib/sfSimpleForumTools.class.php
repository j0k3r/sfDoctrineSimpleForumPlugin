<?php

class sfSimpleForumTools
{

  /**
   * Retrieves user model instance from its username
   *
   * @param string Username
   * @return mixed User model object (class sfGuard by default)
   */
  public static function getUserByUsername($username)
  {
    // $user_class = sfConfig::get('app_sfSimpleForumPlugin_user_class', 'sfGuardUser');
    // $user_peer = $user_class.'Peer';
    // $method_name = sfConfig::get('app_sfSimpleForumPlugin_user_retrieve_by_name_method', 'retrieveByUsername');
    // if (!is_callable($user_peer, $method_name))
    // {
      // throw new sfException(sprintf('Unexistant method %s::%s() for retrieving a %s from its username', $user_peer, $method_name, $user_class));
    // }

    // return call_user_func(array($user_peer, $method_name), $username);

    return Doctrine::getTable('sfGuardUser')->retrieveByUsername($username);
  }

  /**
   * Retrieves primary key of the connected user
   *
   * @param  sfBasicSecurityUser User object
   * @return integer The id of the connected user
   */
  public static function getConnectedUserId($session)
  {
    if(!$session->isAuthenticated())
    {
      throw new sfException('Attempting to retrieve the id of the connected user on an anonymous session');
    }
    if ($session instanceof sfGuardSecurityUser)
    {
      return $session->getGuarduser()->getId();
    }
    elseif (method_exists($session, 'getId'))
    {
      return $session->getId();
    }
    else
    {
      throw new sfException(sprintf('Your "%" session handling class must implement a getId() method returning current connected user primary key', $session));
    }
  }

  /**
   * Retrieves user model instance associated to a forum object
   *
   * @param  Doctrine_Record  Model object (can be thread, post, etc.)
   * @return mixed User model object (class sfGuard by default)
   */
/*
  public static function getUser(Doctrine_Record $object)
  {
    $user_class = sfConfig::get('app_sfSimpleForumPlugin_user_class', 'sfGuardUser');
    if (!class_exists($user_class))
    {
      throw new sfException(sprintf('User class %s cannot be found', $user_class));
    }

    $user_getter = sprintf('get%s', $user_class);
    if (!method_exists($object, $user_getter))
    {
      throw new sfException(sprintf('Cannot call %s::%s() - You should either install sfGuardPlugin or configure your app.yml file filling the "user_class" parameter in the sfSimpleForumPlugin section.', get_class($object), $user_getter));
    }

    return $object->$user_getter();
  }
*/
}
