<?php

class updateLastPostForumTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'forum';
    $this->name             = 'update-last-post';
    $this->briefDescription = 'Updated latest post information from the database';
    $this->detailedDescription = <<<EOF
The [update-last-post|INFO] task does things.
Call it with:

  [php symfony forum:update-last-post|INFO]

  Updated latest_post_id with latest information from the database.

EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $application = $options['application'];

    if($options['env'] == 'dev')
    {
      $env = 'dev';
      $debug = true;
    }
    elseif($options['env'] == 'prod')
    {
      $env = 'prod';
      $debug = false;
    }
    else
    {
      $this->logSection('error', 'Wrong environment specified!');
      return;
    }

    // initialize the database connection
    $configuration = ProjectConfiguration::getApplicationConfiguration($application, $env, $debug);
    // need to create  a context, if not, we got an error : The "default" context does not exist.
    $context = sfContext::createInstance($configuration);
    $databaseManager = new sfDatabaseManager($configuration);

    $this->logSection('----------', '----------');
    $this->logSection('Update latest post start', date('d-m-y H:i'));
    $this->logSection('----------', '----------');


    $topics = Doctrine::getTable('sfSimpleForumTopic')->findAll();
    foreach($topics as $topic)
    {
      $topic->set('nb_posts', Doctrine::getTable('sfSimpleForumPost')->findByTopicId($topic->get('id'))->count());
      $topic->set('latest_post_id', $topic->getLatestPostByQuery()->get('id'));
      $topic->save();

      $this->logSection('Topic', $topic->get('title'));
      $this->logSection('post_id', $topic->getLatestPostByQuery()->get('id'));
    }

    $forums = Doctrine::getTable('sfSimpleForumForum')->findAll();
    foreach($forums as $forum)
    {
      $forum->set('latest_post_id', ($forum->getLatestPostByQuery() instanceOf sfSimpleForumPost) ? $forum->getLatestPostByQuery()->get('id') : null);
      $forum->save();

      $this->logSection('Forum', $forum->get('name'));
      $this->logSection('post_id', ($forum->getLatestPostByQuery() instanceOf sfSimpleForumPost) ? $forum->getLatestPostByQuery()->get('id') : null);
    }

    $this->logSection('End', date('d-m-y H:i'));
  }
}
