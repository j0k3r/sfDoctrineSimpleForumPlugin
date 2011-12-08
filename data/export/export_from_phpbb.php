<?php

// set up the database connection
include("config.inc.php");

// Gestion du BBcode
define('IN_PHPBB', true);
define('INCLUDE_PHPBB', true);

$phpbb_root_path = 'forum/';
$phpbb_table_prefix = 'forum';

include_once($phpbb_root_path . 'extension.inc');
include_once($phpbb_root_path . 'common.php');
$template = new Template($phpbb_root_path . 'templates/subSilver');
include($phpbb_root_path . 'includes/bbcode.php');

function stripText($text)
{
  $text = strtolower($text);

  // strip all non word chars
  $text = preg_replace('/\W/', ' ', $text);

  // replace all white space sections with a dash
  $text = preg_replace('/\ +/', '-', $text);

  // trim dashes
  $text = preg_replace('/\-$/', '', $text);
  $text = preg_replace('/^\-/', '', $text);

  $text = strtr($text, "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËéèêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ@²", "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynna2");

  return $text;
}

//////// USER

$sql = "SELECT username, user_password, user_regdate, user_level, user_avatar, user_email, user_website, user_from, user_occ
        FROM `".$phpbb_table_prefix."_users`
        WHERE user_id > 1
        AND user_active = 1";
$req = mysql_query($sql);

$yml = array();
while($data = mysql_fetch_array($req))
{
  switch($data['user_level'])
  {
    case 1 :  $group_id = 'admin'; break;
    case 2 :  $group_id = 'moderator'; break;
    default : $group_id = 'inscrit';
  }

  $yml['sfGuardUser'][] = '
  '.$data['username'].'_user:
    username:       '.$data['username'].'
    password:       '.$data['user_password'].'
    is_super_admin: '.($data['user_level'] == 1 ? 1 : 0).'
    created_at:     '.date('Y-m-d h:i:s', $data['user_regdate']).'
    algorithm:      md5
';

  $yml['sfGuardUserProfile'][] = '
  '.$data['username'].'_profile:
    user_id:        '.$data['username'].'_user
    avatar:         '.$data['user_avatar'].'
    email:          '.$data['user_email'].'
    website:        '.$data['user_website'].'
    fromwhere:      '.$data['user_from'].'
    occupation:     '.$data['user_occ'].'
';

  $yml['sfGuardUserGroup'][] = '
  '.$data['username'].'_group:
    group_id:       '.$group_id.'
    user_id:        '.$data['username'].'_user
  ';
}

echo '<pre>';
echo 'sfGuardUser:';
foreach($yml['sfGuardUser'] as $oneC)
{
  echo $oneC;
}
echo '

sfGuardPermission:
  admin:
    name:           admin
    description:    Administrator permission
  moderator:
    name:           moderator
    description:    Moderator permission
  inscrit:
    name:           inscrit
    description:    Registered permission

sfGuardGroup:
  admin:
    name:           admin
    description:    Les administrateurs
  moderator:
    name:           moderator
    description:    Les modérateurs
  inscrit:
    name:           inscrit
    description:    Les inscrits

sfGuardGroupPermission:
  admin:
    group_id:       admin
    permission_id:  admin
  moderator:
    group_id:       moderator
    permission_id:  moderator
  inscrit:
    group_id:       inscrit
    permission_id:  inscrit

';

echo 'sfGuardUserGroup:';
foreach($yml['sfGuardUserGroup'] as $oneC)
{
  echo $oneC;
}
echo '
';

echo 'sfGuardUserProfile:';
foreach($yml['sfGuardUserProfile'] as $oneC)
{
  echo $oneC;
}

echo "\n";

//////// CATEGEORY

$sql_category = 'SELECT * FROM `'.$phpbb_table_prefix.'_categories` ORDER BY `'.$phpbb_table_prefix.'_categories`.`cat_order` ASC';
$req_category = mysql_query($sql_category);

$yml = array();
while($data = mysql_fetch_array($req_category))
{
  $yml['sfSimpleForumCategory'][] = '
  c_'.$data['cat_id'].':
    name:         "'.$data['cat_title'].'"
    description:  "'.$data['cat_title'].'"
    rank:         '.$data['cat_order'].'
';
}

echo 'sfSimpleForumCategory:';
foreach($yml['sfSimpleForumCategory'] as $oneC)
{
  echo $oneC;
}
echo "\n";

//////// FORUMS

$sql_forum = 'SELECT * FROM `'.$phpbb_table_prefix.'_forums` WHERE '.$phpbb_table_prefix.'_forums.auth_post != 3';
$req_forum = mysql_query($sql_forum);

