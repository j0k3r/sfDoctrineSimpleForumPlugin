<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfDoctrineSimpleForumPlugin configuration.
 * 
 * @package    sfDoctrineSimpleForumPlugin
 * @subpackage config
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: sfDoctrineGuardPluginConfiguration.class.php 25546 2009-12-17 23:27:55Z Jonathan.Wage $
 */
class sfDoctrineSimpleForumPluginConfiguration extends sfPluginConfiguration {

  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    foreach (array('sfSimpleForumCategoryAdmin', 'sfSimpleForumForumAdmin','sfSimpleForumRankAdmin') as $module)
    {
      if (in_array($module, sfConfig::get('sf_enabled_modules', array())))
      {
        $this->dispatcher->connect('routing.load_configuration', array('sfDoctrineSimpleForumRouting', 'addRouteFor' . str_replace('sfSimpleForum', '', $module)));
      }
    }

    if (in_array('sfSimpleForum', sfConfig::get('sf_enabled_modules', array())) && sfConfig::get('app_sfDoctrineSimpleForumPlugin_load_css'))
    {
      $this->dispatcher->connect('context.load_factories', array($this, 'listenLoadCss'));
    }
  }

  /**
   * Load StyleSheet
   * @param sfEvent $event
   */
  public static function listenLoadCss(sfEvent $event)
  {
    sfContext::getInstance()->getResponse()->addStyleSheet('/sfDoctrineSimpleForumPlugin/css/forum.css', 'last');
    sfContext::getInstance()->getResponse()->addStyleSheet('/sfDoctrineSimpleForumPlugin/css/reset.css', 'last');
  }

}
