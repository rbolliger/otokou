package com.bl457xor.app.otokou;

import java.io.IOException;
import java.io.StringReader;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;

import android.util.Log;

import com.bl457xor.app.otokou.xml.OtokouXmlSetChargeHandler;

public class OtokouCharge {
	public static final String CATEGORY_001_NAME = "Fuel";
	public static final String CATEGORY_002_NAME = "Initial Investment";
	public static final String CATEGORY_003_NAME = "Leasing";
	public static final String CATEGORY_004_NAME = "Tax";
	public static final String CATEGORY_005_NAME = "Accessory";
	public static final String CATEGORY_006_NAME = "Insurance";
	public static final String CATEGORY_007_NAME = "Fine";
	public static final String CATEGORY_008_NAME = "Maintenance";
	public static final String CATEGORY_ERR_NAME = "Unexpected Category";
	
	public static final int CATEGORY_001_ID = 1;
	public static final int CATEGORY_002_ID = 2;
	public static final int CATEGORY_003_ID = 3;
	public static final int CATEGORY_004_ID = 4;
	public static final int CATEGORY_005_ID = 5;
	public static final int CATEGORY_006_ID = 6;
	public static final int CATEGORY_007_ID = 7;
	public static final int CATEGORY_008_ID = 8;
	
	public long vehicleID;
	public String vehicle;
	public long categoryID;
	public String category;	
	public String date;
	public double kilometers;
	public double amount;	
	public String comment;	
	public double quantity;	
	
	public OtokouCharge(long vehicleID, String vehicle, int categoryID, String date, double kilometers, double amount, String comment, double quantity) {
		this.vehicleID = vehicleID;
		this.vehicle = vehicle;
		this.categoryID = categoryID;
		this.category = getCategoryFromID(categoryID);
		this.date = date;
		this.kilometers = kilometers;
		this.amount = amount;	
		this.comment = comment;	
		this.quantity = quantity;	
	}
	
	public OtokouCharge(long vehicleID, String vehicle, int categoryID, String date, double kilometers, double amount, String comment) {
		this(vehicleID,vehicle,categoryID,date,kilometers,amount,comment,0.0);
	}
	
	public static String getCategoryFromID(int ID) {
		switch (ID) {
		case CATEGORY_001_ID:
			return CATEGORY_001_NAME;
		case CATEGORY_002_ID:
			return CATEGORY_002_NAME;
		case CATEGORY_003_ID:
			return CATEGORY_003_NAME;
		case CATEGORY_004_ID:
			return CATEGORY_004_NAME;
		case CATEGORY_005_ID:
			return CATEGORY_005_NAME;
		case CATEGORY_006_ID:
			return CATEGORY_006_NAME;
		case CATEGORY_007_ID:
			return CATEGORY_007_NAME;
		case CATEGORY_008_ID:
			return CATEGORY_008_NAME;
		default:			
			return CATEGORY_ERR_NAME;
		}	
	}
	
	public static boolean checkReponseXml(String rawData) throws Exception {
		  try {
			    SAXParserFactory spf = SAXParserFactory.newInstance();
			    SAXParser sp = spf.newSAXParser();

			    XMLReader xr = sp.getXMLReader();

			    OtokouXmlSetChargeHandler xmlHandler = new OtokouXmlSetChargeHandler();
			    xr.setContentHandler(xmlHandler);

			    InputSource is = new InputSource(new StringReader(rawData)); 		    
			    xr.parse(is);	    
			    xmlHandler.getApiXmlVersion();
			    if (!xmlHandler.headerOk()) throw new Exception("Cound't parse XML header");
			    
			    if (!xmlHandler.bodyOk()) throw new Exception("Cound't parse XML Body");
			    
			    if (xmlHandler.getResult().equalsIgnoreCase("ok")) {
			    	return true;
			    }
			    
			  } catch(ParserConfigurationException pce) {
			    Log.i("SAX XML", "sax parse error", pce);
			  } catch(SAXException se) {
			    Log.i("SAX XML", "sax error", se);
			  } catch(IOException ioe) {
			    Log.i("SAX XML", "sax parse io error", ioe);
			  }
		return false;
	}
}