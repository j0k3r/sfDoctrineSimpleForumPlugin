<table id="threads">
  <tr>
    <th>&nbsp;</th>
    <th class="thread_name"><?php echo __('Topic', null, 'sfSimpleForum') ?></th>
    <th class="thread_replies"><?php echo __('Replies', null, 'sfSimpleForum') ?></th>
    <?php if (sfConfig::get('app_sfSimpleForumPlugin_count_views', true)): ?>
    <th class="thread_replies"><?php echo __('Views', null, 'sfSimpleForum') ?></th>
    <?php endif; ?>
    <?php if (sfConfig::get('app_sfSimpleForumPlugin_display_recommandations', true)): ?>
    <th class="thread_recommandations"><?php echo __('Recommandations', null, 'sfSimpleForum') ?></th>
    <?php endif; ?>
    <th class="thread_recent"><?php echo __('Last Message', null, 'sfSimpleForum') ?></th>
  </tr>
  <?php foreach ($topics as $topic): ?>
    <?php include_partial('sfSimpleForum/topic', array(
      'topic'             => $topic, 
      'include_forum'     => $include_forum, 
      'user_is_moderator' => $sf_user->hasCredential('moderator'),
      'sf_cache_key'      => $topic->getId().'_'.$sf_user->hasCredential('moderator')
      )) ?>
  <?php endforeach; ?>
</table>
