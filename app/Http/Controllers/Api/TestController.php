<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\UserModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
class TestController extends Controller
{
    public function test(){
    	$user_info=[
    		'uid'=>123,
    		'name'=>'lishi',
    		'email'=>'lishi@qq.com',
    		'age'=>18
    	];

    	$response=[
    		'errno'=>0,
    		'msg'=>'ok',
    		'data'=>[
    			'user_info'=>$user_info,
    		]
    	];
    	echo json_encode($response);
    }
    public function reg(Request $request){
    	echo '<pre>';print_r($request->input());echo '</pre>';
    	$pass1=$request->input('pass1');
    	$pass2=$request->input('pass2');
    	if($pass1!=$pass2){
    		die("两次输入不一样");
    	}
    	$password=password_hash($pass1,PASSWORD_BCRYPT);
    	$data=[
    		'email'=>$request->input('email'),
    		'name'=>$request->input('name'),
    		'password'=>$password,
    		'mobile'=>$request->input('mobile'),
    		'last_login'=>time(),
    		'last_ip'=>$_SERVER['REMOTE_ADDR'],
    	];
    	$uid=UserModel::insertGetId($data);
    	var_dump($uid);

    }

  	public function login(Request $request){
  		$name=$request->input('name');
  		$pass=$request->input('pass');
  		//echo "pass: ".$pass;echo '<br>';
  		$u=UserModel::where(['name'=>$name])->first();
  		//var_dump($u);die;
  		if($u){
  			//echo '<pre>';print_r($u);echo '</pre>';
  			//密码
  			if(password_verify($pass,$u->password)){
  				echo "登陆成功";
  				$token=Str::random(32);
  				$response=[
  						'errno'=>0,
  						'msg'=>'ok',
  						'data'=>[
  							'token'=>$token
  						]
  				];
  				
  			}else{
  				$response=[
  					'errno'=>400003,
  					'msg'=>'密码不正确',
  				];
  			}
  			
  		}else{
  			$response=[
  					'errno'=>400004,
  					'msg'=>'没有此用户',
  				];
  		}
		return $response;
  	}
  	public function userList()
    {
        $user_token = $_SERVER['HTTP_TOKEN'];
        echo 'user_token: '.$user_token;echo '</br>';
        $current_url = $_SERVER['REQUEST_URI'];
        echo "当前URL: ".$current_url;echo '<hr>';
        //echo '<pre>';print_r($_SERVER);echo '</pre>';
        //$url = $_SERVER[''] . $_SERVER[''];
        $redis_key = 'str:count:u:'.$user_token.':url:'.md5($current_url);
        echo 'redis key: '.$redis_key;echo '</br>';
        $count = Redis::get($redis_key);        //获取接口的访问次数
        echo "接口的访问次数： ".$count;echo '</br>';
        if($count >= 10){
            echo "请不要频繁访问此接口，访问次数已到上限，请稍后再试";
            Redis::expire($redis_key,10);
            die;
        }
        $count = Redis::incr($redis_key);
        echo 'count: '.$count;
    }
    public function postamanl(){

        $data = [
            'user_name' => 'zhangsan',
            'email'     => 'zhangsan@qq.com',
            'amount'    => 10000
        ];

        echo json_encode($data);

       // //获取用户标识
       //  $token = $_SERVER['HTTP_TOKEN'];
       //  // 当前url
       //  $request_uri = $_SERVER['REQUEST_URI'];
       //  $url_hash = md5($token . $request_uri);
       //  //echo 'url_hash: ' .  $url_hash;echo '</br>';
       //  $key = 'count:url:'.$url_hash;
       //  //echo 'Key: '.$key;echo '</br>';
       //  //检查 次数是否已经超过限制
       //  $count = Redis::get($key);
       //  echo "当前接口访问次数为：". $count;echo '</br>';
       //  if($count >= 5){
       //      $time = 10;     // 时间秒
       //      echo "请勿频繁请求接口, $time 秒后重试";
       //      Redis::expire($key,$time);
       //      die;
       //  }
       //  // 访问数 +1
       //  $count = Redis::incr($key);
       //  echo 'count: '.$count;
    }
   }
