<?php
//project homepage: https://github.com/ashwinks/PHP-LinkedIn-SDK
//APIs on LinkedIn: https://developer.linkedin.com/apis
//Demo created by: Ondrej@tvarwebu.cz 2014-04-15
require 'LinkedIn/LinkedIn.php';
session_start();
$li = new Linkedin\LinkedIn(
  array(
    'api_key' => 'yourapikey',
    'api_secret' => 'yourapisecret',
    'callback_url' => 'https://yourdomain.com/path_to_script/demo_example.php'
  )
);
  $group_id = 0; // TO BE MODIFIED FOR GROUP YOU HAVE ACCESS TO
  $company_id = 0; // TO BE MODIFIED FOR GROUP YOU HAVE ACCESS TO

if(!empty($_REQUEST['act']) && $_REQUEST['act']=='forget_token'){
  unset($_SESSION['linked_in_token']);
}
if(!empty($_REQUEST['act']) && $_REQUEST['act']=='retrieve_token'){
$url = $li->getLoginUrl(
  array(
    //LinkedIn\LinkedIn::SCOPE_BASIC_PROFILE,
    //LinkedIn\LinkedIn::SCOPE_EMAIL_ADDRESS,
    LinkedIn\LinkedIn::SCOPE_NETWORK,
    LinkedIn\LinkedIn::SCOPE_FULL_PROFILE,
    LinkedIn\LinkedIn::SCOPE_READ_WRITE_GROUPS,
    LinkedIn\LinkedIn::SCOPE_WRITE_MESSAGES,
    //LinkedIn\LinkedIn::SCOPE_READ_WRITE_COMPANY,
    LinkedIn\LinkedIn::SCOPE_READ_WRTIE_UPDATES
  )
);
Header("Location: {$url}");
die();

}elseif(!empty($_REQUEST['code'])){
  $token = $li->getAccessToken($_REQUEST['code']);
  $token_expires = $li->getAccessTokenExpiration();
  echo "Token was successfully received<br />";
  //var_dump($token);
  //var_dump($token_expires);
  $_SESSION['linked_in_token'] = $token;
}
if(!empty($_SESSION['linked_in_token'])){
  echo '<a href="?act=forget_token">Forget Token</a> ';
  echo '<a href="?act=load_profile">Load Profile</a> ';
  echo '<a href="?act=load_groups">Load Group</a> ';
  echo '<a href="?act=load_groups_posts">Load Group Posts</a> ';
  echo '<a href="?act=load_connections">Load Connections</a> ';
  echo '<a href="?act=load_companies">Load Companies</a> ';
  echo '<a href="?act=send_comment_form">Send Comment Form</a> ';
  //echo '<a href="?act=send_comment">Send Comment</a> ';
  echo '<a href="?act=send_comment_group_form">Send Comment Group Form</a> ';
  echo '<a href="?act=send_comment_company_form">Send Comment Company Form</a> ';

  echo '<a href="?act=test">Test</a> ';

  echo '<hr />';
}else{
  echo '<a href="?act=retrieve_token">Retrieve token</a> ';
}
if(!empty($_REQUEST['error']))
  echo "<br />Error: {$_REQUEST['error']}<br />";
if(!empty($_REQUEST['error_description']))
  echo "Description: {$_REQUEST['error_description']}<br />";

