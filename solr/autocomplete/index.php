<?php

ini_set('memory_limit', '4096M'); 

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
  $solr = new Apache_Solr_Service('localhost', 8983, '/solr/NBCNews2/');

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
    <link
  href="http://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css"
  rel="stylesheet"></link>
    <title> Solr Spellcheck Autocomplete </title>
    <script src="http://code.jquery.com/jquery-1.10.2.js"></script>
    <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script>
      $(function() {
        var URL_PREFIX = "http://localhost:8983/solr/NBCNews2/suggest?q=";
        var URL_SUFFIX = "&wt=json";
        $("#q").autocomplete({
          source : function(request, response) {
            var URL = URL_PREFIX + $("#q").val() + URL_SUFFIX;
            $.ajax({
              url : URL,
              success : function(data) {
                array = data.suggest.suggest[$("#q").val()].suggestions
                var docs = JSON.stringify(array);
                var jsonData = JSON.parse(docs);
                response($.map(jsonData, function(value, key) {
                  return {
                    label : value.term
                  }
                }));
              },
              dataType : 'jsonp',
              jsonp : 'json.wrf'
            });
          },
          minLength : 1
        })
      });
    </script>
  </head>
  <body>
    <form  accept-charset="utf-8" method="get">
      <label for="q">Search:</label>
      <input id="q" name="q" type="text" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'utf-8'); ?>"/>
      <input type="radio" name="sort" value="lucene" checked> Lucene
      <input type="radio" name="sort" value="pagerank"> PageRank
      <input type="submit"/>
    </form>
<?php
// Spell Check 
include 'SpellCorrector.php';
$correct=SpellCorrector::correct($query);
if (!strcasecmp($correct, $query) == 0){
//  echo "Do you mean ".$correct." ?";
  echo '<p><a href="?q='.$correct.'&sort=lucene">Do you mean <i>'.$correct.'</i>?</a></p>';
  echo "<p>Showing results for: ".$query."</p>";
}

?>
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
function plaintext($html)
{
    // remove comments and any content found in the the comment area (strip_tags only removes the actual tags).
    $plaintext = preg_replace('#<!--.*?-->#s', '', $html);

    // put a space between list items (strip_tags just removes the tags).
    $plaintext = preg_replace('#</li>#', ' </li>', $plaintext);

    // remove all script and style tags
    $plaintext = preg_replace('#<(script|style)\b[^>]*>(.*?)</(script|style)>#is', "", $plaintext);

    // remove br tags (missed by strip_tags)
    $plaintext = preg_replace("#<br[^>]*?>#", " ", $plaintext);

    // remove all remaining html
    $plaintext = strip_tags($plaintext);

    return $plaintext;
}
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

    $contents = plaintext(file_get_contents($id));
    $contents = filter_var($contents, FILTER_SANITIZE_STRING);
    $pos = stripos($contents,$query);
    if ($pos){
      $string = substr($contents, $pos-30, $pos+30);
      $snip = trim(preg_replace('/\s+/', ' ', $string));
     } else {
       $snip = "Query not found in body";
     }
    $output = "... ".$snip." ...";

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
          <tr>          
            <th> <?php echo "Snippet"; ?> </th>
            <td> <?php echo $output; ?> </td>
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