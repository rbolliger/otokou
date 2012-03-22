package com.bl457xor.app.otokou.xml;

import org.xml.sax.Attributes;
import org.xml.sax.SAXException;

public class OtokouXmlGetUserHandler extends OtokouXmlResponseHandler {
	// booleans that check whether it's in a specific element
	protected boolean inUserId = false;
	protected boolean inFirstName = false;
	protected boolean inLastName = false;
	
	// hold retrieved data
	protected String xmlUserId = null;
	protected String xmlFirstName = null;
	protected String xmlLastName = null;

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
	 * Returns true if body data have been retrieved correctly.
	 *
	 * @return
	 */
	public boolean bodyOk() {
		if (xmlUserId != null && xmlFirstName != null && xmlLastName != null) {
			return true;
		}
		else {
			return false;
		}
	}
	
	@Override
	public void startElement(String namespaceURI, String localName, String qName, Attributes atts) throws SAXException {
		super.startElement(namespaceURI,localName,qName,atts);
		
		if(localName.equals("user_id")) inUserId = true;
		else if(localName.equals("first_name")) inFirstName = true;
		else if(localName.equals("last_name")) inLastName = true;
	}
	
	@Override
	public void endElement(String namespaceURI, String localName, String qName) throws SAXException {
		super.endElement(namespaceURI,localName,qName);
		
		if(localName.equals("user_id")) inUserId = false;
		else if(localName.equals("first_name")) inFirstName = false;
		else if(localName.equals("last_name")) inLastName = false;
	}
	
	@Override
	public void characters(char ch[], int start, int length) {
		super.characters(ch,start,length);
		
		String chars = new String(ch, start, length);
		chars = chars.trim();

		if(inBody && inOtokou && inRoot && !inHeader) {
			if (inUserId) {
				xmlUserId = chars;
			}
			else if (inFirstName) {
				xmlFirstName = chars;
			}
			else if (inLastName) {
				xmlLastName = chars;
			}
		} 
	}
}
