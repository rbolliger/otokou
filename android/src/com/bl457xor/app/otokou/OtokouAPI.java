package com.bl457xor.app.otokou;

import java.io.StringWriter;
import java.util.ArrayList;

import org.xmlpull.v1.XmlSerializer;

import android.util.Xml;

public class OtokouAPI {
	// general constants
	public static final String OTOKOU_API_URL = "https://otokou.donax.ch/api/";
	public static final String OTOKOU_SET_CHARGE_ACTION = "set_charge";
	public static final String OTOKOU_GET_VEHICLES_ACTION = "get_vehicles";
	public static final String OTOKOU_GET_USER_ACTION = "get_user";

	public static OtokouUser getUserData(String username, String apiKey) {
		String getRequest = OTOKOU_API_URL+OTOKOU_GET_USER_ACTION;
    	try {		
    		return new OtokouUser(HttpHelper.executeHttpPost(getRequest,writeGetUserXml(username, apiKey)), username, apiKey);
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return null;
		}		
	}
	
	public static ArrayList<OtokouVehicle> getVehiclesData(String username, String apiKey) {	
		String getRequest = OTOKOU_API_URL+OTOKOU_GET_VEHICLES_ACTION;
    	try {		
    		return OtokouVehicle.CollectionFromXml(HttpHelper.executeHttpPost(getRequest,writeGetVehiclesXml(username, apiKey)));
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return null;
		}
	}

	public static boolean setNewChargeData(String username, String apiKey, OtokouCharge charge) {
		String getRequest = OTOKOU_API_URL+OTOKOU_SET_CHARGE_ACTION;
    	try {
    		return OtokouCharge.checkReponseXml(HttpHelper.executeHttpPost(getRequest,writeSetNewChargeXml(username, apiKey, charge)));
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		return false;
	}
	
	private static String writeGetUserXml(String username, String apiKey){
	    XmlSerializer serializer = Xml.newSerializer();
	    StringWriter writer = new StringWriter();
	    try {
	        serializer.setOutput(writer);
	        serializer.startDocument("UTF-8", true);
	        serializer.startTag(null, "root");
	        serializer.startTag(null, "otokou");
	        serializer.attribute(null, "version", "1.0");
	        serializer.startTag(null, "header");
	        serializer.startTag(null, "request");
	        serializer.text(OTOKOU_GET_USER_ACTION);
	        serializer.endTag(null, "request");
	        serializer.endTag(null, "header");
	        serializer.startTag(null, "body");
	        serializer.startTag(null, "username");
	        serializer.text(username);
	        serializer.endTag(null, "username");
	        serializer.startTag(null, "apikey");
	        serializer.text(apiKey);
	        serializer.endTag(null, "apikey");
	        serializer.endTag(null, "body");
	        serializer.endTag(null, "otokou");
	        serializer.endTag(null, "root");
	        serializer.endDocument();
	        return writer.toString();
	    } catch (Exception e) {
	        throw new RuntimeException(e);
	    } 
	}
	
	private static String writeGetVehiclesXml(String username, String apiKey) {
	    XmlSerializer serializer = Xml.newSerializer();
	    StringWriter writer = new StringWriter();
	    try {
	        serializer.setOutput(writer);
	        serializer.startDocument("UTF-8", true);
	        serializer.startTag(null, "root");
	        serializer.startTag(null, "otokou");
	        serializer.attribute(null, "version", "1.0");
	        serializer.startTag(null, "header");
	        serializer.startTag(null, "request");
	        serializer.text(OTOKOU_GET_VEHICLES_ACTION);
	        serializer.endTag(null, "request");
	        serializer.endTag(null, "header");
	        serializer.startTag(null, "body");
	        serializer.startTag(null, "username");
	        serializer.text(username);
	        serializer.endTag(null, "username");
	        serializer.startTag(null, "apikey");
	        serializer.text(apiKey);
	        serializer.endTag(null, "apikey");
	        serializer.endTag(null, "body");
	        serializer.endTag(null, "otokou");
	        serializer.endTag(null, "root");
	        serializer.endDocument();
	        return writer.toString();
	    } catch (Exception e) {
	        throw new RuntimeException(e);
	    } 
	}
	
	private static String writeSetNewChargeXml(String username, String apiKey, OtokouCharge charge) {
	    XmlSerializer serializer = Xml.newSerializer();
	    StringWriter writer = new StringWriter();
	    try {
	        serializer.setOutput(writer);
	        serializer.startDocument("UTF-8", true);
	        serializer.startTag(null, "root");
	        serializer.startTag(null, "otokou");
	        serializer.attribute(null, "version", "1.0");
	        serializer.startTag(null, "header");
	        serializer.startTag(null, "request");
	        serializer.text(OTOKOU_SET_CHARGE_ACTION);
	        serializer.endTag(null, "request");
	        serializer.endTag(null, "header");
	        serializer.startTag(null, "body");
	        serializer.startTag(null, "username");
	        serializer.text(username);
	        serializer.endTag(null, "username");
	        serializer.startTag(null, "apikey");
	        serializer.text(apiKey);
	        serializer.endTag(null, "apikey");
	        serializer.startTag(null, "vehicle_id");
	        serializer.text(""+charge.getVehicleId());
	        serializer.endTag(null, "vehicle_id");
	        serializer.startTag(null, "category_id");
	        serializer.text(""+charge.getCategoryId());
	        serializer.endTag(null, "category_id");
	        serializer.startTag(null, "date");
	        serializer.text(charge.getDate());
	        serializer.endTag(null, "date");
	        serializer.startTag(null, "kilometers");
	        serializer.text(""+charge.getKilometers());
	        serializer.endTag(null, "kilometers");
	        serializer.startTag(null, "amount");
	        serializer.text(""+charge.getAmount());
	        serializer.endTag(null, "amount");
	        serializer.startTag(null, "comment");
	        serializer.text(charge.getComment());
	        serializer.endTag(null, "comment");
	        serializer.startTag(null, "quantity");
	        serializer.text(""+charge.getQuantity());
	        serializer.endTag(null, "quantity");           
	        serializer.endTag(null, "body");
	        serializer.endTag(null, "otokou");
	        serializer.endTag(null, "root");
	        serializer.endDocument();
	        return writer.toString();
	    } catch (Exception e) {
	        throw new RuntimeException(e);
	    } 
	}
}
