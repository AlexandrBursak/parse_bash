<?php

class pasre_bash
{

  const NUMBER_PAGE = 5;
  const PARSE_LINK = 'http://bash.im';
  public $current_page = 0;
  public $default_link = '';
  public $array_content = [];

  function __construct()
  {

    $content = $this->get_something( self::PARSE_LINK );

    $content = iconv('CP1251', "UTF-8", $content);
    $this->parse_navigation($content);


    for( $i = $this->current_page; $i > $this->current_page-self::NUMBER_PAGE; $i-- ) {
      $full_page = self::PARSE_LINK . $this->default_link . $i;
      if ($i != $this->current_page)
      {
        $content = $this->get_something( $full_page );
        $content = iconv('CP1251', "UTF-8", $content);
      }
      $this->parse_current_page( $content );
    }

    $this->render();
  }

  function render()
  {
      echo '<div class="counter">' . count($this->array_content) . '</div>';

    foreach ($this->array_content as $message)
    {
      echo '<div style="padding:10px;">' . $message . '</div>';
    }
  }

  function parse_current_page( $content )
  {
    $pattern = '/<div class="text">(.*)<\/div>/i';
    preg_match_all($pattern, $content, $matches);

    foreach ($matches[1] as $mess)
    {
      $this->array_content[] = $mess;
    }
  }

  function parse_navigation( $content )
  {
    $pattern = '/<form method="post" action="\/index">(.*)<\/form>/i';
    $value = preg_match($pattern, $content, $match);
    $navigation = $match[1];

    $pattern = '/numeric="integer" min="1" max="(\d{1,5})"/i';
    $value = preg_match($pattern, $navigation, $match);
    $this->current_page = $match[1];

    $pattern = '/<a href="([\/a-zA-Z]+)\d{0,}">/i';
    $value = preg_match($pattern, $navigation, $match);
    $this->default_link = $match[1];
  }

  function get_something( $link )
  {
    $content = @file_get_contents($link);
    if ( $content )
    {
      return $content;
    }
    else
    {
      return false;
    }
  }

}

new pasre_bash();