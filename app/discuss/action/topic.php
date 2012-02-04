<?php
defined('IN_TS') or die('Access Denied.');

/* 
 * 小组话题内容页
 */

$topicid = intval($_GET['id']);

if($topicid == 0){
	header("Location: ".SITE_URL);
	exit;
}

$new['group']->isTopic($topicid);

$strTopic = $new['group']->getOneTopic($topicid);
	
//帖子分类
if($strTopic['typeid'] != '0'){
	$strTopic['type'] = $db->once_fetch_assoc("select * from ".dbprefix."discuss_topics_type where typeid='".$strTopic['typeid']."'");
}

//小组
$strGroup = $db->once_fetch_assoc("select * from ".dbprefix."discuss where groupid='".$strTopic['groupid']."'");

//判断会员是否加入该小组
$groupid = intval($strGroup['groupid']);

$userid = intval($TS_USER['user']['userid']);

//浏览方式
if($strGroup['isopen']=='1'){
	
	$title = $strTopic['title'];
	include template("topic_isopen");
	
}else{
	
	
	//帖子标签
	$strTopic['user']	= aac('user')->getUserForApp($strTopic['userid']);
	
	$title = $strTopic['title'];
	
	//评论列表开始
	$sc = isset($_GET['sc']) ? $_GET['sc'] : 'desc';
	
	$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
	
	//倒序asc
	if($sc=='asc'){
		$url = SITE_URL.tsurl('discuss','topic',array('id'=>$topicid,'sc'=>$sc,'page'=>''));
	}else{
		$url = SITE_URL.tsurl('discuss','topic',array('id'=>$topicid,'page'=>''));
	}
	
	
	$lstart = $page*15-15;
	
	$arrComment = $db->fetch_all_assoc("select * from ".dbprefix."discuss_topics_comments where `topicid`='$topicid' order by addtime $sc limit $lstart,15");
	foreach($arrComment as $key=>$item){
		$arrTopicComment[] = $item;
		$arrTopicComment[$key]['user'] = aac('user')->getUserForApp($item['userid']);
		$arrTopicComment[$key]['recomment'] = $new['group']->recomment($item['referid']);
	}
	
	$commentNum = $db->once_fetch_assoc("select count(*) from ".dbprefix."discuss_topics_comments where `topicid`='$topicid'");
	
	$pageUrl = pagination($commentNum['count(*)'], 15, $page, $url);
	
	//最新帖子
	$newTopics = $db->fetch_all_assoc("select topicid,userid,title from ".dbprefix."discuss_topics where groupid='$groupid' and isshow='0' order by addtime desc limit 6");
	
	foreach($newTopics as $key=>$item){
		$newTopic[] = $item;
		$newTopic[$key]['user'] = aac('user')->getSimpleUser($item['userid']);
	}
	
	($page > 1) ? $titlepage = " - 第".$page."页" : $titlepage='';
	
	$title = $title.$titlepage.' - '.$strGroup['groupname'];
	include template('topic');
	
	$db->query("update ".dbprefix."discuss_topics set `count_view`=count_view+1 where topicid='".$topicid."'");
	
}