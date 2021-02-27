<?php
class Dl {
    
    public function __construct() {
        
    }
    
    public function dlVideo($dy, $type) {
        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            require_once('/Applications/phpstudy/WWW/douyin/curl.php');
        } else {
            require_once('/www/wwwroot/'.$_SERVER['SERVER_NAME'].'/curl.php');
        }
        if ($dy) {
            preg_match('/[a-zA-z]+:\/\/[^\s]*/', $dy, $url);
            if (preg_match('/v.douyin.com/', $url[0]) == 1) {
                preg_match('/(?<=sec_uid=)[A-Za-z0-9-_]+/', get_url($url[0]), $sec_uid);
                if (count($sec_uid)) {
                    $max_cursor = 0;
                    $total_new = 0;
                    $total_old = 0;
                    do {
                        $video_info = $this->getVideoList($sec_uid[0], $max_cursor);
                        $aweme_list = $video_info['aweme_list'];
                        if ($max_cursor == 0) {
                            $author = $aweme_list[0]['author']['nickname'];
                            $dir = 'dl/'.$author.'/';
                            if (!is_dir($dir)) {
                                mkdir($dir, 0777, true);
                            }
                            $history_txt = fopen($dir.'history.txt', 'a');
                            $history = file($dir.'history.txt');
                        }
                        $has_more = $type == 1 ? false : $video_info['has_more'];
                        $max_cursor = $video_info['max_cursor'];
                        for ($i = 0; $i < count($aweme_list); $i++) {
                            $aweme_id = $aweme_list[$i]['aweme_id'];
                            $video_url = $aweme_list[$i]['video']['play_addr']['url_list'][0];
                            $desc = $aweme_list[$i]['desc'];
                            if (!in_array($aweme_id."\r\n", $history)) {
                                $file_name = $desc ? $desc : $aweme_id;
                                file_put_contents($dir.$file_name.'.mp4', fopen($video_url, 'r'));
                                fwrite($history_txt, $aweme_id."\r\n");
                                $total_new = $total_new + 1;
                            } else {
                                $total_old = $total_old + 1;
                            }
                        }
                    } while ($has_more);
                    fclose($history_txt);
                    $res = array(
                        'code' => 200,
                        'data' => array(
                            'author' => $author,
                            'total' => array(
                                'all' => $total_new + $total_old,
                                'new' => $total_new,
                                'old' => $total_old
                            )
                        ),
                        'msg' => '下载成功'
                    );
                } else {
                    $loc = get_headers($url[0], true)['location'];
                    $start = 'video/';
                    $end = '/?region';
                    $id = $this->getId($loc,$start,$end);
                    $arr = json_decode(get_url("https://www.iesdouyin.com/web/api/v2/aweme/iteminfo/?item_ids=$id"), true);
                    preg_match('/href="(.*?)">Found/', get_url(str_replace('playwm', 'play', $arr['item_list'][0]["video"]["play_addr"]["url_list"][0])), $matches);
                    
                    $title = $arr['item_list'][0]["share_info"]["share_title"];
                    $video = $matches[1];
                    $music = $arr['item_list'][0]['music']['play_url']["url_list"][0];
                    $cover = $arr['item_list'][0]['video']["origin_cover"]["url_list"][0];
                    
                    $res = array(
                        'code' => 200,
                        'data' => array(
                            'title' => $title,
                            'video' => $video,
                            'music' => $music,
                            'cover' => $cover
                        ),
                        'msg' => '请求成功'
                    );
                }
            } else {
                $res = array(
                    'code' => 201,
                    'msg' => '请传入正确的抖音视频链接/作者主页链接'
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
    
    // 获取视频列表
    private function getVideoList($sec_uid, $max_cursor) {
        $url = 'https://www.iesdouyin.com/web/api/v2/aweme/post/?sec_uid='.$sec_uid.'&max_cursor='.$max_cursor.'&count=2000';
        do {
            $res = json_decode(get_url($url), true);
        } while ($res['aweme_list'] == []);
        return $res;
    }
    
    // 获取视频id
    private function getId($content,$start,$end) {
        $r = explode($start, $content);
        if (isset($r[1])) {
        $r = explode($end, $r[1]);
        return $r[0];
        }
        return '';
    }
}
?>