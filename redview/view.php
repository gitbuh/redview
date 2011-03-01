<?php
/**
 RedView View.

 Provides end-to-end form submission handling.
 */
class RedView_View {

	public $attribs;
	public $template;

	protected $_vars;

	public function beforeRender () {
	}

	public function render () {
	}

	public function afterRender () {
	}

	public function loadTemplate () {
		$cache=null;
		$template=@$this->attribs['template'];
		if (!$template) $template=$this->template;
		if ($template) $cache = RedView::parse($template);
		if ($cache) require_once $cache;
	}

	public function loadMarkup ($file) {
		if ($this->_vars) {
			extract($this->_vars);
		}
		include $file;
	}

	public function set ($k, $v) {
		$this->_vars[$k]=$v;
	}
	public function get ($k) {
		return $this->_vars[$k];
	}



	/**
	 __sleep
	 Magic method, called on serialize. Properties named with a leading underscore will not be serialized.
	 @return array of names of properties to serialize.
	 */
	public function __sleep () {
		$propNames;
		foreach (array_keys(get_object_vars($this)) as $k) if ($k{0}!='_') $propNames[]=$k;
		return $propNames;
	}

	/**
	 __wakeup
	 Magic method, called on unserialize.
	 */
	public function __wakeup () {
		$this->__construct();
	}

}

