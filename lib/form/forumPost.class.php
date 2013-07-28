<?php

/**
 * PluginsfSimpleForumPost form.
 *
 * @package    form
 * @subpackage sfSimpleForumPost
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class forumPost extends PluginsfSimpleForumPostForm
{
  public function configure()
  {
    parent::configure();

    $widgetSchema = $this->getWidgetSchema();
    $validatorSchema = $this->getValidatorSchema();

    unset(
      $widgetSchema['created_at'],
      $widgetSchema['updated_at'],
      $widgetSchema['author_name'],
      $widgetSchema['user_id'],
      $widgetSchema['title'],
      $widgetSchema['forum_id']
    );

    unset(
      $validatorSchema['created_at'],
      $validatorSchema['updated_at'],
      $validatorSchema['author_name'],
      $validatorSchema['user_id'],
      $validatorSchema['title'],
      $validatorSchema['forum_id']
    );

    $widgetSchema['topic_id'] = new sfWidgetFormInputHidden();

    $validatorSchema['content'] = new sfValidatorString(array('required' => true));

    $widgetSchema->setNameFormat('forum_post[%s]');
  }
}
