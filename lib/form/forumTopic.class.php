<?php

/**
 * PluginsfSimpleForumPost form.
 *
 * @package    form
 * @subpackage sfSimpleForumPost
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 6174 2007-11-27 06:22:40Z fabien $
 */
class forumTopic extends PluginsfSimpleForumTopicForm
{
  public function configure()
  {
    parent::configure();
    $user = sfContext::getInstance()->getUser();
    $widgetSchema = $this->getWidgetSchema();
    $validatorSchema = $this->getValidatorSchema();
    
    unset(
      $widgetSchema['created_at'],
      $widgetSchema['updated_at'],
      $widgetSchema['user_id'],
      $widgetSchema['latest_post_id'],
      $widgetSchema['nb_posts'],
      $widgetSchema['nb_views'],
      $widgetSchema['slug']
    );

    unset(
      $validatorSchema['created_at'],
      $validatorSchema['updated_at'],
      $validatorSchema['user_id'],
      $validatorSchema['latest_post_id'],
      $validatorSchema['nb_posts'],
      $validatorSchema['nb_views'],
      $validatorSchema['slug']
    );
    
    $widgetSchema['forum_id'] = new sfWidgetFormInputHidden();
    $widgetSchema['content'] = new sfWidgetFormTextarea();
    
    $validatorSchema['title'] = new sfValidatorString(array('max_length' => 255, 'required' => true));
    $validatorSchema['content'] = new sfValidatorString(array('required' => true));
    
    if( ! $user->hasCredential('moderator'))
    {
      unset(
        $widgetSchema['is_sticked'],
        $widgetSchema['is_locked']
      );

      unset(
        $validatorSchema['is_sticked'],
        $validatorSchema['is_locked']
      );
    }
    
    $widgetSchema->setNameFormat('forum_topic[%s]');
  }
}