<table id="messages">
  <?php foreach ($posts as $post): ?>
    <?php include_partial('sfSimpleForum/post', array(
      'post'              => $post,
      'include_topic'     => $include_topic,
      'user_is_moderator' => $sf_user->hasCredential('moderator'),
      'sf_cache_key'      => $post->getId().'_'.$sf_user->hasCredential('moderator')
    )) ?>
  <?php endforeach; ?>
</table>