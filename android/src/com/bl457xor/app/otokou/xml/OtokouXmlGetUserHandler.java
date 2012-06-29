package com.bl457xor.app.otokou.xml;

import org.xml.sax.Attributes;
import org.xml.sax.SAXException;

public class OtokouXmlGetUserHandler extends OtokouXmlResponseHandler {
	// booleans that check whether it's in a specific element
	protected boolean inUserId = false;
	protected boolean inFirstName = false;
	protected boolean inLastName = false;
	protected boolean inLastUserUpdate = false;
	protected boolean inLastVehiclesUpdate = false;
	protected boolean inVehiclesNumber = false;
	
	// hold temporary data
	protected String xmlVehiclesNumberString = null;
	
	// hold retrieved data
	protected String xmlUserId = null;
	protected String xmlFirstName = null;
	protected String xmlLastName = null;
	protected String xmlLastUserUpdate = null;
	protected String xmlLastVehiclesUpdate = null;
	protected Long xmlVehiclesNumber = null;

	/**
	 * Returns the User ID found in the XML.
	 *
	 * @return
	 */
	public String getXmlUserId() {
		return xmlUserId;
	}
	
	/**
	 * Returns the First Name found in the XML.
	 *
	 * @return
	 */
	public String getXmlFirstName() {
		return xmlFirstName;
	}
	
	/**
	 * Returns the Last Name found in the XML.
	 *
	 * @return
	 */
	public String getXmlLastName() {
		return xmlLastName;
	}
	
	/**
	 * Returns the Last Name found in the XML.
	 *
	 * @return
	 */
	public String getXmlLastUserUpdate() {
		return xmlLastUserUpdate;
	}
	
	/**
	 * Returns the Last Vehicles Update found in the XML.
	 *
	 * @return
	 */
	public String getXmlLastVehiclesUpdate() {
		return xmlLastVehiclesUpdate;
	}
	
	/**
	 * Returns the Vehicles number found in the XML.
	 *
	 * @return
	 */
	public long getXmlVehiclesNumber() {
		return xmlVehiclesNumber;
	}
	
	/**
	 * Returns true if body data have been retrieved correctly.
	 *
	 * @return
	 */
	public boolean bodyOk() {
		if (xmlUserId != null && xmlFirstName != null && xmlLastName != null && xmlLastUserUpdate != null && xmlLastVehiclesUpdate != null && xmlVehiclesNumber != null) return true;
		else return false;
	}
	
	@Override
	public void startElement(String namespaceURI, String localName, String qName, Attributes atts) throws SAXException {
		super.startElement(namespaceURI,localName,qName,atts);
		
		if(localName.equals("user_id")) {
			inUserId = true;
			xmlUserId = null;
		}
		else if(localName.equals("first_name")) {
			inFirstName = true;
			xmlFirstName = null;
		}
		else if(localName.equals("last_name")) {
			inLastName = true;
			xmlLastName = null;
		}
		else if(localName.equals("last_user_update")) {
			inLastUserUpdate = true;
			xmlLastUserUpdate = null;
		}
		else if(localName.equals("last_vehicles_update")) {
			inLastVehiclesUpdate = true;
			xmlLastVehiclesUpdate = null;
		}
		else if(localName.equals("vehicles_number")) {
			inVehiclesNumber = true;
			xmlVehiclesNumber = null;
		}
	}
	
	@Override
	public void endElement(String namespaceURI, String localName, String qName) throws SAXException {
		super.endElement(namespaceURI,localName,qName);
		
		if(localName.equals("user_id")) {
			inUserId = false;
			xmlUserId = trimData(xmlUserId);
		}
		else if(localName.equals("first_name")) {
			inFirstName = false;
			xmlFirstName = trimData(xmlFirstName);
		}
		else if(localName.equals("last_name")) {
			inLastName = false;
			xmlLastName = trimData(xmlLastName);
		}
		else if(localName.equals("last_user_update")) {
			inLastUserUpdate = false;
			xmlLastUserUpdate = trimData(xmlLastUserUpdate);
		}
		else if(localName.equals("last_vehicles_update")) {
			inLastVehiclesUpdate = false;
			xmlLastVehiclesUpdate = trimData(xmlLastVehiclesUpdate);
		}
		else if(localName.equals("vehicles_number")) {			
			inVehiclesNumber = false;
			xmlVehiclesNumberString = trimData(xmlVehiclesNumberString);
			try {
				xmlVehiclesNumber = Long.parseLong(xmlVehiclesNumberString);
			}
			catch (NumberFormatException e) {
				xmlVehiclesNumber = null;
			}
		}
	}
	
	@Override
	public void characters(char ch[], int start, int length) {
		super.characters(ch,start,length);
		
		String chars = new String(ch, start, length);

		if(inBody && inOtokou && inRoot && !inHeader) {
			if (inUserId) xmlUserId = appendData(xmlUserId,chars);
			else if (inFirstName) xmlFirstName = appendData(xmlFirstName,chars);
			else if (inLastName) xmlLastName = appendData(xmlLastName,chars);
			else if (inLastUserUpdate) xmlLastUserUpdate = appendData(xmlLastUserUpdate,chars);
			else if (inLastVehiclesUpdate) xmlLastVehiclesUpdate = appendData(xmlLastVehiclesUpdate,chars);
			else if (inVehiclesNumber) xmlVehiclesNumberString = appendData(xmlVehiclesNumberString,chars);
		} 
	}
}
