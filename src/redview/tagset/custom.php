<?php

class RedView_Tagset_Custom extends RedView_ATag {

  public static function register (RedView_Parser $parser) {
    $parser->register('r:each', 'RedView_Tag_Each');
    $parser->register('r:else', 'RedView_Tag_Else');
    $parser->register('r:format', 'RedView_Tag_Format');
    $parser->register('r:if', 'RedView_Tag_If');
    $parser->register('r:load', 'RedView_Tag_Load');
    $parser->register('r:slot', 'RedView_Tag_Slot');
    $parser->register('r:view', 'RedView_Tag_View');
  }

}

