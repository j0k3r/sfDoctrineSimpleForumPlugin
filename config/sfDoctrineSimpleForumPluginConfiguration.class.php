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
class sfDoctrineSimpleForumPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    foreach (array('sfSimpleForumCategoryAdmin', 'sfSimpleForumForumAdmin') as $module)
    {
      if (in_array($module, sfConfig::get('sf_enabled_modules', array())))
      {
        $this->dispatcher->connect('routing.load_configuration', array('sfDoctrineSimpleForumRouting', 'addRouteFor'.str_replace('sfSimpleForum', '', $module)));
      }
    }
  }
}
