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
  }
?><br />
