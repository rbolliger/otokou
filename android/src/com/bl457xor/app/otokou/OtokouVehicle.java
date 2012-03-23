package com.bl457xor.app.otokou;

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

import android.util.Log;

import com.bl457xor.app.otokou.xml.OtokouXmlGetVehiclesHandler;

public class OtokouVehicle implements Serializable {

	private static final long serialVersionUID = 4361161188955806700L;
	public long vehicleID;
	public String vehicle;
	
	public OtokouVehicle(long vehicleID, String vehicle) {
		this.vehicleID = vehicleID;
		this.vehicle = vehicle;
	}
	
	@Override
	public String toString() {
		return vehicleID+" "+vehicle;
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

	public static ArrayList<OtokouVehicle> CollectionFromXml(String rawData) throws Exception {
		// TODO erase debug
		Log.i("XML:", rawData);
		
		try {
			    SAXParserFactory spf = SAXParserFactory.newInstance();
			    SAXParser sp = spf.newSAXParser();

			    XMLReader xr = sp.getXMLReader();

			    OtokouXmlGetVehiclesHandler xmlHandler = new OtokouXmlGetVehiclesHandler();
			    xr.setContentHandler(xmlHandler);

			    InputSource is = new InputSource(new StringReader(rawData)); 		    
			    xr.parse(is);	    
			    xmlHandler.getApiXmlVersion();
			    if (!xmlHandler.headerOk()) throw new Exception("Cound't parse XML header");
			    
			    if (!xmlHandler.bodyOk()) throw new Exception("Cound't parse XML Body");
			    
			    return xmlHandler.getXmlVehicles();
			    
			  } catch(ParserConfigurationException pce) {
			    Log.i("SAX XML", "sax parse error", pce);
			  } catch(SAXException se) {
			    Log.i("SAX XML", "sax error", se);
			  } catch(IOException ioe) {
			    Log.i("SAX XML", "sax parse io error", ioe);
			  }

		return null;
	}
}
