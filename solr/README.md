# Solr #

## Quick-Start ##

[Quick Start Guide](http://lucene.apache.org/solr/quickstart.html)

```shell
date
## note that this starts solr in cloud mode
solr start -e cloud -noprompt 
  open http://localhost:8983/solr
  post -c gettingstarted docs/
  open http://localhost:8983/solr/gettingstarted/browse
  post -c gettingstarted example/exampledocs/*.xml
  post -c gettingstarted example/exampledocs/books.json
  post -c gettingstarted example/exampledocs/books.csv
  post -c gettingstarted -d "<delete><id>SP2514N</id></delete>"
  solr healthcheck -c gettingstarted
date
```
#### Objectives ####
- Launched Solr into SolrCloud mode, two nodes, two collections including shards and replicas
- Indexed a directory of rich text files
- Indexed Solr XML files
- Indexed Solr JSON files
- Indexed CSV content
- Opened the admin console, used its query interface to get JSON formatted results
- Opened the /browse interface to explore Solr's features in a more friendly and familiar interface

## Commands ##

```shell
solr start -e cloud -noprompt
post -c gettingstarted docs/
post -c gettingstarted example/exampledocs/*.xml
post -c gettingstarted example/exampledocs/books.json
post -c gettingstarted example/exampledocs/books.csv
solr stop -all

## solr script includes built-in support for this, 
## which not only starts Solr but also then indexes this data too
solr start -e techproducts

post -c gettingstarted -d "<delete><id>SP2514N</id></delete>"
```

```shell
solr start

solr create -c NBCNews
<=====>
Copying configuration to new core instance directory:
/usr/local/Cellar/solr/6.3.0/server/solr/NBCNews

Creating new core 'NBCNews' using command:
http://localhost:8983/solr/admin/cores?action=CREATE&name=NBCNews&instanceDir=NBCNews

{
  "responseHeader":{
    "status":0,
    "QTime":748},
  "core":"NBCNews"}
<=====>

post -c NBCNews -filetypes html data/NBCNewsData/NBCNewsDownloadData
<=====>
java -classpath /usr/local/Cellar/solr/6.3.0/libexec/dist/solr-core-6.3.0.jar -Dauto=yes -Dfiletypes=html -Dc=NBCNews -Ddata=files -Drecursive=yes org.apache.solr.util.SimplePostTool data/NBCNewsData/NBCNewsDownloadData
SimplePostTool version 5.0.0
Posting files to [base] url http://localhost:8983/solr/NBCNews/update...
Entering auto mode. File endings considered are html
Entering recursive mode, max depth=999, delay=0s
Indexing directory data/NBCNewsData/NBCNewsDownloadData (19362 files, depth=0)
POSTing file 0001cdf5-6966-4649-92f2-2092d26d958f.html (text/html) to [base]/extract
POSTing file 000384c3-ac3d-46bb-ada8-0e4dda5fb78a.html (text/html) to [base]/extract
  ...
POSTing file 002f117d-4bc4-4f50-a604-c9ad946e11a4.html (text/html) to [base]/extract
19362 files indexed.
COMMITting Solr index changes to http://localhost:8983/solr/NBCNews/update...
Time spent: 0:09:25.668
<=====>
```

## Useful Links ##

- [Main starting point for administering Solr](http://localhost:8983/solr/#/)
- [Query tab](http://localhost:8983/solr/#/gettingstarted/query)
- [Browse indexed documents](http://localhost:8983/solr/gettingstarted/browse)
- [Searching](http://lucene.apache.org/solr/quickstart.html#searching)
- [Clean Up](http://lucene.apache.org/solr/quickstart.html#cleanup)
- [Solr Query Syntax](https://cwiki.apache.org/confluence/display/solr/The+Standard+Query+Parser#TheStandardQueryParser-SpecifyingTermsfortheStandardQueryParser)
- [Solr XML](https://cwiki.apache.org/confluence/display/solr/Uploading+Data+with+Index+Handlers#UploadingDatawithIndexHandlers-XMLFormattedIndexUpdates)
- [Solr Style JSON](https://cwiki.apache.org/confluence/display/solr/Uploading+Data+with+Index+Handlers#UploadingDatawithIndexHandlers-Solr-StyleJSON)
- [CSV Formatted Index Updates](https://cwiki.apache.org/confluence/display/solr/Uploading+Data+with+Index+Handlers#UploadingDatawithIndexHandlers-CSVFormattedIndexUpdates)
- [git submodule tutorial](https://git-scm.com/book/en/v2/Git-Tools-Submodules)

