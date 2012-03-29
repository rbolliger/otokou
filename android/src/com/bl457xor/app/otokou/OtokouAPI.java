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

	public static OtokouUser getUserData(String apiKey) {
		String getRequest = OTOKOU_API_URL+OTOKOU_GET_USER_ACTION;
    	try {		
    		return new OtokouUser(HttpHelper.executeHttpPost(getRequest,writeGetUserXml(apiKey)),apiKey);
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return null;
		}		
	}
	
	public static ArrayList<OtokouVehicle> getVehiclesData(String apiKey) {	
		String getRequest = OTOKOU_API_URL+OTOKOU_GET_VEHICLES_ACTION;
    	try {		
    		return OtokouVehicle.CollectionFromXml(HttpHelper.executeHttpPost(getRequest,writeGetVehiclesXml(apiKey)));
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return null;
		}
	}

	public static boolean setNewChargeData(OtokouCharge charge, String apiKey) {
		String getRequest = OTOKOU_API_URL+OTOKOU_SET_CHARGE_ACTION;
    	try {
    		return OtokouCharge.checkReponseXml(HttpHelper.executeHttpPost(getRequest,writeSetNewChargeXml(charge, apiKey)));
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		return false;
	}
	
	private static String writeGetUserXml(String apiKey){
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
	
	private static String writeGetVehiclesXml(String apiKey) {
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
	
	private static String writeSetNewChargeXml(OtokouCharge charge, String apiKey) {
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
	        serializer.startTag(null, "apikey");
	        serializer.text(apiKey);
	        serializer.endTag(null, "apikey");        
	        serializer.startTag(null, "vehicle_id");
	        serializer.text(""+charge.vehicleID);
	        serializer.endTag(null, "vehicle_id");
	        serializer.startTag(null, "category_id");
	        serializer.text(""+charge.categoryID);
	        serializer.endTag(null, "category_id");
	        serializer.startTag(null, "date");
	        serializer.text(charge.date);
	        serializer.endTag(null, "date");
	        serializer.startTag(null, "kilometers");
	        serializer.text(""+charge.kilometers);
	        serializer.endTag(null, "kilometers");
	        serializer.startTag(null, "amount");
	        serializer.text(""+charge.amount);
	        serializer.endTag(null, "amount");
	        serializer.startTag(null, "comment");
	        serializer.text(charge.comment);
	        serializer.endTag(null, "comment");
	        serializer.startTag(null, "quantity");
	        serializer.text(""+charge.quantity);
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
