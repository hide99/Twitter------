***************************************************************************
	Cakephp ツイッター oAuth プラグイン
	Copyright webservice,inc ( http://www.okws.jp/ )
***************************************************************************

--------------------------------------------------
■特徴
--------------------------------------------------

・ツイッターAPIにログイン
・クッキー及びセッションでログイン情報を保持
・返り値は全て配列

--------------------------------------------------
■初期設定
--------------------------------------------------

bootstrap.php に以下を設定

Configure::write('twitterbot',array(
    'key' => 'xxxxxxxxxxxxxxxxxxxx',//TWITTER_CONSUMER_KEY
    'secret' => 'xxxxxxxxxxxxxxxx',//TWITTER_CONSUMER_SECRET
);



使用したい コントローラー 例 UsersController

var $components = array('twitter.Twitter');

//ツイッターログインが必要なアクション
var $twitterDeny = array(
    'kakiko',
);

class LabosController extends AppController
{	
	var $components = array('twitter.Twitter');
	var $twitterDeny = array(
		'kakiko',
	);
	
	function kakiko()
	{
		//指定メッセージをツイート
		//$this->Twitter->post('やしきたかじん');
		
		//自分自身のプロフィールを取得
		//$res = $this->Twitter->action('profile');
		
		//pr($res['friends_count']);//フォローしてい人の数
		//pr($res['followers_count']);//フォローされている人の数
		
		
		
		//フォロワー一覧を取得
		//$res = $this->Twitter->action('followers');
		
		//フレンド（フォローイング一覧を取得）
		//$res = $this->Twitter->action('friends');
		
		//指定した screen_name の人にダイレクトメッセージを送る
		//$res = $this->Twitter->directMessage('okwsjp','かたこりこり');
		
		//指定した screen_name の人を友達に追加
		//$res = $this->Twitter->addFriend('okwsjp');
		
		//ビューで使うと良い
		////echo '<img src="http://img.tweetimag.es/i/'.$res['screen_name'].'_n">';//プロフィール画像を表示
		
	}
	
	function logout()
	{
		$this->Twitter->logout();
		$this->flash('ツイッターよりログアウトしました','/');
	}
}



