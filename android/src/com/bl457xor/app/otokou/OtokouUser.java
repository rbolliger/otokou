package com.bl457xor.app.otokou;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.io.Serializable;
import java.io.StreamCorruptedException;
import java.io.StringReader;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;

import android.util.Log;

import com.bl457xor.app.otokou.xml.OtokouXmlGetUserHandler;

public class OtokouUser implements Serializable{

	private static final long serialVersionUID = 2592158732300070601L;
	private long userID;
	//public String title;
	private String firstName;
	//public String middleName;
	private String lastName;
	//public String userName;
	//public String password
	//public String email;
	private String apiKey;
	
	public OtokouUser(long userID, String firstName, String lastName, String apiKey) throws Exception {
		this.userID = userID;
		this.firstName = firstName;
		this.lastName = lastName;
		this.apiKey = apiKey;		
	}
	
	public OtokouUser(String rawData, String apikey) throws Exception {
		  try {
		    SAXParserFactory spf = SAXParserFactory.newInstance();
		    SAXParser sp = spf.newSAXParser();

		    XMLReader xr = sp.getXMLReader();

		    OtokouXmlGetUserHandler xmlHandler = new OtokouXmlGetUserHandler();
		    xr.setContentHandler(xmlHandler);

		    InputSource is = new InputSource(new StringReader(rawData)); 		    
		    xr.parse(is);	    
		    xmlHandler.getApiXmlVersion();
		    if (!xmlHandler.headerOk()) throw new Exception("Cound't parse XML header");
		    
		    if (!xmlHandler.bodyOk()) throw new Exception("Cound't parse XML Body");
		    
			this.userID = Long.parseLong(xmlHandler.getXmlUserId());
			this.firstName = xmlHandler.getXmlFirstName();
			this.lastName = xmlHandler.getXmlLastName();
			this.apiKey = apikey;
		    
		  } catch(ParserConfigurationException pce) {
		    Log.i("SAX XML", "sax parse error", pce);
		  } catch(SAXException se) {
		    Log.i("SAX XML", "sax error", se);
		  } catch(IOException ioe) {
		    Log.i("SAX XML", "sax parse io error", ioe);
		  }
	}
	
	@Override
	public String toString() {
		return firstName+" "+lastName;	
	}
	
	public static OtokouUser OtokouUserFromByteArray(byte[] byteArray) {
		try {
			ByteArrayInputStream b = new ByteArrayInputStream(byteArray);
			ObjectInputStream o = new ObjectInputStream(b);
			return (OtokouUser)o.readObject();
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
	
	public long getUserId() {
		return userID;
	}
	
	public String getFirstName() {
		return firstName;
	}
	
	public String getLastName() {
		return lastName;
	}
	
	public String getApikey() {
		return apiKey;
	}
}