if(!empty($_SESSION['linked_in_token'])){
  //var_dump($_SESSION['linked_in_token']);
  $li->setAccessToken($_SESSION['linked_in_token']);
}
if(!empty($_REQUEST['act']) && $_REQUEST['act']=='load_profile'){
  $info = $li->get('/people/~:(first-name,last-name,positions)');
  print_r($info);
}
if(!empty($_REQUEST['act']) && $_REQUEST['act']=='load_groups'){
  $info = $li->get('/people/~/group-memberships:(group:(id,name),membership-state)', array('count'=>5,'start'=>0));
  print_r($info);
}
if(!empty($_REQUEST['act']) && $_REQUEST['act']=='load_groups_posts'){
  $info = $li->get('/people/~/group-memberships:(group:(id,name,posts;count=15,site-group-url))', array('membership-state'=>'owner','count'=>3,'start'=>0));
  print_r($info);
}
if(!empty($_REQUEST['act']) && $_REQUEST['act']=='load_connections'){
  $info = $li->get('/people/~/connections');
  print_r($info);
}
if(!empty($_REQUEST['act']) && $_REQUEST['act']=='load_companies'){
  $li->debug = true;
  $info = $li->get('/people/~/following/companies', array('is-company-admin'=>true));
  print_r($info);
}
if(!empty($_REQUEST['act']) && $_REQUEST['act']=='send_comment_form'){
  echo '<form action="" method="post"> ';
  echo '<input type="text" name="comment" placeholder="Comment" value="'.(!empty($_POST) ? htmlspecialchars($_POST['comment']): '').'"><br />';
  echo '<input type="text" name="title" placeholder="Title" value="'.(!empty($_POST) ? htmlspecialchars($_POST['title']): '').'"><br />';
  echo '<input type="text" name="description" placeholder="Description" value="'.(!empty($_POST) ? htmlspecialchars($_POST['description']): '').'"><br />';
  echo '<input type="text" name="submitted-url" placeholder="Submitted URL" value="'.(!empty($_POST) ? htmlspecialchars($_POST['submitted-url']): '').'"><br />';
  echo '<input type="text" name="submitted-image-url" placeholder="Submitted Image URL" value="'.(!empty($_POST) ? htmlspecialchars($_POST['submitted-image-url']): '').'"><br />';
  echo '<input type="text" name="visibility" placeholder="Visibility" value="anyone" value="'.(!empty($_POST) ? htmlspecialchars($_POST['Visibility']): '').'"><br />';
  echo '<input type="submit" value="Post"><br />';
  echo '</form>';
}
if(!empty($_REQUEST['act']) && ($_REQUEST['act']=='send_comment' OR (!empty($_POST) && $_REQUEST['act']=='send_comment_form'))){
  $li->debug = true;
  $data = array(
    'comment'=>isset($_POST) ? $_POST['comment'] : 'Check out the YouRecruit Share API json!',
    'content' => array(
      'title'=> isset($_POST) ? $_POST['title'] : 'LinkedIn Developers Documentation On Using the Share API',
      'description'=> isset($_POST) ? $_POST['description'] : 'Leverage the Share API to maximize engagement on user-generated content on LinkedIn',
      'submitted-url'=> isset($_POST) ? $_POST['submitted-url'] :'https://developer.linkedin.com/documents/share-api',
      'submitted-image-url'=> isset($_POST) ? $_POST['submitted-image-url'] : 'http://m3.licdn.com/media/p/3/000/124/1a6/089a29a.png',
    ),
    'visibility' => array(
      'code' => isset($_POST) ? $_POST['visibility'] : 'anyone',
    ),
  );

$res = $li->post('/people/~/shares', $data);
  print_r($res);
}
if(!empty($_REQUEST['act']) && $_REQUEST['act']=='send_comment_group_form'){
  echo '<form action="" method="post"> ';
  echo '<input type="text" name="title_main" placeholder="Title Main" value="'.(!empty($_POST) ? htmlspecialchars($_POST['title_main']): '').'"><br />';
  echo '<input type="text" name="summary" placeholder="Summary" value="'.(!empty($_POST) ? htmlspecialchars($_POST['summary']): '').'"><br />';
  echo '<input type="text" name="title" placeholder="Title" value="'.(!empty($_POST) ? htmlspecialchars($_POST['title']): '').'"><br />';
  echo '<input type="text" name="description" placeholder="Description" value="'.(!empty($_POST) ? htmlspecialchars($_POST['description']): '').'"><br />';
  echo '<input type="text" name="submitted-url" placeholder="Submitted URL" value="'.(!empty($_POST) ? htmlspecialchars($_POST['submitted-url']): '').'"><br />';
  echo '<input type="text" name="submitted-image-url" placeholder="Submitted Image URL" value="'.(!empty($_POST) ? htmlspecialchars($_POST['submitted-image-url']): '').'"><br />';
  echo '<input type="submit" value="Post"><br />';
  echo '</form>';
}

