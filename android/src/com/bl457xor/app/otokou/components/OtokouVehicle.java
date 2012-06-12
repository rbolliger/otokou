package com.bl457xor.app.otokou.components;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.io.Serializable;
import java.io.StreamCorruptedException;
import java.io.StringReader;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;

import android.database.Cursor;

import com.bl457xor.app.otokou.OtokouAPI;
import com.bl457xor.app.otokou.OtokouException;
import com.bl457xor.app.otokou.db.OtokouVehicleAdapter;
import com.bl457xor.app.otokou.xml.OtokouXmlGetVehiclesHandler;

public class OtokouVehicle  extends OtokouComponent implements Serializable {

	private static final long serialVersionUID = 4361161188955806700L;
	private long otokouVehicleID;
	private long id;
	private String vehicle;
	private boolean found;
	
	
	public OtokouVehicle(int errorCode, String errorMessage) {
		super(errorCode,errorMessage);
	}
	
	public OtokouVehicle(long otokouVehicleID, String vehicle) {
		super();
		this.otokouVehicleID = otokouVehicleID;
		this.vehicle = vehicle;
	}

	public OtokouVehicle(Cursor cursor) {
		super();
		this.id = cursor.getLong(cursor.getColumnIndex(OtokouVehicleAdapter.COL_ID_NAME));
		this.otokouVehicleID = cursor.getLong(cursor.getColumnIndex(OtokouVehicleAdapter.COL_2_NAME));
		this.vehicle = cursor.getString(cursor.getColumnIndex(OtokouVehicleAdapter.COL_3_NAME));
	}

	@Override
	public String toString() {
		return otokouVehicleID+" "+vehicle;
	}
	
	public static OtokouVehicle OtokouVehicleFromByteArray(byte[] byteArray) {
		try {
			ByteArrayInputStream b = new ByteArrayInputStream(byteArray);
			ObjectInputStream o = new ObjectInputStream(b);
			return (OtokouVehicle)o.readObject();
		} catch (StreamCorruptedException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (ClassNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return null;
	}
	
	public byte[] toByteArray() {
		try {	
		     ByteArrayOutputStream b = new ByteArrayOutputStream();
		     ObjectOutputStream o = new ObjectOutputStream(b);
		     o.writeObject(this);
		     return b.toByteArray();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return null;
	}
	
	public static ArrayList<OtokouVehicle> CollectionFromString(String rawData) throws Exception {
		
		ArrayList<OtokouVehicle> vehicles = new ArrayList<OtokouVehicle>();
		
		List<String> vehiclesData = Arrays.asList(rawData.split(","));		
		if (vehiclesData.size() >= 2) {
			if (vehiclesData.get(0).equals("000")) {
				if (vehiclesData.size() >= 5 && vehiclesData.size()%2 == 1) {
					for (int i = 3; i < vehiclesData.size(); i=i+2) {
						vehicles.add(new OtokouVehicle(Long.parseLong(vehiclesData.get(i)),vehiclesData.get(i+1)));					
					}
					return vehicles;
				}
				else {
					throw new Exception("Api Error: number of fields incorrect for vehicles query");
				}
			}
			else {
				throw new Exception("Api Error: "+vehiclesData.get(0));
			}
		}
		else {
			throw new Exception("Api Undefined Error while retrieving Vehicles");
		}
	}

	public static ArrayList<OtokouVehicle> CollectionFromXml(String rawData) throws OtokouException {		
		try {
			SAXParserFactory spf = SAXParserFactory.newInstance();
			SAXParser sp = spf.newSAXParser();

			XMLReader xr = sp.getXMLReader();

			OtokouXmlGetVehiclesHandler xmlHandler = new OtokouXmlGetVehiclesHandler();
			xr.setContentHandler(xmlHandler);

			InputSource is = new InputSource(new StringReader(rawData)); 		    
			xr.parse(is);	    
			xmlHandler.getApiXmlVersion();

			if (!xmlHandler.headerOk()) throw new OtokouException(OtokouException.CODE_RESPONSE_GET_VEHICLES_XML_HEADER_PARSE_FAIL);
			if (!xmlHandler.bodyOk()) {
				if (Long.parseLong(xmlHandler.getXmlErrorCode()) == OtokouAPI.OTOKOU_API_ERROR_CODE_INCORRECT_LOGIN) {
					throw new OtokouException(OtokouException.CODE_RESPONSE_GET_VEHICLES_INCORRECT_LOGIN);
				}
				else {
					throw new OtokouException(OtokouException.CODE_RESPONSE_GET_VEHICLES_XML_BODY_PARSE_FAIL);
				}
			}
			
			return xmlHandler.getXmlVehicles();

		} catch(ParserConfigurationException e) {
			e.printStackTrace();
			throw new OtokouException(OtokouException.CODE_RESPONSE_GET_VEHICLES_PARSE_FAIL);
		} catch(SAXException e) {
			e.printStackTrace();
			throw new OtokouException(OtokouException.CODE_RESPONSE_GET_VEHICLES_PARSE_FAIL);
		} catch(IOException e) {
			e.printStackTrace();
			throw new OtokouException(OtokouException.CODE_RESPONSE_GET_VEHICLES_PARSE_FAIL);
		}
	}
	
	public long getOtokouVehicleId() {
		return otokouVehicleID;
	}
	
	public long getId() {
		return id;
	}
	
	public void setId(long id) {
		this.id = id;
	}
	
	public String getVehicleName() {
		return vehicle;
	}
	
	public boolean isFound() {
		return found;
	}
	
	public void setFound(boolean found) {
		this.found = found;
	}
}
