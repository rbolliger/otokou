package com.bl457xor.app.otokou;

import java.io.StringWriter;

import org.xmlpull.v1.XmlSerializer;

import android.util.Xml;

import com.bl457xor.app.otokou.components.OtokouCharge;
import com.bl457xor.app.otokou.components.OtokouComponent;
import com.bl457xor.app.otokou.components.OtokouUser;
import com.bl457xor.app.otokou.components.OtokouVehicle;
import com.bl457xor.app.otokou.components.OtokouVehicles;

public class OtokouAPI {
	// general constants
	public static final String OTOKOU_API_URL = "https://otokou.donax.ch/api/";
	public static final String OTOKOU_SET_CHARGE_ACTION = "set_charge";
	public static final String OTOKOU_GET_VEHICLES_ACTION = "get_vehicles";
	public static final String OTOKOU_GET_USER_ACTION = "get_user";
	// Otokou API error codes
	public static final long OTOKOU_API_ERROR_CODE_INCORRECT_LOGIN = 211;

	public static OtokouUser getUserData(String username, String apiKey) {
		String getRequest = OTOKOU_API_URL+OTOKOU_GET_USER_ACTION;
    	try {		
    		return new OtokouUser(HttpHelper.executeHttpPost(getRequest,writeGetUserXml(username, apiKey)), username, apiKey);
		} catch (OtokouException e) {
			return new OtokouUser(e.getExceptionCode(),e.getMessage());
		}		
	}
	
	public static OtokouVehicles getVehiclesData(String username, String apiKey) {	
		String getRequest = OTOKOU_API_URL+OTOKOU_GET_VEHICLES_ACTION;
    	try {		
    		return new OtokouVehicles(OtokouVehicle.CollectionFromXml(HttpHelper.executeHttpPost(getRequest,writeGetVehiclesXml(username, apiKey))));
		} catch (OtokouException e) {
			return new OtokouVehicles(e.getExceptionCode(),e.getMessage());
		}
	}

	public static OtokouComponent setNewChargeData(String username, String apiKey, OtokouCharge charge) {
		String getRequest = OTOKOU_API_URL+OTOKOU_SET_CHARGE_ACTION;
    	try {
    		OtokouCharge.checkReponseXml(HttpHelper.executeHttpPost(getRequest,writeSetNewChargeXml(username, apiKey, charge)));
    		return new OtokouComponent();
		} catch (OtokouException e) {
			return new OtokouComponent(e.getExceptionCode(),e.getMessage());
		}
	}
	
	private static String writeGetUserXml(String username, String apiKey) throws OtokouException {
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
	    	e.printStackTrace();
	    	throw new OtokouException(OtokouException.CODE_WRITE_GET_USER_XML_FAIL);
	    } 
	}
	
	private static String writeGetVehiclesXml(String username, String apiKey) throws OtokouException {
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
	    	e.printStackTrace();
	    	throw new OtokouException(OtokouException.CODE_WRITE_GET_VEHICLES_XML_FAIL);
	    } 
	}
	
	private static String writeSetNewChargeXml(String username, String apiKey, OtokouCharge charge) throws OtokouException {
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
	    	e.printStackTrace();
	    	throw new OtokouException(OtokouException.CODE_WRITE_SET_CHARGE_XML_FAIL);
	    } 
	}
}
