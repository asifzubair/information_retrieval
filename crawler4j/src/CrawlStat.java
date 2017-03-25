package crawler4j;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

public class CrawlStat {
    private int totalProcessedPages;
    private long totalLinks;
    private long totalTextSize;
    private List<String> processedURLs = new ArrayList<String>();
    private List<String> fetchedURLs = new ArrayList<String>();
    private HashMap<String, String> visitedURLs = new HashMap<String, String>();

    public int getTotalProcessedPages() {
        return totalProcessedPages;
    }

    public void setTotalProcessedPages(int totalProcessedPages) {
        this.totalProcessedPages = totalProcessedPages;
    }
    
    public List<String> getProcessedURLs(){
    	return processedURLs;
    };

    public List<String> getFetchedURLs(){
    	return fetchedURLs;
    };
    
    public HashMap<String, String> getVisitedURLs(){
    	return visitedURLs;
    };

    public void incProcessedPages() {
        this.totalProcessedPages++;
    }
    
    public void incProcessedURLs(String url){
    	this.processedURLs.add(url);
    }
    
    public void incFetchedURLs(String url_status){
    	this.fetchedURLs.add(url_status);
    }
    
    public void incVisitedURLs(String url, String desc){
    	this.visitedURLs.put(url, desc);
    }

    public long getTotalLinks() {
        return totalLinks;
    }

    public void setTotalLinks(long totalLinks) {
        this.totalLinks = totalLinks;
    }

    public long getTotalTextSize() {
        return totalTextSize;
    }

    public void setTotalTextSize(long totalTextSize) {
        this.totalTextSize = totalTextSize;
    }

    public void incTotalLinks(int count) {
        this.totalLinks += count;
    }

    public void incTotalTextSize(int count) {
        this.totalTextSize += count;
    }
}
