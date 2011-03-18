<?php

App::import('Vendor', 'Twitter.oauth/oauth_consumer');
App::import("Component", "Session");
App::import("Component", "Cookie");

class TwitterComponent extends Object
{
	var $controller;
	var $config;
	var $requestToken;
	var $consumer;
	
	function startup(&$controller) {
		$this->controller =& $controller;
		$this->controller->Session =& new SessionComponent();
		$this->controller->Cookie =& new CookieComponent();
		
		$this->config = Configure::read('twitterbot');
		
		if(!isset($this->controller->twitterDeny)){
			$this->controller->twitterDeny = array();
		}
		
		$this->_deny();
	}
	
	/*
		指定したアクションを実行
		@params str $mode
						profile ログインユーザーのプロフィールを取得
						followers フォロワー一覧を取得
		@return array
	*/
	function action($mode)
	{
		$list = array(
			'profile' => 'http://api.twitter.com/account/verify_credentials.json',
			'followers' => 'http://twitter.com/statuses/followers.json',
			'friends' => 'http://twitter.com/statuses/friends.json'
		);
		
		$res = $this->get($list[$mode]);
		return $res;
	}
	
	/*
		指定したユーザーをフレンド（フォローイング）に加える
		@params str $screen_name
		@return array
	*/
	function addFriend($screen_name)
	{
		// http://twitter.com/friendships/create/bob.xml
		$res = $this->consumer->post($this->accessToken->key, $this->accessToken->secret, 'http://twitter.com/friendships/create/okwsjp.xml');
		return $res;
	}
	
	/*
		ダイレクトメッセージを送る
		@params str $screen_name
		@params str $message
		@return array
	*/
	function directMessage($screen_name,$message)
	{
		$res = $this->consumer->post($this->accessToken->key, $this->accessToken->secret, 'http://twitter.com/direct_messages/new.json', array('user' => $screen_name , 'text' => $message));
		return $res;
	}
	
	/*
		指定したメッセージを書き込む
		@params str $message 
		@return array
	*/
	function post($message)
	{
		$this->consumer->post($this->accessToken->key, $this->accessToken->secret, 'http://api.twitter.com/1/statuses/update.json', array('status' => $message));
	}
	
	/*
		指定したURLのJson等を取得
		@params str $url
						profile ログインユーザーのプロフィールを取得
		@return array
	*/
	function get($url)
	{
		$res = json_decode($this->consumer->get($this->accessToken->key, $this->accessToken->secret, $url));
		$res = Set::reverse($res);

		return $res;
	}
	
	
	/*
		ログアウト
	*/
	function logout()
	{
		$this->controller->Session->delete('Twitter');
		$this->controller->Cookie->delete('Twitter');
	}
	
	/*
		現在のアクションがtwitter認証が必要なアクションなら、現在ログイン中かどうかを調べる
	*/
	function _deny()
	{
		$this->consumer = $this->_createConsumer();
		if(in_array($this->controller->action,$this->controller->twitterDeny)){
			
			//ログインしていない場合はログインさせる		
			if(!isset($_GET['oauth_token'])){
				if(!$this->_isLogin()){		
					$this->controller->redirect('https://api.twitter.com/oauth/authorize?oauth_token=' . $this->requestToken->key);	
				} else {
					$this->accessToken->key = $this->controller->Session->read('Twitter.accessToken.key');
					$this->accessToken->secret = $this->controller->Session->read('Twitter.accessToken.secret');	
				}
			} else {
				
				//新規ログインしてきた場合
				$this->accessToken = $this->consumer->getAccessToken('https://api.twitter.com/oauth/access_token',$this->controller->Session->read('Twitter.requestToken'));
				
				$this->controller->Session->write('Twitter.accessToken.key', $this->accessToken->key);
				$this->controller->Session->write('Twitter.accessToken.secret', $this->accessToken->secret);
				
				
				//ここでクッキーに書き込み
				$this->controller->Cookie->write('Twitter.accessToken.key', $this->accessToken->key, false, '+1 years');
				$this->controller->Cookie->write('Twitter.accessToken.secret', $this->accessToken->secret, false, '+1 years');
				
				$this->controller->redirect($this->controller->Session->read('Twitter.callbackUrl'));
				
			}
						
		}
	}
	
	//現在、ログインしているか調べる
	/*
		@return bool ログインしていれば true
							していなければ false
	*/
	function _isLogin()
	{
		$res = true;
		
		//クッキーがあればクッキーを使う
		if($this->controller->Cookie->read('Twitter.accessToken.key')){
			$this->controller->Session->write('Twitter.accessToken.key',$this->controller->Cookie->read('Twitter.accessToken.key'));
			$this->controller->Session->write('Twitter.accessToken.secret',$this->controller->Cookie->read('Twitter.accessToken.secret'));
		}
		
		//新規ログインモードへ
		if(!$this->controller->Session->read('Twitter.accessToken.key')){
			$this->controller->Session->write('Twitter.callbackUrl',FULL_BASE_URL.$this->controller->here);
			
			$this->requestToken = $this->consumer->getRequestToken('https://api.twitter.com/oauth/request_token',$this->controller->Session->read('Twitter.callbackUrl'));
			$this->controller->Session->write('Twitter.requestToken', $this->requestToken);
			$res = false;
		}
		
		return $res;
		
	}
	
	function _createConsumer() {
		return new OAuth_Consumer($this->config['key'],$this->config['secret']);
	}
}

?>