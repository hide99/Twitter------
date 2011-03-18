<?
App::import("Component", "twitter.twitter");
App::import("Component", "Session");

class User extends Model {
    
	var $useTable = false;
}





class UserController extends Controller {
    
	var $components = array('twitter.Twitter','Session');
	
	//ツイッター auth を完了していなければ、ツイッター認証画面に飛ばす
	var $twitterDeny = array(
					'mypage'
				);
 
    function flash($message, $url, $pause = 1, $layout = 'flash') {
        $this->flashMessage = $message;
        $this->flashUrl = $url;
    }
	
    function redirect($url, $status = null, $exit = true) {
        $this->redirectUrl = $url;
    }
 
    function render($action = null, $layout = null, $file = null) {
        $this->renderedAction = $action;
    }
 
    function _stop($status = 0) {
        $this->stopped = $status;
    }
}



class TwitterTestCase extends CakeTestCase {
	
	
	function startTest($method) {
		
		//componentを読み出す
		$this->TwitterComponentTest =& new TwitterComponent();
		$controller = new UserController(); 
		$controller->Session =& new SessionComponent();
		$this->TwitterComponentTest->startup(&$controller);
		
		echo '<h3>Test... ( コンポーネント )　'.$method.'</h3>';
	}
	
	function endTest() {
		unset($this->controller);
		ClassRegistry::flush();
		echo '<hr/>';
	}
	
	function testDeny()
	{
		$this->TwitterComponentTest =& new TwitterComponent();
		$controller = new UserController(); 
		$controller->Session =& new SessionComponent();
		$controller->action = 'mypage';
		
		$controller->requestToken = array();
		$this->TwitterComponentTest->startup(&$controller);
		
		
		
		
		
		
	}
	

	

}

?>