<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2007 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2007 Nick Winfield <enquiries@superhaggis.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Nick Winfield <enquiries@superhaggis.com>              
 * @version    SVN: $Id$
 */

class BasesfSimpleForumActions extends sfActions
{
  public function executeIndex()
  {
    $this->forward('sfSimpleForum', 'forumList');
  }
  
  public function executeForumList()
  {
    $forums = Doctrine::getTable('sfSimpleForumForum')->getAllOrderedByCategory();
    $nb_topics = 0; 
    $nb_posts  = 0;

    foreach($forums as $forum)
    {
      $nb_topics += $forum->getNbTopics();
      $nb_posts  += $forum->getNbPosts();
    }

    $this->forums     = $forums;
    $this->nb_topics  = $nb_topics;
    $this->nb_posts   = $nb_posts;
    $this->feed_title = $this->getFeedTitle();
  }
  
  public function executeLatestPosts()
  {
    $this->post_pager = Doctrine::getTable('sfSimpleForumPost')->getLatestPager(
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    $this->nb_topics  = Doctrine::getTable('sfSimpleForumTopic')->findAll()->count();
    $this->feed_title = $this->getFeedTitle();
    $this->rankArray  = $this->getRankArray();
  }
  
  public function executeLatestPostsFeed()
  {
    $this->checkFeedPlugin();
    
    $this->posts = Doctrine::getTable('sfSimpleForumPost')->getLatest(
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    $this->rule = $this->getModuleName().'/latestPosts';
    $this->feed_title = $this->getFeedTitle();
    
    return $this->renderText($this->getFeedFromObjects($this->posts));
  }
  
  protected function getFeedTitle()
  {
    $this->getContext()->getConfiguration()->loadHelpers(array('I18N'));
    return __('Latest messages from %forums%', array(
      '%forums%'  => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
    ));
  }
  
  public function executeLatestTopics()
  {
    $this->topics_pager = Doctrine::getTable('sfSimpleForumTopic')->getLatestPager(
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    $this->feed_title = $this->getLatestTopicsFeedTitle();
  }
  
  public function executeLatestTopicsFeed()
  {
    $this->checkFeedPlugin();
    
    $this->topics = Doctrine::getTable('sfSimpleForumTopic')->getLatest(
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    $this->rule = $this->getModuleName().'/latestTopics';
    $this->feed_title = $this->getLatestTopicsFeedTitle();
    
    return $this->renderText($this->getFeedFromObjects($this->topics));
  }
  
  protected function getLatestTopicsFeedTitle()
  {
    $this->getContext()->getConfiguration()->loadHelpers(array('I18N'));
    return __('Latest topics from %forums%', array(
      '%forums%'  => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
    ));
  }

  // get rank information
  protected function getRankArray()
  {
    $rankArray = array();
    if (sfConfig::get('app_sfSimpleForumPlugin_display_rank', true))
    {
      $rankArray = Doctrine_Core::getTable('sfSimpleForumRank')->fetchOrderedByNbPostsArray();
    }
    return $rankArray;
  }

  // One forum
  
  public function executeForum()
  {
    $this->setForumVars();
    
    $this->topic_pager = $this->forum->getTopicsPager(
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    $this->topics = $this->topic_pager->getResults();
    
    if (sfConfig::get('app_sfSimpleForumPlugin_count_views', true) && $this->getUser()->isAuthenticated())
    {
      // FIXME: When Propel can do a right join with multiple on conditions, merge this query with the pager's one
      $this->topics = Doctrine::getTable('sfSimpleForumTopic')->setIsNewForUser($this->topics, sfSimpleForumTools::getConnectedUserId($this->getUser()));
    }

    $response = $this->getResponse();
    $response->addMeta('description', $this->forum->getName() . ' - ' . $this->forum->getDescription());
  }

  public function executeForumLatestPosts()
  {
    $this->setForumVars();

    $this->post_pager = $this->forum->getPostsPager(
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    $this->rankArray  = $this->getRankArray();
  }
    
  public function executeForumLatestPostsFeed()
  {
    $this->checkFeedPlugin();
    
    $this->setForumVars();    
    $this->posts = $this->forum->getPosts(
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    $this->rule = $this->getModuleName().'/forumLatestPosts?forum_name='.$this->name;
    
    return $this->renderText($this->getFeedFromObjects($this->posts));
  }
    
  protected function setForumVars()
  {
    $this->name = $this->getRequestParameter('forum_name');

    $forum = Doctrine::getTable('sfSimpleForumForum')->retrieveBySlug($this->name);
    $this->forward404Unless($forum);
    $this->forum = $forum;

    $this->getContext()->getConfiguration()->loadHelpers(array('I18N'));
    $this->feed_title =  __('Latest messages from %forums% » %forum%', array(
      '%forums%'  => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
      '%forum%'   => $this->forum->getName()
    ));
  }
  
  // One topic

  public function executeTopic($request)
  {
    $this->setTopicVars($request->getParameter('id'));
    $this->post_pager = $this->topic->getPostsPager(
      $request->getParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    $this->forward404Unless($this->post_pager, 'Topic not found !');
    
    if (sfConfig::get('app_sfSimpleForumPlugin_count_views', true))
    {
      // lame protection against simple page refreshing
      if($this->getUser()->getAttribute('sf_simple_forum_latest_viewed_topic') != $this->topic->getId())
      {
        $this->topic->incrementViews();
        $this->getUser()->setAttribute('sf_simple_forum_latest_viewed_topic', $this->topic->getId());
      }
      if($this->getUser()->isAuthenticated())
      {
        $this->topic->addViewForUser(sfSimpleForumTools::getConnectedUserId($this->getUser()));
      }
    }

    $this->rankArray = $this->getRankArray();

    if (!$this->topic->getIsLocked() && $this->getUser()->isAuthenticated())
    {
      $this->form = new forumPost();
      $this->form->setDefaults(array('topic_id' => $this->topic->get('id')));
      
      if ($request->isMethod('post'))
      {
        $this->form->bind($request->getParameter('forum_post'));
        
        // We must check if the topic isn't locked
        $this->forward404If($this->topic->getIsLocked());
        
        if ($this->form->isValid())
        {
          $values = $this->form->getValues();
          
          $post = new sfSimpleForumPost();
          $post->setContent($values['content']);
          $post->setTitle($this->topic->get('title'));
          $post->setUserId(sfSimpleForumTools::getConnectedUserId($this->getUser()));
          $post->setTopicId($this->topic->getId());
          $post->setForumId($this->topic->get('sfSimpleForumForum')->get('id'));
          $post->save();

          $this->topic->clearTopicView($this->getUser()->getGuardUser()->getId());
          
          $this->redirectToPost($post);
        }
      }
    }

    $response = $this->getResponse();
    $response->addMeta('description', $this->topic->getTitle());
  }
  
  public function executeTopicFeed($request)
  {
    $this->checkFeedPlugin();
    $this->setTopicVars($request->getParameter('id'));
    $this->posts = $this->topic->getPosts(
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    $this->forward404Unless($this->posts);
    
    $this->rule = $this->getModuleName().'/topic?id='.$this->getRequestParameter('id').'&stripped_title='.$this->getRequestParameter('forum_name');
    
    return $this->renderText($this->getFeedFromObjects($this->posts));
  }
  
  protected function setTopicVars($id)
  {
    $this->topic = Doctrine::getTable('sfSimpleForumTopic')->find($id);
    $this->forward404Unless($this->topic);

    $this->getContext()->getConfiguration()->loadHelpers(array('I18N'));
    $this->feed_title =  __('Latest messages from %forums% » %forum% » %topic%', array(
      '%forums%'  => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
      '%forum%'   => $this->topic->getsfSimpleForumForum()->getName(),
      '%topic%'   => $this->topic->getTitle()
    ));
  }
  
  public function executePost()
  {
    $post = Doctrine::getTable('sfSimpleForumPost')->find($this->getRequestParameter('id'));
    $this->forward404Unless($post);

    $topic = $post->getTopic();
    $position = $post->getPositionInTopic();
    $page = ceil(($position + 1) / sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10));
    $this->redirect($this->getModuleName().'/topic?id='.$topic->getId().'&stripped_title='.$topic->getSlug().'&page='.$page.'#post'.$post->getId());
  }
  
  // One user

  public function executeUserLatestPosts()
  {
    $this->setUserVars();
    
    $this->post_pager = Doctrine::getTable('sfSimpleForumPost')->getForUserPager(
      $this->user->getId(),
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    $this->feed_title = $this->getUserLatestPostsFeedTitle();
  }
  
  public function executeUserLatestPostsFeed()
  {
    $this->setUserVars();
    
    $this->posts = Doctrine::getTable('sfSimpleForumPost')->getForUser(
      $this->user->getId(),
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    
    $this->rule = $this->getModuleName().'/userLatestPosts?username='.$this->username;
    $this->feed_title = $this->getUserLatestPostsFeedTitle();
    
    return $this->renderText($this->getFeedFromObjects($this->posts));
  }
  
  protected function getUserLatestPostsFeedTitle()
  {
    $this->getContext()->getConfiguration()->loadHelpers(array('I18N'));
    return __('Latest messages from %forums% by %username%', array(
      '%forums%'   => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
      '%username%' => $this->user->getUsername(),
    ));
  }
  
  public function executeUserLatestTopics()
  {
    $this->setUserVars();
        
    $this->topics_pager = Doctrine::getTable('sfSimpleForumTopic')->getForUserPager(
      $this->user->getId(),
      $this->getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    
    $this->feed_title = $this->getUserLatestTopicsFeedTitle();
  }
  
  public function executeUserLatestTopicsFeed()
  {
    $this->setUserVars();
    
    $this->topics = Doctrine::getTable('sfSimpleForumTopic')->getForUser(
      $this->user->getId(),
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    $this->rule = $this->getModuleName().'/latestUserTopics?username='.$this->username;
    $this->feed_title = $this->getUserLatestTopicsFeedTitle();
    
    return $this->renderText($this->getFeedFromObjects($this->topics));
  }
  
  protected function getUserLatestTopicsFeedTitle()
  {
    $this->getContext()->getConfiguration()->loadHelpers(array('I18N'));
    return __('Latest topics from %forums% by %username%', array(
      '%forums%'   => sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'),
      '%username%' => $this->user->getUsername(),
    ));
  }
  
  protected function setUserVars()
  {
    $this->username  = $this->getRequestParameter('username');
    $this->user      = sfSimpleForumTools::getUserByUsername($this->username);
    $this->rankArray = $this->getRankArray();

    $this->forward404Unless($this->user);
  }
  
  // Feed related private methods
  
  protected function checkFeedPlugin()
  {
    if(!class_exists('sfFeedPeer'))
    {
      throw new sfException('You must install sfFeed2Plugin to use the feed actions');
    }
  }
  
  protected function getFeedFromObjects($objects)
  {
    $feed = sfFeedPeer::createFromObjects(
      $objects,
      array(
        'format'      => 'atom1',
        'title'       => $this->feed_title,
        'link'        => $this->rule,
        'methods'     => array('authorEmail' => '')
      )
    );
    $this->setLayout(false);
    return $feed->asXml();
  }
  
  // Display the topic creation form
  
  public function executeCreateTopic($request)
  {
    $this->form = new forumTopic();
    if($request->hasParameter('forum_name'))
    {
      $this->forum = Doctrine::getTable('sfSimpleForumForum')->retrieveBySlug($request->getParameter('forum_name'));
      $this->forward404Unless($this->forum, 'Forum not found !');

      $this->form->setDefaults(array('forum_id' => $this->forum->get('id')));
    }
    elseif( ! $request->isMethod('post'))
    {
      // we don't allow new topic outside forum
      $this->forward404();
    }

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter('forum_topic'));
      
      if ($this->form->isValid())
      {
        $values = $this->form->getValues();
        
        $topic = new sfSimpleForumTopic();
        $topic->set('forum_id', $values['forum_id']);
        $topic->setTitle($values['title']);
        $topic->setUserId(sfSimpleForumTools::getConnectedUserId($this->getUser()));
        if ($this->getUser()->hasCredential('moderator'))
        {
          $topic->setIsSticked($values['is_sticked'] ? 1 : 0);
          $topic->setIsLocked($values['is_locked']   ? 1 : 0);
        }
        $topic->save();

        $post = new sfSimpleForumPost();
        $post->setContent($values['content']);
        $post->setTitle($values['title']);
        $post->setUserId(sfSimpleForumTools::getConnectedUserId($this->getUser()));
        $post->setsfSimpleForumTopic($topic);
        $post->setForumId($topic->get('sfSimpleForumForum')->get('id'));
        $post->save();
        
        $this->redirectToPost($post);
      }
    }
  }
  
  protected function redirectToPost($post)
  {
    $position = $post->getPositionInTopic();
    $page = ceil(($position + 1) / sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10));
    $this->redirect($this->getModuleName().'/topic?id='.$post->getTopic()->getId().'&stripped_title='.$post->getTopic()->getSlug().'&page='.$page.'#post'.$post->getId());    
  }
  
  public function executeDeletePost()
  {
    $post = Doctrine::getTable('sfSimpleForumPost')->find($this->getRequestParameter('id'));
    $this->forward404Unless($post);
    
    $topic = $post->getTopic();
    if (Doctrine::getTable('sfSimpleForumPost')->findByTopicId($topic->get('id'))->count() == 1) 
    {
      // it is the last post of the topic, so delete the whole topic
      $topic->delete();
      $forum = $post->getsfSimpleForumForum();
      $this->redirect($this->getModuleName().'/forum?forum_name='.$forum->getSlug());
    }
    else
    {
      // delete only one message and redirect to the topic
      $post->delete();
      $this->redirect($this->getModuleName().'/topic?id='.$topic->getId().'&stripped_title='.$topic->getSlug());
    }
  }
  
  public function executeDeleteTopic()
  {
    $topic = Doctrine::getTable('sfSimpleForumTopic')->find($this->getRequestParameter('id'));
    $this->forward404Unless($topic);
    
    $topic->delete();
    
    $forum = $topic->getsfSimpleForumForum();
    $this->redirect($this->getModuleName().'/forum?forum_name='.$forum->getSlug());
  }
  
  // stick/unstick a topic
  
  public function executeToggleStick()
  {
    $topic = Doctrine::getTable('sfSimpleForumTopic')->find($this->getRequestParameter('id'));
    $this->forward404Unless($topic);
    
    $topic->setIsSticked(!$topic->getIsSticked());
    $topic->leaveUpdatedAtUnchanged();
    $topic->save();
    
    $this->redirect($this->getModuleName().'/topic?id='.$topic->getId());
  }
  
  // lock/unlock a topic
  
  public function executeToggleLock()
  {
    $topic = Doctrine::getTable('sfSimpleForumTopic')->find($this->getRequestParameter('id'));
    $this->forward404Unless($topic);
    
    $topic->setIsLocked(!$topic->getIsLocked());
    $topic->leaveUpdatedAtUnchanged();
    $topic->save();
    
    $this->redirect($this->getModuleName().'/topic?id='.$topic->getId());
  }

  public function executeReportAbuse()
  {
    $topic = Doctrine::getTable('sfSimpleForumTopic')->find($this->getRequestParameter('id'));
    $this->forward404Unless($topic);
    
    $topic->reportAbuse($this->getUser()->getGuardUser());
    $topic->leaveUpdatedAtUnchanged();
    $topic->save();

    $this->sendAbuseEmail($topic);

    $this->redirect($this->getModuleName().'/topic?id='.$topic->getId());
  }

  protected function sendAbuseEmail(sfSimpleForumTopic $topic)
  {
    $this->getContext()->getConfiguration()->loadHelpers(array('I18N'));
    $mailFrom = sfConfig::get('app_sfSimpleForumPlugin_from_email','changethis@test.com');

    $mailTo = sfConfig::get('app_sfSimpleForumPlugin_admin_email',null);

    if($mailTo === null)
    {
      return;
    }

    $mailBody = $this->getPartial('sfSimpleForum/abuse_mail', array(
      'topic' 		=> $topic,
    ));

    $message = $this->getMailer() 
      ->compose($mailFrom,$mailTo,__('An abuse was reported for topic "%1%"',array("%1%" => $topic->getTitle()),'sfSimpleForum'),$mailBody)
      ->setContentType('text/html');
    $this->getMailer()->send($message);
  }

  public function executeRecommand()
  {
    $topic = Doctrine::getTable('sfSimpleForumTopic')->find($this->getRequestParameter('id'));
    $this->forward404Unless($topic);
    
    $topic->recommand($this->getUser()->getGuardUser());
    $topic->leaveUpdatedAtUnchanged();
    $topic->save();
    
    $this->redirect($this->getModuleName().'/topic?id='.$topic->getId());
  }

  public function executeUpdatePost($request)
  {
    $this->forward404If( ! $request->getParameter('content') || 
                         ! $request->getParameter('id') || 
                         ! $this->getUser()->hasCredential('moderator'));

    $post = Doctrine::getTable('sfSimpleForumPost')->find(substr($request->getParameter('id'), 5, strlen($request->getParameter('id'))));
    $this->forward404Unless($post);

    $post->set('content', $request->getParameter('content'));
    $post->save();

    return $this->renderText(nl2br($post->get('content')));
  }

  public function executeUpdateTopic($request)
  {
    $this->forward404If( ! $request->getParameter('title') || 
                         ! $request->getParameter('id') || 
                         ! $this->getUser()->hasCredential('moderator'));

    $topic = Doctrine::getTable('sfSimpleForumTopic')->find(substr($request->getParameter('id'), 6, strlen($request->getParameter('id'))));
    $this->forward404Unless($topic);

    $topic->set('title', $request->getParameter('title'));
    $topic->save();

    return $this->renderText(nl2br($topic->get('title')));
  }
}
