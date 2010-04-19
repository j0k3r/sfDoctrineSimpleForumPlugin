<?php

class fixForumTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'forum';
    $this->name             = 'fix';
    $this->briefDescription = 'Updated latest information from the database';
    $this->detailedDescription = <<<EOF
The [fix|INFO] task does things.
Call it with:

  [php symfony forum:fix|INFO]
  
  Updated nb_posts, nb_topics and latest_post_id with latest information from the database.
  
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    if($options['env'] == 'dev')
    {
      $application = 'frontend';
      $env = 'dev';
      $script = 'frontend_dev.php';
      $debug = true;
    }
    elseif($options['env'] == 'prod')
    {
      $application = 'frontend';
      $env = 'prod';
      $script = 'index.php';
      $debug = false;
    }
    else
    {
      $this->logSection('error', 'Wrong environment specified!');
      die();
    }

    // initialize the database connection
    $configuration = ProjectConfiguration::getApplicationConfiguration($application, $env, $debug);
    // need to create  a context, if not, we got an error : The "default" context does not exist.
    $context = sfContext::createInstance($configuration);
    $databaseManager = new sfDatabaseManager($configuration);

    $this->logSection('----------', '----------');
    $this->logSection('fix forum start', date('d-m-y H:i'));
    $this->logSection('----------', '----------');
    
    $posts = Doctrine::getTable('sfSimpleForumPost')->findAll();
    foreach($posts as $post)
    {
      if(is_null($post->get('forum_id')))
      {
        $post->set('forum_id', $post->get('sfSimpleForumTopic')->get('forum_id'));
        $post->set('title', $post->get('sfSimpleForumTopic')->get('title'));
        $post->save();
      }
    }
    
    $topics = Doctrine::getTable('sfSimpleForumTopic')->findAll();
    foreach($topics as $topic)
    {
      $topic->set('nb_posts', Doctrine::getTable('sfSimpleForumPost')->findByTopicId($topic->get('id'))->count());
      $topic->set('latest_post_id', $topic->getLatestPostByQuery()->get('id'));
      $topic->save();

      $this->logSection('Topic', $topic->get('title'));
      $this->logSection('nb_posts', Doctrine::getTable('sfSimpleForumPost')->findByTopicId($topic->get('id'))->count());
      $this->logSection('post_id', $topic->getLatestPostByQuery()->get('id'));
    }
    
    $forums = Doctrine::getTable('sfSimpleForumForum')->findAll();
    foreach($forums as $forum)
    {
      $forum->set('nb_posts', Doctrine::getTable('sfSimpleForumPost')->findByForumId($forum->get('id'))->count());
      $forum->set('nb_topics', Doctrine::getTable('sfSimpleForumTopic')->findByForumId($forum->get('id'))->count());
      $forum->set('latest_post_id', ($forum->getLatestPostByQuery() instanceOf sfSimpleForumPost) ? $forum->getLatestPostByQuery()->get('id') : null);
      $forum->save();

      $this->logSection('Forum', $forum->get('name'));
      $this->logSection('nb_posts', Doctrine::getTable('sfSimpleForumPost')->findByForumId($forum->get('id'))->count());
      $this->logSection('nb_topics', Doctrine::getTable('sfSimpleForumTopic')->findByForumId($forum->get('id'))->count());
      $this->logSection('post_id', ($forum->getLatestPostByQuery() instanceOf sfSimpleForumPost) ? $forum->getLatestPostByQuery()->get('id') : null);
    }
    
    $this->logSection('End', date('d-m-y H:i'));
  }
}
