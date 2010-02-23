<?php

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Nick Winfield <enquiries@superhaggis.com>              
 * @version    SVN: $Id$
 */

// autoloading for plugin lib actions is broken as at symfony-1.0.2
require_once(sfConfig::get('sf_plugins_dir'). '/sfDoctrineSimpleForumPlugin/modules/sfSimpleForum/lib/BasesfSimpleForumActions.class.php');

class sfSimpleForumActions extends BasesfSimpleForumActions
{
}
