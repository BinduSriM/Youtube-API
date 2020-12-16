<?php
if(isset($_GET['Search'])){
if ($_GET['q']) {
  // Call set_include_path() as needed to point to your client library.
  require_once ($_SERVER["DOCUMENT_ROOT"].'/phpfiles/google-api-php-client/src/Google_Client.php');
  require_once ($_SERVER["DOCUMENT_ROOT"].'/phpfiles/google-api-php-client/src/contrib/Google_YouTubeService.php');

  /* Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
  Google APIs Console <http://code.google.com/apis/console#access>
  Please ensure that you have enabled the YouTube Data API for your project. */
  $DEVELOPER_KEY = 'AIzaSyAi_5mcHNQd0ZsT9wNnyB7oR7DWlZzWoX8';

  $client = new Google_Client();
  $client->setDeveloperKey($DEVELOPER_KEY);

  $youtube = new Google_YoutubeService($client);
  $query=$_GET['q'];
  $query=str_replace(' ','-',$query);
  try {
    $searchResponse = $youtube->search->listSearch('id,snippet', array(
      'q' => $query,
      'maxResults' => 5+1,
    ));

    $videos = '';
    $channels = '';
    echo "<h3>Top 5 Videos</h3>";
    foreach ($searchResponse['items'] as $searchResult) {
      switch ($searchResult['id']['kind']) {
        case 'youtube#video':
          $videos .= sprintf('<li>%s (%s)</li>', $searchResult['snippet']['title'],
            $searchResult['id']['videoId']."<a href=http://www.youtube.com/watch?v=".$searchResult['id']['videoId']." target=_blank>   Watch This Video</a>");
          echo "<div class='row'><div class='col col-md-4'><iframe height='200'  
            src='https://www.youtube.com/embed/".$searchResult['id']['videoId']."' frameborder='5' allowfullscreen > </iframe></div></div>";
          break;
        case 'youtube#channel':
          $link='https://www.youtube.com/channel/'.$searchResult['id']['channelId'];
          $channels .= sprintf('<li>%s (%s)</li>', $searchResult['snippet']['title'],$link);
          break;
       }
    }

   } catch (Google_ServiceException $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  }
}
echo "<h3>Channels</h3>
<ul>". $channels ."</ul>";
}

?>

<!doctype html>
<html>
  <head>
    <title>YouTube Search</title>
<link href="//www.w3resource.com/includes/bootstrap.css" rel="stylesheet">
<style type="text/css">
body{margin-top: 50px; margin-left: 50px}
</style>
  </head>
  <body>
    <form method="GET">
  <div>
    Search Term: <input type="search" id="q" name="q" placeholder="Enter Search Term">
  </div>
  <input type="submit" value="Search" name="Search">
</form>
</body>
</html>
