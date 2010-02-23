<?php

function pager_navigation($pager, $uri, $options = array())
{
  if (!$pager->haveToPaginate())
  {
    return '';
  }
  
  $navigation = '';    
  
  use_helper('I18N');
  
  $pages_displayed = sfConfig::get('app_sfSimpleForumPlugin_pages_displayed', 5);
  
  $uri .= (preg_match('/\?/', $uri) ? '&' : '?').'page=';
  
  // Previous page
  $navigation .= '<li class="begin">'.(($pager->getPage() != 1) ? link_to(__('Previous'), $uri.$pager->getPreviousPage()) : '&nbsp;').'</li>';
  
  // First page
  if ($pager->getPage() > $pages_displayed + 1 )
  {
    $navigation .= '<li>'.link_to('1', $uri.'1').'</li>';
    if($pager->getPage() > $pages_displayed + 2)
    {
      $navigation .= '<li>&nbsp;..&nbsp</li>';
    }
  }
  
  // Pages one by one
  $max_page = min($pager->getPage() + $pages_displayed, $pager->getLastPage());
  $min_page = max($pager->getPage() - $pages_displayed, 1);
  
  for ($page = $min_page; $page <= $max_page; $page++)
  {
    if($page == $pager->getPage())
    {
      $navigation .= '<li class="current">'.$page.'</li>'; 
    }
    else
    {
      $navigation .= '<li>'.link_to($page, $uri.$page).'</li>';
    }
  }
  
  // Last page
  if ($pager->getPage() < ($pager->getLastPage() - $pages_displayed))
  {
    if ($pager->getPage() < ($pager->getLastPage() - $pages_displayed - 1 ))
    {
      $navigation .= '<li>&nbsp;..&nbsp;';
    }
    $navigation .= '<li>'.link_to($pager->getLastPage(), $uri.$pager->getLastPage()).'</li>';
  }
  
  // Next page
  $navigation .= '<li class="end">'.(($pager->getPage() != $pager->getLastPage()) ? link_to(__('Next'), $uri.$pager->getNextPage()) : '&nbsp;').'</li>';
  
  $css_class = isset($options['class']) ? $options['class'] : 'pagination';
  
  return '<div class="'.$css_class.'"><ul>'.$navigation.'</ul></div>';
}
