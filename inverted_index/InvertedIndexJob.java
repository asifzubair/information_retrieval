import java.io.IOException;
import java.util.StringTokenizer;
import java.util.ArrayList; 
import java.util.Collections; 
import java.util.HashMap; 
import java.util.Map;
import java.util.Iterator;

import org.apache.hadoop.conf.Configuration;
import org.apache.hadoop.fs.Path;
import org.apache.hadoop.io.IntWritable;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.mapreduce.Job;
import org.apache.hadoop.mapreduce.Mapper;
import org.apache.hadoop.mapreduce.Reducer;
import org.apache.hadoop.mapreduce.lib.input.FileInputFormat;
import org.apache.hadoop.mapreduce.lib.output.FileOutputFormat;

public class InvertedIndexJob {

  public static class TokenizerMapper
       extends Mapper<Object, Text, Text, Text>{

    private Text word = new Text();
    public void map(Object key, Text value, Context context
                    ) throws IOException, InterruptedException {

        String parts[] = value.toString().split('\t'); 
        String docId = parts[0];
        String textStr = parts[1];
        StringTokenizer itr = new StringTokenizer(textStr);
        while (itr.hasMoreTokens()) {
            word.set(itr.nextToken());
            context.write(word, new Text(docId));
        }   
    }   
  }

  public static class TextReducer
       extends Reducer<Text,Text,Text,Text> {

    public void reduce(Text key, Iterable<Text> values,
                       Context context
                       ) throws IOException, InterruptedException {
    
      HashMap<String, Integer> docId = new HashMap<String, Integer>();
      for(Text val : values){
        String valString = val.toString();
        Integer count = docId.get(valString);
        docId.put(valString, (count == null) ? 1 : count + 1);
      }

      Iterator<Map.Entry<String,Integer>> entries = docId.entrySet().iterator();
      StringBuilder sb = new StringBuilder();
      sb.append("\t");
      while (entries.hasNext()) {
        Map.Entry<String,Integer> entry = entries.next();
        sb.append(entry.getKey());
        sb.append(':');
        sb.append(entry.getValue());
        sb.append("\t");
      }

      context.write(key, new Text(sb.toString()));
    }
  }

  public static void main(String[] args) throws Exception {
    Configuration conf = new Configuration();
    Job job = Job.getInstance(conf, "word count");
    job.setJarByClass(InvertedIndexJob.class);
    job.setMapperClass(TokenizerMapper.class);
    job.setReducerClass(TextReducer.class);
    job.setOutputKeyClass(Text.class);
    job.setOutputValueClass(Text.class);
    FileInputFormat.addInputPath(job, new Path(args[0]));
    FileOutputFormat.setOutputPath(job, new Path(args[1]));
    System.exit(job.waitForCompletion(true) ? 0 : 1);
  }
}
