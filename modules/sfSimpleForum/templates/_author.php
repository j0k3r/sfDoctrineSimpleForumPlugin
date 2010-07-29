<?php
  $author_name = $post->getAuthorName();
  if($author_name == 'anonymous')
  {
    echo $author_name;
  }
  else
  {
    //$author = sfSimpleForumTools::getUserByUsername($author_name);
    $nb_posts = $author->getsfSimpleForumPost()->count();
    echo link_to(get_partial('sfSimpleForum/author_name', array('author' => $author_name, 'sf_cache_key' => $author_name)), 'sfSimpleForum/userLatestPosts?username='.$author_name);
    echo '<br/>';
    if ($author->hasPermission('moderator'))
    {
      echo __('Moderator', null, 'sfSimpleForum') . '<br/>';
    }
    echo format_number_choice('[1]1 message|(1,+Inf] %1% messages', array('%1%' => $nb_posts), $nb_posts, 'sfSimpleForum');

    if(sfConfig::get('app_sfSimpleForumPlugin_display_avatar',false)):
      $method = sfConfig::get('app_sfSimpleForumPlugin_avatar_method','getId');
      include_partial('sfSimpleForum/avatar',array('src'=>$sf_user->$method()));
    endif;

    if(sfConfig::get('app_sfSimpleForumPlugin_display_rank',false)):
      include_partial('sfSimpleForum/rank',array('rankArray'=>$rankArray,'nb_posts'=>$nb_posts));
    endif;

  }
?><br />
