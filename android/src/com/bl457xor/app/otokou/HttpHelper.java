package com.bl457xor.app.otokou;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;

public class HttpHelper {
    public String executeHttpGet(String getUrl) throws Exception {
        BufferedReader in = null;
        String page = "";
        try {
            HttpClient client = new DefaultHttpClient();
            HttpResponse response = client.execute(new HttpGet(getUrl));
            in = new BufferedReader
            (new InputStreamReader(response.getEntity().getContent()));
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
}
