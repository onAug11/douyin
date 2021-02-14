<?php
class Ta {
    public $conn;
    
    public function __construct() {
        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            require_once('/Applications/phpstudy/WWW/douyin/MySql.php');
        } else {
            require_once('/www/wwwroot/'.$_SERVER['SERVER_NAME'].'/MySql.php');
        }
        
        $this->conn = new mysqli($servername, $username, $password, $dbname);
    }
    
    public function get_ta_info($dy) {
        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            require_once('/Applications/phpstudy/WWW/douyin/curl.php');
        } else {
            require_once('/www/wwwroot/'.$_SERVER['SERVER_NAME'].'/curl.php');
        }
        preg_match('/[a-zA-z]+:\/\/[^\s]*/', $dy, $url);
        if (preg_match('/v.douyin.com/', $url[0]) == 1) {
            preg_match('/(?<=sec_uid=)[A-Za-z0-9-_]+/', get_url($dy), $sec_uid);
            if ($sec_uid) {
                $user_info = json_decode(get_url('https://www.iesdouyin.com/web/api/v2/user/info/?sec_uid='.$sec_uid[0]), true)['user_info'];
                $uid = $user_info['uid']; // uid
                $nickname = $user_info['nickname']; // 昵称
                $signature = $user_info['signature']; // 签名
                $avatar = $user_info['avatar_larger']['url_list'][0]; // 头像
                $following_count = $user_info['following_count']; // 关注数
                $follower_count = $user_info['follower_count']; // 粉丝数
                $total_favorited = (int)$user_info['total_favorited']; // 获赞数
                $aweme_count = $user_info['aweme_count']; // 作品数
                $favoriting_count = $user_info['favoriting_count']; // 喜欢作品数
                
                $nickname_encode = $this->emoji_encode($nickname);
                $signature_encode = $this->emoji_encode($signature);
                
                $sql = "select following_count, follower_count, total_favorited, aweme_count, favoriting_count from douyin_ta where uid = '$uid'";
                $result = $this->conn->query($sql);
                
                if ($result->num_rows == 1) {
                    $res = $result->fetch_assoc();
                    $following = $following_count - $res['following_count'];
                    $follower = $follower_count - $res['follower_count'];
                    $favorited = $total_favorited - $res['total_favorited'];
                    $aweme = $aweme_count - $res['aweme_count'];
                    $favoriting = $favoriting_count - $res['favoriting_count'];
        
                    $sql = "update douyin_ta set nickname = '$nickname_encode', signature = '$signature_encode', avatar = '$avatar', following_count = $following_count, follower_count = $follower_count, total_favorited = $total_favorited, aweme_count = $aweme_count, favoriting_count = $favoriting_count where uid = $uid";
                    $result = $this->conn->query($sql);
                    $res_following = $following == 0 ? null : $nickname.($following > 0 ? '新关注了' : '取消关注了').abs($following).'个人';
                    $res_follower = $follower == 0 ? null : $nickname.($follower > 0 ? '新增了' : '掉了').abs($follower).'个粉丝';
                    $res_favorited = $favorited == 0 ? null : $nickname.'的作品'.($favorited > 0 ? '新增了' : '减少了').abs($favorited).'个赞';
                    $res_aweme = $aweme == 0 ? null : $nickname.($aweme > 0 ? '新发布了' : '删除了').abs($aweme).'个作品';
                    $res_favoriting = $favoriting == 0 ? null : $nickname.($favoriting > 0 ? '新点赞了' : '取消点赞了').abs($favoriting).'个作品';
                    $res = array(
                        'code' => 200,
                        'data' => array(
                            'uid' => $uid,
                            'nickname' => $nickname,
                            'signature' => $signature,
                            'avatar' => $avatar,
                            'count' => array(
                                'following_count' => $following_count,
                                'follower_count' => $follower_count,
                                'total_favorited' => $total_favorited,
                                'aweme_count' => $aweme_count,
                                'favoriting_count' => $favoriting_count
                            ),
                            'change' => array(
                                'following' => $res_following,
                                'follower' => $res_follower,
                                'favorited' => $res_favorited,
                                'aweme' => $res_aweme,
                                'favoriting' => $res_favoriting
                            )
                        ),
                        'msg' => '查询成功'
                    );
                    
                } else {
                    $sql = "insert into douyin_ta set uid = '$uid', url = '$dy', nickname = '$nickname_encode', signature = '$signature_encode', avatar = '$avatar', following_count = $following_count, follower_count = $follower_count, total_favorited = $total_favorited, aweme_count = $aweme_count, favoriting_count = $favoriting_count";
                    $this->conn->query($sql);
                    $res = array(
                        'code' => 200,
                        'data' => array(
                            'uid' => $uid,
                            'nickname' => $nickname,
                            'signature' => $signature,
                            'avatar' => $avatar,
                            'count' => array(
                                'following_count' => $following_count,
                                'follower_count' => $follower_count,
                                'total_favorited' => $total_favorited,
                                'aweme_count' => $aweme_count,
                                'favoriting_count' => $favoriting_count
                            )
                        ),
                        'msg' => '关注成功'
                    );
                }
            } else {
                $res = array(
                    'code' => 201,
                    'msg' => '请输入正确的抖音作者主页链接'
                );
            }
        } else {
            $res = array(
                'code' => 201,
                'msg' => '缺少dy参数'
            );
        }
        return $res;
    }
    
    // 对emoji表情转义
    private function emoji_encode($str){
        $strEncode = '';
     
        $length = mb_strlen($str,'utf-8');
     
        for ($i=0; $i < $length; $i++) {
            $_tmpStr = mb_substr($str,$i,1,'utf-8');    
            if(strlen($_tmpStr) >= 4){
                $strEncode .= '[[EMOJI:'.rawurlencode($_tmpStr).']]';
            }else{
                $strEncode .= $_tmpStr;
            }
        }
     
        return $strEncode;
    }
    
    // 对emoji表情转反义
    private function emoji_decode($str){
        $strDecode = preg_replace_callback('|\[\[EMOJI:(.*?)\]\]|', function($matches){  
            return rawurldecode($matches[1]);
        }, $str);
     
        return $strDecode;
    }
}
?>