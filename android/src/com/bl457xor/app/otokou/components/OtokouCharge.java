package com.bl457xor.app.otokou.components;

import java.io.IOException;
import java.io.StringReader;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;

import android.database.Cursor;

import com.bl457xor.app.otokou.OtokouAPI;
import com.bl457xor.app.otokou.OtokouException;
import com.bl457xor.app.otokou.db.OtokouChargeAdapter;
import com.bl457xor.app.otokou.xml.OtokouXmlSetChargeHandler;

public class OtokouCharge extends OtokouComponent {
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
	
	private long id;
	private long otokouVehicleID;
	private long vehicleID;
	private String vehicle;
	private long categoryID;
	private String category;	
	private String date;
	private double kilometers;
	private double amount;	
	private String comment;	
	private double quantity;	
	
	
	public OtokouCharge(int errorCode, String errorMessage) {
		super(errorCode,errorMessage);
	}
	
	public OtokouCharge(long otokouVehicleID, String vehicle, int categoryID, String date, double kilometers, double amount, String comment, double quantity) {
		super();
		this.otokouVehicleID = otokouVehicleID;
		this.vehicle = vehicle;
		this.categoryID = categoryID;
		this.category = getCategoryFromID(categoryID);
		this.date = date;
		this.kilometers = kilometers;
		this.amount = amount;	
		this.comment = comment;	
		this.quantity = quantity;	
	}
	
	public OtokouCharge(long otokouVehicleID, String vehicle, int categoryID, String date, double kilometers, double amount, String comment) {
		this(otokouVehicleID,vehicle,categoryID,date,kilometers,amount,comment,0.0);
	}
	
	public OtokouCharge(Cursor c) {
		super();
		this.id = c.getLong(c.getColumnIndex(OtokouChargeAdapter.COL_ID_NAME));
		this.vehicleID = c.getLong(c.getColumnIndex(OtokouChargeAdapter.COL_2_NAME));
		this.vehicle = "";
		this.categoryID = c.getLong(c.getColumnIndex(OtokouChargeAdapter.COL_3_NAME));
		this.category = getCategoryFromID((int)this.categoryID);
		this.date = c.getString(c.getColumnIndex(OtokouChargeAdapter.COL_5_NAME));
		this.kilometers = c.getFloat(c.getColumnIndex(OtokouChargeAdapter.COL_6_NAME));
		this.amount = c.getFloat(c.getColumnIndex(OtokouChargeAdapter.COL_7_NAME));
		this.comment = c.getString(c.getColumnIndex(OtokouChargeAdapter.COL_8_NAME));
		this.quantity = c.getFloat(c.getColumnIndex(OtokouChargeAdapter.COL_9_NAME));
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
	
	public static void checkReponseXml(String rawData) throws OtokouException {
		  try {
			    SAXParserFactory spf = SAXParserFactory.newInstance();
			    SAXParser sp = spf.newSAXParser();

			    XMLReader xr = sp.getXMLReader();

			    OtokouXmlSetChargeHandler xmlHandler = new OtokouXmlSetChargeHandler();
			    xr.setContentHandler(xmlHandler);

			    InputSource is = new InputSource(new StringReader(rawData)); 		    
			    xr.parse(is);	    
			    xmlHandler.getApiXmlVersion();
			    
				if (!xmlHandler.headerOk()) throw new OtokouException(OtokouException.CODE_RESPONSE_SET_CHARGE_XML_HEADER_PARSE_FAIL);
				if (!xmlHandler.bodyOk()) {
					if (Long.parseLong(xmlHandler.getXmlErrorCode()) == OtokouAPI.OTOKOU_API_ERROR_CODE_INCORRECT_LOGIN) {
						throw new OtokouException(OtokouException.CODE_RESPONSE_SET_CHARGE_INCORRECT_LOGIN);
					}
					else {
						throw new OtokouException(OtokouException.CODE_RESPONSE_SET_CHARGE_XML_BODY_PARSE_FAIL);
					}
				}
				
			    if (!xmlHandler.getResult().equalsIgnoreCase("ok")) {
			    	throw new OtokouException(OtokouException.CODE_RESPONSE_SET_CHARGE_NOT_OK);
			    }
			    
		  } catch(ParserConfigurationException e) {
			  e.printStackTrace();
			  throw new OtokouException(OtokouException.CODE_RESPONSE_SET_CHARGE_PARSE_FAIL);
		  } catch(SAXException e) {
			  e.printStackTrace();
			  throw new OtokouException(OtokouException.CODE_RESPONSE_SET_CHARGE_PARSE_FAIL);
		  } catch(IOException e) {
			  e.printStackTrace();
			  throw new OtokouException(OtokouException.CODE_RESPONSE_SET_CHARGE_PARSE_FAIL);
		  }
	}

	public long getId() {
		return id;
	}
	
	public void setId(long id) {
		this.id = id;
	}	
	
	public long getUserId() {
		return vehicleID;
	}	

	public long getOtokouVehicleId() {
		return otokouVehicleID;
	}
	
	public void setOtokouVehicleId(long id) {
		this.otokouVehicleID = id;
	}
	
	public long getVehicleId() {
		return vehicleID;
	}
	
	public String getVehicleName() {
		return vehicle;
	}
	
	public long getCategoryId() {
		return categoryID;
	}
	
	public String getCategory() {
		return category;
	}	

	public String getDate() {
		return date;
	}	
	
	public double getKilometers() {
		return kilometers;
	}	
	
	public double getAmount() {
		return amount;
	}
	
	public String getComment() {
		return comment;
	}	
	
	public double getQuantity() {
		return quantity;
	}
}