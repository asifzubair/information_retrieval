<?php

// make sure browsers see this page as utf-8 encoded HTML
header('Content-Type: text/html; charset=utf-8');

$limit = 10;
$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$results = false;
$engine="";

if(isset($_GET['sort']) && $_GET['sort'] === "lucene") {
    $additionalParameters = array();
    $engine = "Lucene";
} else {
    $additionalParameters = array(
      'sort' => 'pageRankFile desc',
      //'facet' => 'true',
      // notice I use an array for a multi-valued parameter 
      //'facet.field' => array(
      //  'field_1',
      //  'field_2'
      //) 
    );
    $engine = "PageRank";
} 


if ($query)
{
  // The Apache Solr Client library should be on the include path
  // which is usually most easily accomplished by placing in the
  // same directory as this script ( . or current directory is a default
  // php include path entry in the php.ini)
  require_once('../Apache/Solr/Service.php');

  // create a new solr service instance - host, port, and webapp
  // path (all defaults in this example)
  $solr = new Apache_Solr_Service('localhost', 8983, '/solr/NBCNews/');

  // if magic quotes is enabled then stripslashes will be needed
  if (get_magic_quotes_gpc() == 1)
  {
    $query = stripslashes($query);
  }

  // in production code you'll always want to use a try /catch for any
  // possible exceptions emitted  by searching (i.e. connection
  // problems or a query parsing error)
  try
  {
    
    $results = $solr->search($query, 0, $limit, $additionalParameters);
  }
  catch (Exception $e)
  {
    // in production you'd probably log or email this error to an admin
    // and then show a special message to the user but for this example
    // we're going to show the full exception
    die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
  }
}

?>
<?php
  $myFile = "../data/NBCNewsData/mapNBCNewsDataFile.csv";
  $fh = fopen($myFile, 'r');
  $theData = fread($fh, filesize($myFile));
  $assoc_array = array();
  $my_array = explode("\n", $theData);
  foreach($my_array as $line)
  {
      $tmp = explode(",", $line);
      $assoc_array[$tmp[0]] = isset($tmp[1]) ? str_replace(array("\n","\r"), '', $tmp[1]) : null;
  }
  fclose($fh);
  $codes = $assoc_array;
?>
<html>
  <head>
    <title>PHP Solr Client </title>
  </head>
  <body>
    <form  accept-charset="utf-8" method="get">
      <label for="q">Search:</label>
      <input id="q" name="q" type="text" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/>
      <input type="radio" name="sort" value="lucene" checked> Lucene
      <input type="radio" name="sort" value="pagerank"> PageRank
<!--      <label>Lucene</label>
      <input type='radio' name='agree' value='lucene' />
      <label>PageRank</label>
      <input type='radio' name='agree' value='pagerank' /> -->
      <input type="submit"/>
    </form>
<?php

// display results
if ($results)
{
  $total = (int) $results->response->numFound;
  $start = min(1, $total);
  $end = min($limit, $total);
?>
    <div>Results using <?php echo $engine,": "; echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:</div>
    <ol>
<?php
  // iterate result documents
  foreach ($results->response->docs as $doc)
  {
?>
      <li>
        <table style="border: 1px solid black; text-align: left">
<?php
    // iterate document fields / values
    foreach ($doc as $field => $value)
    {
      $key = htmlspecialchars($field, ENT_NOQUOTES, 'utf-8');
      if (gettype($value) != "array"){
        if ('id' === $key) {
          $id = htmlspecialchars($value, ENT_NOQUOTES, 'utf-8');
        } elseif ('description' === $key) {
          $desc = htmlspecialchars($value, ENT_NOQUOTES, 'utf-8');
        } elseif ('title' === $key) {
          $title = htmlspecialchars($value, ENT_NOQUOTES, 'utf-8');
        };
      }
    }
?>
          <tr>
            <th> <?php echo "Title"; ?> </th>
            <td> <?php echo '<a href="'.$codes[basename($id)].'" target="_blank">'.$title.'</a>'; ?></td>
          </tr>
          <tr>
            <th> <?php echo "URL"; ?> </th>
            <td> <?php echo '<a href="'.$codes[basename($id)].'" target="_blank">'.$codes[basename($id)].'</a>'; ?></td>
          </tr>
          <tr>
            <th> <?php echo "ID"; ?> </th>
            <td> <?php echo $id; ?> </td>
          </tr>
          <tr>          
            <th> <?php echo "Description"; ?> </th>
            <td> <?php echo $desc; ?> </td>
          </tr>
<?php
//    }
?>
        </table>
      </li>
<?php
  }
?>
    </ol>
<?php
}
?>
  </body>
</html>
