package com.bl457xor.app.otokou;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.List;

import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.ClientProtocolException;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.conn.ssl.SSLSocketFactory;
import org.apache.http.conn.ssl.X509HostnameVerifier;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;


public class HttpHelper {
    public String executeHttpGet(String getUrl) throws Exception {
        BufferedReader in = null;
        String page = "";
        
        try {
            HttpClient client = new DefaultHttpClient();
            HttpResponse response = client.execute(new HttpGet(getUrl));
            in = new BufferedReader(new InputStreamReader(response.getEntity().getContent()));
            StringBuffer sb = new StringBuffer("");
            String line = "";
            String NL = System.getProperty("line.separator");
            while ((line = in.readLine()) != null) {
                sb.append(line + NL);
            }
            in.close();
            page = sb.toString();
        } 
        finally {        	
            if (in != null) {
                try {
                    in.close();
                    } catch (IOException e) {
                    e.printStackTrace();
                }
            }
        }
        return page;
    }
    
    public static String executeHttpPost(String postUrl,String Xml) {    	
    	
    	BufferedReader in = null;
    	String page = "";
    	
    	try {
    		// Create a new HttpClient and Post Header
    		HttpClient httpclient = new DefaultHttpClient();
    		
    		// accept ssl certificate from kreativemedia instead of donax
    		SSLSocketFactory sslSocketFactory = (SSLSocketFactory) httpclient.getConnectionManager().getSchemeRegistry().getScheme("https").getSocketFactory();
		    final X509HostnameVerifier delegate = sslSocketFactory.getHostnameVerifier();
		    if(!(delegate instanceof MyVerifier)) {
		        sslSocketFactory.setHostnameVerifier(new MyVerifier(delegate));
		    }
    		
    		HttpPost httppost = new HttpPost(postUrl);
    		// Add your data
    		List<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>(1);
    		nameValuePairs.add(new BasicNameValuePair("request", Xml));
    		httppost.setEntity(new UrlEncodedFormEntity(nameValuePairs));

    		// Execute HTTP Post Request
    		HttpResponse response = httpclient.execute(httppost);

    		in = new BufferedReader(new InputStreamReader(response.getEntity().getContent()));
    		StringBuffer sb = new StringBuffer("");
    		String line = "";
    		String NL = System.getProperty("line.separator");
    		while ((line = in.readLine()) != null) {
    			sb.append(line + NL);
    		}
    		in.close();
    		page = sb.toString();
    		
    	} catch (UnsupportedEncodingException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (ClientProtocolException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
    	finally {        	
    		if (in != null) {
    			try {
    				in.close();
    			} catch (IOException e) {
    				e.printStackTrace();
    			}
    		}
    	}

    	return page;
    } 
}