$yml = array();
while($data = mysql_fetch_array($req_forum))
{
  $yml['sfSimpleForumForum'][] = '
  f_'.$data['forum_id'].':
    name:                   "'.$data['forum_name'].'"
    description:            "'.$data['forum_desc'].'"
    rank:                   '.$data['forum_order'].'
    sfSimpleForumCategory:  c_'.$data['cat_id'].'
    phpbb_id:               '.$data['forum_id'].'
';
}

echo 'sfSimpleForumForum:';
foreach($yml['sfSimpleForumForum'] as $oneC)
{
  echo $oneC;
}
echo "\n";

//////// TOPICS

$sql_topic = 'SELECT * , u.username
              FROM '.$phpbb_table_prefix.'_topics ft
              LEFT JOIN '.$phpbb_table_prefix.'_users u ON u.user_id = ft.topic_poster
              LEFT JOIN '.$phpbb_table_prefix.'_forums ff ON ff.forum_id = ft.forum_id
              WHERE ft.topic_status != 2
              AND ff.auth_post != 3
              AND u.user_active = 1';
$req_topic = mysql_query($sql_topic);

$yml = array();
while($data = mysql_fetch_array($req_topic))
{
  $yml['sfSimpleForumTopic'][] = '
  t_'.$data['topic_id'].':
    title:              "'.$data['topic_title'].'"
    sfSimpleForumForum: f_'.$data['forum_id'].'
    sfGuardUser:        '.stripText($data['username']).'_user
    is_sticked:         '.($data['topic_type'] ? 'true' : 'false').'
    is_locked:          '.($data['topic_status'] ? 'true' : 'false').'
    nb_views:           '.$data['topic_views'].'
    created_at:         "'.date('Y-m-d H:i:s', $data['topic_time']).'"
    updated_at:         "'.date('Y-m-d H:i:s', $data['topic_time']).'"
    phpbb_id:           '.$data['topic_id'].'
';
}

echo 'sfSimpleForumTopic:';
foreach($yml['sfSimpleForumTopic'] as $oneC)
{
  echo $oneC;
}
echo "\n";

//////// POSTS

$sql_post = ' SELECT p.*, pt.*, u.username, topic_title
              FROM '.$phpbb_table_prefix.'_posts p
              LEFT JOIN '.$phpbb_table_prefix.'_posts_text pt ON pt.post_id = p.post_id
              LEFT JOIN '.$phpbb_table_prefix.'_users u ON u.user_id = p.poster_id
              LEFT JOIN '.$phpbb_table_prefix.'_topics t ON p.topic_id = t.topic_id
              LEFT JOIN '.$phpbb_table_prefix.'_forums ff ON ff.forum_id = p.forum_id
              WHERE ff.auth_post != 3
              AND (u.user_active = 1 OR u.user_id = -1)
              AND t.topic_id IN ( SELECT topic_id
                                      FROM '.$phpbb_table_prefix.'_topics
                                      LEFT JOIN '.$phpbb_table_prefix.'_users u ON u.user_id = topic_poster
                                      WHERE topic_status != 2
                                      AND forum_id != 2
                                      AND u.user_active = 1)
              ORDER BY p.post_id ASC';
$req_post = mysql_query($sql_post);

$yml = array();
while($data = mysql_fetch_array($req_post))
{
  if($data['poster_id'] == -1)
  {
    $user = "\n".'    author_name:        '.$data['post_username'];
  }
  else
  {
    $user = "\n".'    sfGuardUser:        '.stripText($data['username']).'_user';
  }

  $content = bbencode_second_pass($data['post_text'], $data['bbcode_uid']);
  $content = str_replace("\n\r", ' ', $content);
  $content = str_replace("\n", ' ', $content);
  $content = str_replace("\r", "\n      ", $content);
  $content = str_replace("\t", ' ', $content);

  $content = str_replace("(lien externe)", "", $content);
  $content = str_replace(' class="postlink external"', "", $content);
  $content = str_replace(' class="postlink"', "", $content);
  $content = str_replace('<span> </span>', "", $content);
  $content = str_replace('<span class="postbody">', "", $content);
  $content = str_replace('</span>', "", $content);
  $content = str_replace('<span class="genmed">', "", $content);

  $yml['sfSimpleForumPost'][] = '
  p_'.$data['post_id'].':
    sfSimpleForumTopic: t_'.$data['topic_id'].'
    title:              "'.$data['topic_title'].'"'.
    $user.'
    created_at:         "'.date('Y-m-d H:i:s', $data['post_time']).'"
    updated_at:         "'.($data['post_edit_time'] ? date('Y-m-d H:i:s', $data['post_edit_time']) : date('Y-m-d H:i:s', $data['post_time'])).'"
    content:            >
      '.$content.'
    phpbb_id:           '.$data['post_id'].'
';
}

echo 'sfSimpleForumPost:';
foreach($yml['sfSimpleForumPost'] as $oneC)
{
  echo $oneC;
}

?>