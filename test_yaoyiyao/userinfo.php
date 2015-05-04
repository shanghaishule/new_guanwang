<?php
if (isset($_GET['code'])){
   $code=$_GET['code'];
   //echo $code.'<br/>';
   $appid = 'wxd39cddbb26e34013'; //公共账号 appid        
   $secret = '0f2a6b9f990d33001b4c993841367edc' ; //公众账号AppSecret
   $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$_GET['code'].'&grant_type=authorization_code'; 
	//echo $url.'<br/>';
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);  
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$res = curl_exec($ch);  
	curl_close($ch);  
	$json_obj = json_decode($res,true);  
	
	//var_dump($json_obj);
	
	$access_token = $json_obj['access_token'];  
	$openid = $json_obj['openid'];  
	$get_user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';  
	  
	$ch = curl_init();  
	curl_setopt($ch,CURLOPT_URL,$get_user_info_url);  
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$res = curl_exec($ch);  
	curl_close($ch);  
	  
	//解析json  
	$user_obj = json_decode($res,true);  
	$_SESSION['user'] = $user_obj;  
	//echo $user_obj['nickname'].'<br/>';
	//echo $user_obj['country'].'<br/>';
	//echo $user_obj['sex'].'<br/>'; 
	echo "<table style=\"font-size:30px\"><tr><td>姓名</td><td>国家</td><td>性别</td></tr>";
	echo "<tr><td>".$user_obj['nickname']."</td><td>".$user_obj['country']."</td><td>".$user_obj['sex']."</td></tr></table>";
	
	}else{
    echo "NO CODE";
}
class userinfo{

public function Oauth($code='',$mode=0){ 
$appid = 'wxd39cddbb26e34013'; //公共账号 appid        
$secret = '0f2a6b9f990d33001b4c993841367edc' ; //公众账号AppSecret        
if($code=='') $code = $_REQUEST['code'] ; //接收参数        
if(!$code) return false ;       
 $cul = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code' ; 
 $cx = file_get_contents($cul) ; 
 $bx = json_decode($cx,true) ;
 if($bx['errcode']){     
 //第一步 根据code获取refresh_token        
 $this->restat = 0 ;       
 $this->errmsg = $bx ; return ;     
 }       
 $rtoken = $bx['refresh_token'] ; 
 $rurl = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid='.$appid.'&grant_type=refresh_token&refresh_token='.$rtoken ;   
 $rr = file_get_contents($rurl) ; 
 $rr = json_decode($rr,true) ; 
 if($rr['errcode']){  
 //第二步 根据refresh_token获取的access_token和openid    
 $this->restat = 0 ;    
 $this->errmsg = $bx ; return ;    
 }       
 $acct = $rr['access_token'] ;    
 //file_put_contents('abc.txt', $acct);        
 $this->auth_access_token = $acct ; //存放认证的token        
 $openid = $rr['openid'] ;        
 if($mode == 0 ) return ;       
 //第三步拉取信息       
 $purl = "https://api.weixin.qq.com/sns/userinfo?access_token=".$acct."&openid=".$openid."&lang=zh_CN" ;   
 $xv = file_get_contents($purl) ;        //file_put_contents('xv.txt', $xv);        /*$xv返回数据格式        {"openid":"XXX","nickname":"Mini_Ren","sex":1,"language":"zh_CN","city":"郑州","province":"河南","country":"中国","headimgurl":"","privilege":[]}        */   

 
 $xv = json_decode($xv,true) ;     
 if($xv['errcode']){    
 $this->restat = 0 ;       
 $this->errmsg = $bx ; return ;      
 }        $this->res = $xv ;      
 return $xv ; //带有用户信息数组
 }

}

?>