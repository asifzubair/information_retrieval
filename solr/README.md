# Solr #

## Quick-Start ##

[Quick Start Guide](http://lucene.apache.org/solr/quickstart.html)

```shell
date
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

## solr script includes built-in support for this, which not only starts Solr but also then indexes this data too
solr start -e techproducts

post -c gettingstarted -d "<delete><id>SP2514N</id></delete>"
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

