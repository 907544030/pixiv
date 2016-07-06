<?php
/*
不支持R18
*/
set_time_limit(60);
$id = $_GET['id'];
function getcontents($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.99 Safari/537.36 LBBROWSER');
    $str = curl_exec($ch);
    curl_close($ch);
    return $str;
}
$str = getcontents('http://www.pixiv.net/member_illust.php?mode=medium&illust_id=' . $id);
function get_pixiv_img($url)
{
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_REFERER, "http://www.pixiv.net/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}
preg_match_all('/data-title=\"registerImage\"><img src=\"([\\w\\W]*?)\" alt=/', $str, $img, PREG_PATTERN_ORDER); //匹配介绍
preg_match_all('/<meta property=\"og:url\" content=\"([\\w\\W]*?)\">/', $str, $url, PREG_PATTERN_ORDER); //匹配url
preg_match_all('/<meta property=\"twitter:title\" content=\"([\\w\\W]*?)\">/', $str, $title, PREG_PATTERN_ORDER); //匹配介绍
preg_match_all('/<meta property=\"twitter:description\" content="([\\w\\W]*?)\">/', $str, $description, PREG_PATTERN_ORDER); //匹配介绍
preg_match_all('/」\/「([\\w\\W]*?)」[pixiv]/', $str, $author, PREG_PATTERN_ORDER);
$matchimg = str_ireplace('c/600x600/img-master', 'img-original', $img[1][0]);
$matchimg = str_ireplace('_master1200', '', $matchimg);
file_put_contents(basename($matchimg), get_pixiv_img($matchimg));
$imgdetail = getimagesize(basename($matchimg));
echo json_encode(array(
    'url' => 'http://www.pixiv.net/member_illust.php?mode=medium&illust_id=' . $id,
    'title' => $title[1][0],
    'imgurl' => basename($matchimg),
    'visitable' => true,
    'author' => $author[1][0],
    'info' => $description[1][0],
    'width' => $imgdetail[0],
    'height' => $imgdetail[1]
));