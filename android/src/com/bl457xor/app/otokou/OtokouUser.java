package com.bl457xor.app.otokou;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.io.Serializable;
import java.io.StreamCorruptedException;
import java.io.StringReader;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;

import android.database.Cursor;
import android.util.Log;

import com.bl457xor.app.otokou.db.OtokouUserAdapter;
import com.bl457xor.app.otokou.xml.OtokouXmlGetUserHandler;

public class OtokouUser implements Serializable{

	private static final long serialVersionUID = 2592158732300070601L;
	private long otokouUserID;
	private long id;
	//public String title;
	private String firstName;
	//public String middleName;
	private String lastName;
	public String username;
	private String apiKey;
	//public String password
	//public String email;
	private long vehiclesNumber;
	private String lastUpdate;
	private String lastVehiclesUpdate;
	private boolean autoload;
	
	
	public OtokouUser(Cursor c) throws Exception {	
		this.id = c.getLong(c.getColumnIndex(OtokouUserAdapter.COL_ID_NAME));
		this.otokouUserID = c.getLong(c.getColumnIndex(OtokouUserAdapter.COL_1_NAME));
		this.firstName = c.getString(c.getColumnIndex(OtokouUserAdapter.COL_2_NAME));
		this.lastName = c.getString(c.getColumnIndex(OtokouUserAdapter.COL_3_NAME));
		this.username = c.getString(c.getColumnIndex(OtokouUserAdapter.COL_4_NAME));
		this.apiKey = c.getString(c.getColumnIndex(OtokouUserAdapter.COL_5_NAME));
		this.vehiclesNumber = c.getLong(c.getColumnIndex(OtokouUserAdapter.COL_6_NAME));
		this.lastUpdate = c.getString(c.getColumnIndex(OtokouUserAdapter.COL_7_NAME));
		this.lastVehiclesUpdate = c.getString(c.getColumnIndex(OtokouUserAdapter.COL_8_NAME));	
		this.autoload = (c.getLong(c.getColumnIndex(OtokouUserAdapter.COL_9_NAME)) == OtokouUserAdapter.COL_9_AUTOLOAD_ON) ? true : false;
	}
	
	public OtokouUser(String rawData, String username, String apikey) throws Exception {
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
		    
			this.otokouUserID = Long.parseLong(xmlHandler.getXmlUserId());
			this.firstName = xmlHandler.getXmlFirstName();
			this.lastName = xmlHandler.getXmlLastName();
			this.username = username;
			this.apiKey = apikey;
			this.lastUpdate = xmlHandler.getXmlLastUserUpdate();
			this.lastVehiclesUpdate = xmlHandler.getXmlLastVehiclesUpdate();
			this.vehiclesNumber = xmlHandler.getXmlVehiclesNumber();
		    
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
	
	public long getOtokouUserId() {
		return otokouUserID;
	}
	
	public void setId(long id) {
		this.id = id;
	}
	
	public long getId() {
		return id;
	}
	
	public void setAutoload(boolean autoload) {
		this.autoload = autoload;
	}
	
	public boolean getAutoload() {
		return autoload;
	}
	
	public String getFirstName() {
		return firstName;
	}
	
	public String getLastName() {
		return lastName;
	}
	
	public String getUsername() {
		return username;
	}
	
	public void setApikey(String apikey) {
		this.apiKey = apikey;
	}
	
	public String getApikey() {
		return apiKey;
	}
	
	public long getVehiclesNumber() {
		return vehiclesNumber;
	}
	
	public String getLastUpdate() {
		return lastUpdate;
	}
	
	public String getLastVehiclesUpdate() {
		return lastVehiclesUpdate;
	}
	
	public boolean vehiclesAreOutOfDate(OtokouUser user) {
		String myFormatString = "yyyy-MM-dd HH:mm:ss";
		SimpleDateFormat df = new SimpleDateFormat(myFormatString);
		try {
			Date date1 = df.parse(this.getLastVehiclesUpdate());
			Date date2 = df.parse(user.getLastVehiclesUpdate());
			if (date2.after(date1) || this.getVehiclesNumber() !=  user.getVehiclesNumber()) {
				return true;
			}
			else return false;
			
		} catch (ParseException e1) {
			// TODO Auto-generated catch block
			e1.printStackTrace();
		}
		return true;
	}

	public void updateData(OtokouUser retrivedOtokouUser) {
		this.otokouUserID = retrivedOtokouUser.getOtokouUserId();
		this.firstName = retrivedOtokouUser.getFirstName();
		this.lastName = retrivedOtokouUser.getLastName();
		this.lastUpdate = retrivedOtokouUser.getLastUpdate();
		this.lastVehiclesUpdate = retrivedOtokouUser.getLastVehiclesUpdate();
		this.vehiclesNumber = retrivedOtokouUser.getVehiclesNumber();
	}
}
