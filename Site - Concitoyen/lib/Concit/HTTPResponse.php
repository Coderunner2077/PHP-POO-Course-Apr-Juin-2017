<?php
namespace Concit;

class HTTPResponse extends ApplicationComponent {
	protected $page;
	
	public function setPage(Page $page) {
		$this->page = $page;
	}
	
	public function redirect($location) {
		header('Location: '.$location);
		exit;
	}
	
	public function redirect404() {
		$this->page = new Page($this->app);
		$this->page->setContentFile(__DIR__.'/../../Errors/404.html');
		header('HTTP/1.0 404 Not Found');
		$this->send();
	}
	
	public function redirect403() {
		$this->page = new Page($this->app);
		$this->page->setContentFile(__DIR__.'/../../Errors/403.html');
		header('HTTP/1.0 403 Forbidden');
		$this->send();
	}
	
	public function send() {
		exit($this->page->getGeneratedPage());
	}
	
	public function addHeader($header) {
		header($header);
	}
	
	public function setCookie($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = true) {
		setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
	}
}