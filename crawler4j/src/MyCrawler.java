package crawler4j;

import java.io.FileWriter;
import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;
import java.util.Set;
import java.util.regex.Pattern;

//import org.apache.http.HttpStatus;

import com.opencsv.CSVWriter;

import edu.uci.ics.crawler4j.crawler.Page;
import edu.uci.ics.crawler4j.crawler.WebCrawler;
import edu.uci.ics.crawler4j.parser.BinaryParseData;
import edu.uci.ics.crawler4j.parser.HtmlParseData;
import edu.uci.ics.crawler4j.parser.ParseData;
import edu.uci.ics.crawler4j.url.WebURL;

public class MyCrawler extends WebCrawler {
    /*private final static Pattern FILTERS = Pattern.compile(".*(\\.(css|js|gif|jpg"
                                                           + "|png|mp3|mp3|zip|gz))$");
    */
	private final static Pattern SKIP = Pattern.compile(".*(\\.(css|js|mp3|mp3|zip|gz|xml))$");
	private static final Pattern DOC_EXTENSIONS = Pattern.compile(".*\\.(doc|pdf)$");
	private static final Pattern IMAGE_EXTENSIONS = Pattern.compile(".*\\.(bmp|gif|jpg|png|jpeg)$");
	private final static String seed = "http://www.nbcnews.com/";
    CrawlStat myCrawlStat;
    
    public MyCrawler() {
        myCrawlStat = new CrawlStat();
    }
    
    /**
     * This method receives two parameters. The first parameter is the page
     * in which we have discovered this new url and the second parameter is
     * the new url. You should implement this function to specify whether
     * the given url should be crawled or not (based on your crawling logic).
     * In this example, we are instructing the crawler to ignore urls that
     * have css, js, git, ... extensions and to only accept urls that start
     * with "http://www.nbcnews.com/". In this case, we didn't need the
     * referringPage parameter to make the decision.
     */
    @Override
    public boolean shouldVisit(Page referringPage, WebURL url) {
    	String href = url.getURL().toLowerCase();
    	href = href.replace(",", "_");
    	myCrawlStat.incProcessedURLs(href);
    	
    	if (DOC_EXTENSIONS.matcher(href).matches() && href.startsWith(seed))
    		return true;
    	
    	if (IMAGE_EXTENSIONS.matcher(href).matches() && href.startsWith(seed))
    		return true;
    	
    	if (!SKIP.matcher(href).matches() && href.startsWith(seed))
    		return true;
    	
    	return false;
    }
    
    
    @Override
    protected void handlePageStatusCode(WebURL webUrl, int statusCode, String statusDescription) {
       	String href = webUrl.getURL().toLowerCase();
       	href = href.replace(",", "_");
    	myCrawlStat.incFetchedURLs(href + "," + statusCode);
    }
    

    /**
     * This function is called when a page is fetched and ready
     * to be processed by your program.
     */

    @Override
    public void visit(Page page) {
    	String url = page.getWebURL().getURL();
    	String content_type = page.getContentType();
    	double size = 0;
    	int outgoing_urls = 0;
    	myCrawlStat.incProcessedPages();
    	
    	ParseData pd = page.getParseData();
    	if (pd instanceof HtmlParseData) {
    		HtmlParseData htmlParseData = (HtmlParseData) pd;
    		Set<WebURL> links = htmlParseData.getOutgoingUrls();
    		outgoing_urls = links.size();
    		myCrawlStat.incTotalLinks(outgoing_urls);
    		try {
    			size = htmlParseData.getText().getBytes("UTF-8").length/1024.;
                myCrawlStat.incTotalTextSize((int) size);
            } catch (UnsupportedEncodingException ignored) {
                // Do nothing
            }
    	} else if (pd instanceof BinaryParseData) 
    		size = page.getContentData().length/1024.;
    	url = url.replace(",", "_");
    	String desc = url + "," + size + "," + outgoing_urls + "," + content_type;
    	myCrawlStat.incVisitedURLs(url, desc);
    }
    
    /**
     * This function is called by controller to get the local data of this crawler when job is
     * finished
     */
    @Override
    public Object getMyLocalData() {
        return myCrawlStat;
    }

    /**
     * This function is called by controller before finishing the job.
     * You can put whatever stuff you need here.
     */
    @Override
    public void onBeforeExit() {
        try {
			dumpMyData();
		} catch (IOException e) {
			e.printStackTrace();
		}
        
    }
    
    public void dumpMyData() throws IOException {
        int id = getMyId();
        
        CSVWriter writer = new CSVWriter(new FileWriter("Crawler_urls_" + id + ".csv"), ',');
        for (String url : myCrawlStat.getProcessedURLs()) {
        	String status = url.startsWith(seed) ? "OK" : "N_OK";
        	String[] entries = new String[]{url, status};
        	writer.writeNext(entries);
        }
        writer.close();
        
        writer = new CSVWriter(new FileWriter("Crawler_visited_" + id + ".csv"), ',');
        HashMap<String,String> visitedUrls = myCrawlStat.getVisitedURLs();
        Set set = visitedUrls.entrySet();
        Iterator iterator = set.iterator();
        while(iterator.hasNext()){
        	Map.Entry<String, String> mentry = (Map.Entry) iterator.next();
        	String[] entries = mentry.getValue().split(",");
        	writer.writeNext(entries);
        }
        writer.close();
        
        writer = new CSVWriter(new FileWriter("Crawler_fetched_" + id + ".csv"), ',');
        for (String url : myCrawlStat.getFetchedURLs()) {
        	String[] entries = url.split(",");
        	writer.writeNext(entries);
        }
        writer.close();
        
        // You can configure the log to output to file
        logger.info("Crawler {} > Processed Pages: {}", id, myCrawlStat.getTotalProcessedPages());
        logger.info("Crawler {} > Total Links Found: {}", id, myCrawlStat.getTotalLinks());
        logger.info("Crawler {} > Total Text Size: {}", id, myCrawlStat.getTotalTextSize());
    }
    
}