
<label for="sf_simple_forum_category_forum">Forums</label>
<div class="form_field" id="sf_simple_forum_category_forum">
  <ul>
  <?php foreach ($form->getObject()->getForum() as $forum): ?>
    <li><?php echo link_to($forum->getName(), 'sfSimpleForumForumAdmin/edit?id='.$forum->getId()) ?></li>
  <?php endforeach ?>
  </ul>
</div>