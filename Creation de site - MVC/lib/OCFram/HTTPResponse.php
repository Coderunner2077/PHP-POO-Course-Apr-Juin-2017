<?php
namespace OCFram;

class HTTPResponse extends ApplicationComponent {
	protected $page;
	
	public function setPage(Page $page) {
		$this->page = $page;
	}
	
	public function addHeader($header) {
		header($header);
	}
	
	public function redirect($location) {
		header('Location: '.$location);
	}
	
	public function redirect404() { // -! 1
		$this->page = new Page($this->app);
		
		$this->page->setContentFile(__DIR__.'/../../Errors/404.html');
		header('HTTP/1.0 404 Not Found; charset=utf-8');
		
		$this->send();
	}
	
	public function send() {	// -! 1
		exit($this->page->getGeneratedPage());	
	}
	
	public function addCookie($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = true) {
		setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
	}
}