if(!empty($_REQUEST['act']) && (!empty($_POST) && $_REQUEST['act']=='send_comment_group_form')){
  $li->debug = true;
  $data = array(
    'title'=>$_POST['title_main'],
    'summary'=>$_POST['summary'],
    'content' => array(
      'title'=> isset($_POST) ? $_POST['title'] : 'LinkedIn Developers Documentation On Using the Share API',
      'description'=> isset($_POST) ? $_POST['description'] : 'Leverage the Share API to maximize engagement on user-generated content on LinkedIn',
      'submitted-url'=> isset($_POST) ? $_POST['submitted-url'] :'https://developer.linkedin.com/documents/share-api',
      'submitted-image-url'=> isset($_POST) ? $_POST['submitted-image-url'] : 'http://m3.licdn.com/media/p/3/000/124/1a6/089a29a.png',
    ),
  );

  if(empty($group_id))
    die('Fill in the group id first, please');
  $res = $li->post('/groups/'.$group_id.'/posts', $data);
  print_r($res);
}

if(!empty($_REQUEST['act']) && $_REQUEST['act']=='send_comment_company_form'){
  echo '<form action="" method="post"> ';
  echo '<input type="text" name="comment" placeholder="Comment" value="'.(!empty($_POST) ? htmlspecialchars($_POST['comment']): '').'"><br />';
  echo '<input type="text" name="title" placeholder="Title" value="'.(!empty($_POST) ? htmlspecialchars($_POST['title']): '').'"><br />';
  echo '<input type="text" name="description" placeholder="Description" value="'.(!empty($_POST) ? htmlspecialchars($_POST['description']): '').'"><br />';
  echo '<input type="text" name="submitted-url" placeholder="Submitted URL" value="'.(!empty($_POST) ? htmlspecialchars($_POST['submitted-url']): '').'"><br />';
  echo '<input type="text" name="submitted-image-url" placeholder="Submitted Image URL" value="'.(!empty($_POST) ? htmlspecialchars($_POST['submitted-image-url']): '').'"><br />';
  echo '<input type="text" name="visibility" placeholder="Visibility" value="anyone" value="'.(!empty($_POST) ? htmlspecialchars($_POST['visibility']): '').'"><br />';
  echo '<input type="submit" value="Post"><br />';
  echo '</form>';
}

if(!empty($_REQUEST['act']) && (!empty($_POST) && $_REQUEST['act']=='send_comment_company_form')){
  $li->debug = true;
  $data = array(
    'comment'=>$_POST['comment'],
    'content' => array(
      'title'=> isset($_POST) ? $_POST['title'] : 'LinkedIn Developers Documentation On Using the Share API',
      'description'=> isset($_POST) ? $_POST['description'] : 'Leverage the Share API to maximize engagement on user-generated content on LinkedIn',
      'submitted-url'=> isset($_POST) ? $_POST['submitted-url'] :'https://developer.linkedin.com/documents/share-api',
      'submitted-image-url'=> isset($_POST) ? $_POST['submitted-image-url'] : 'http://m3.licdn.com/media/p/3/000/124/1a6/089a29a.png',
    ),
    'visibility' => array(
      'code' => isset($_POST) ? $_POST['visibility'] : 'anyone',
    ),
  );

  if(empty($company_id))
    die('Fill in the company id first, please');

  $res = $li->post('/companies/'.$company_id.'/shares', $data);
  print_r($res);
}

if(!empty($_REQUEST['act']) && $_REQUEST['act']=='test'){
  $response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<update>
  <update-key>UPDATE-120145407-5860059068051259392</update-key>
  <update-url>http://www.linkedin.com/updates?discuss=&amp;scope=120145407&amp;stype=M&amp;topic=5860059068051259392&amp;type=U&amp;a=x5uO</update-url>
</update>';
  $xml_obj = simplexml_load_string($response);
  $array = json_decode(json_encode((array)$xml_obj), TRUE);
}
?>