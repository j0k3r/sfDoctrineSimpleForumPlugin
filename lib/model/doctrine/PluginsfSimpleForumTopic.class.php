<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class PluginsfSimpleForumTopic extends BasesfSimpleForumTopic
{
  protected
    $is_new   = false;
  
  public function getIsNew()
  {
    return $this->is_new;
  }
  
  public function setIsNew($value = true)
  {
    $this->is_new = $value;
  }
  
  public function getUser()
  {
    return sfSimpleForumTools::getUser($this);
  }
  
  public function leaveUpdatedAtUnchanged()
  {
    //$this->modifiedColumns[] = 'updated_at';
  }
  
  public function incrementViews()
  {
    $this->setNbViews($this->getNbViews() + 1);
    // Preserve the previous update date to avoid changing the topic order
    $this->leaveUpdatedAtUnchanged();
    parent::save();
  }
  
  public function addViewForUser($user_id)
  {
    //check if there is not already a topic view for this user
    $topic = Doctrine::getTable('sfSimpleForumTopicView')->find(array($user_id, $this->getId()));
    if ($topic instanceOf sfSimpleForumTopicView)
    {
      return;
    }
    $topicView = new sfSimpleForumTopicView();
    $topicView->setTopicId($this->getId());
    $topicView->setUserId($user_id);
    $topicView->save();
  }
  
  public function getViewForUser($user_id)
  {
    return Doctrine::getTable('sfSimpleForumTopicView')->find(array($user_id, $this->getId()));
  }

  public function clearTopicView($user_id)
  {
    Doctrine_Query::create()
      ->from('sfSimpleForumTopicView')
      ->where("topic_id = ?", $this->getId())
      ->andWhere("user_id != ?", $user_id)
      ->delete()->execute();
  }

  public function clearViews()
  {
    $q = Doctrine_Query::create();
    $q->from('sfSimpleForumPost');
    $q->where('topic_id = ?', array($this->getId()));
    $q->delete();
  }
  
  public function getNbReplies()
  {
    return $this->getNbPosts() - 1;
  }
  
  public function getLatestPostByQuery()
  {
    $q = Doctrine_Query::create();
    $q->from('sfSimpleForumPost');
    $q->where('topic_id = ?', array($this->getId()));
    $q->orderBy('id DESC');
    
    return $q->limit(1)->execute()->getFirst();
  }
  
  public function getLatestPost()
  {
    return $this->getsfSimpleForumPost();
  }
  
  public function getPosts($max = null)
  {
    return Doctrine::getTable('sfSimpleForumPost')->getForTopic($this->getId(), $max);
  }

  public function getPostsPager($page = 1, $max_per_page = 10)
  {
    return Doctrine::getTable('sfSimpleForumPost')->getForTopicPager($this->getId(), $page, $max_per_page);
  }
  
  public function updateReplies($latestReply = null, $con = null)
  {
    if($this->getId())
    {
      if($latestReply)
      {
        $this->setNbPosts(Doctrine::getTable('sfSimpleForumPost')->findByTopicId($this->get('id'))->count());
        $this->setLatestPostId($latestReply->getId());
        $this->setUpdatedAt($latestReply->getCreatedAt());
      }
      else
      {
        $this->setNbPosts(0);
        $this->setLatestPostId(null);
      }
      $this->save($con, $latestReply);
    }
  }

  public function save(Doctrine_Connection $con = null, $latestPost = null)
  {
    // we don't handle this save when we load data from command line
    if( ! isset($_SERVER['REQUEST_URI']))
    {
      return parent::save($con);
    }

    if(!$con)
    {
      $con = Doctrine_Manager::connection();
    }

    try
    {
      $con->beginTransaction();
      
      parent::save($con);
      
      // Update the topic's forum counts
      $forum = $this->getsfSimpleForumForum();
      if(!$latestPost)
      {
        $latestPost = $forum->getLatestPostByQuery();
      }
      $forum->updateCounts($latestPost, $con);
     
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollback();
      throw $e;
    }
  }
    
  public function delete(Doctrine_Connection $conn = null, $latestPost = null)
  {
    if(!$con)
    {
      $con = Doctrine_Manager::connection();
    }

    try
    {
      $con->beginTransaction();
      
      parent::delete($con);
      
      // Update the topic's forum counts
      $forum = $this->getsfSimpleForumForum();
      if(!$latestPost)
      {
        $latestPost = $forum->getLatestPostByQuery();
      }
      $forum->updateCounts($latestPost, $con);
      
      $con->commit();
    }
    catch (Exception $e)
    {
      $con->rollback();
      throw $e;
    }
  }
}