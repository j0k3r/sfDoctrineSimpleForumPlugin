<?php use_helper('Pagination') ?>

<?php slot('title', $forum->getName().' - Forum '.sfConfig::get('app_config_title_short')) ?>

<?php slot('forum_navigation') ?>
  <?php echo forum_breadcrumb(array(
    array(sfConfig::get('app_sfSimpleForumPlugin_forum_name', 'Forums'), '@forum_home_list'),
    $forum->getName()
  )) ?>
<?php end_slot() ?>

<div id="middle-side" class="forum">

  <div class="breadcrumb">
    <?php include_slot('forum_navigation') ?>
  </div>
  
  <h1><?php echo $forum->getName() ?></h1>

  <ul class="forum_actions">
    <li><?php echo link_to('<span>'.__('New topic', null, 'sfSimpleForum').'</span>', 
                           '@forum_new_topic?forum_name='.$forum->getSlug(),
                           array('class' => 'button')) ?></li>
  </ul>
  
  <?php include_partial('sfSimpleForum/figures', array(
    'display_topic_link'  => false,
    'nb_topics'           => $forum->getNbTopics(),
    'topic_rule'          => '',
    'display_post_link'   => $forum->getNbPosts(),
    'nb_posts'            => $forum->getNbPosts(),
    'post_rule'           => '@forum_name_last?forum_name='.$forum->getSlug(),
    'feed_rule'           => '@forum_name_last_feed?forum_name='.$forum->getSlug(),
    'feed_title'          => $feed_title
  )) ?>
  
  <?php if ($forum->getNbTopics()): ?>
    
    <?php include_partial('sfSimpleForum/topic_list', array('topics' => $topics, 'include_forum' => false)) ?>
    
    <div id="pager">
      <?php echo pager_navigation($topic_pager, 'sfSimpleForum/forum?forum_name='.$forum->getSlug()) ?>    
    </div>
  <?php else: ?>
    <br/>
    <p><?php echo __('There is no topic in this discussion yet. Perhaps you would like to %start%?', array('%start%' =>  link_to(__('start a new one', null, 'sfSimpleForum'), '@forum_new_topic?forum_name='.$forum->getSlug())), 'sfSimpleForum') ?></p>
  <?php endif; ?>

</div>