<?php

/**
 * PluginsfSimpleForumCategory form.
 *
 * @package    form
 * @subpackage sfSimpleForumCategory
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
abstract class PluginsfSimpleForumCategoryForm extends BasesfSimpleForumCategoryForm
{
  public function setup()
  {
    parent::setup();
    unset($this['created_at'], $this['updated_at']);
  }  
}