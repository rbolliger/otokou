package com.bl457xor.app.otokou.xml;

import org.xml.sax.Attributes;
import org.xml.sax.SAXException;
import org.xml.sax.helpers.DefaultHandler;

public class OtokouXmlResponseHandler extends DefaultHandler {
	// booleans that check whether it's in a specific element
	protected boolean inRoot = false;
	protected boolean inOtokou = false;
	protected boolean inHeader = false;
	protected boolean inBody = false;
	protected boolean inErrorCode = false;
	protected boolean inErrorMessage = false;
	protected boolean inResponse = false;

	// hold retrieved data
	protected String xmlVersion = null;
	protected String xmlErrorCode = null;
	protected String xmlErrorMessage = null;
	protected String xmlResponseType = null;

	/**
	 * Returns the Api Version found in the XML.
	 *
	 * @return
	 */
	public String getApiXmlVersion() {
		return xmlVersion;
	}
	
	/**
	 * Returns the Error Code found in the XML.
	 *
	 * @return
	 */
	public String getXmlErrorCode() {
		return xmlErrorCode;
	}
	
	
	/**
	 * Returns the Error Message found in the XML.
	 *
	 * @return
	 */
	public String getXmlErrorMessage() {
		return xmlErrorMessage;
	}
	
	
	/**
	 * Returns the Type of response received found in the XML.
	 *
	 * @return
	 */
	public String getXmlResponseType() {
		return xmlResponseType;
	}
	
	/**
	 * Returns true if header data have been retrieved correctly.
	 *
	 * @return
	 */
	public boolean headerOk() {
		if (xmlVersion != null && xmlErrorCode != null && xmlErrorMessage != null && xmlResponseType != null) {
			return true;
		}
		else {
			return false;
		}
	}

	@Override
	public void startElement(String namespaceURI, String localName, String qName, Attributes atts) throws SAXException {

		if(localName.equals("header")) inHeader = true;
		else if(localName.equals("otokou")) {
			inOtokou = true;
			xmlVersion = atts.getValue("version");
		} 
		else if(localName.equals("root")) inRoot = true;
		else if(localName.equals("error_code")) inErrorCode = true;
		else if(localName.equals("error_message")) inErrorMessage = true;
		else if(localName.equals("response"))inResponse = true;
		else if(localName.equals("body")) inBody = true;
	}

	@Override
	public void endElement(String namespaceURI, String localName, String qName) throws SAXException {
		if(localName.equals("header")) inHeader = false;
		else if(localName.equals("otokou")) inOtokou = false;
		else if(localName.equals("root")) inRoot = false;
		else if(localName.equals("error_code")) inErrorCode = false;
		else if(localName.equals("error_message")) inErrorMessage = false;
		else if(localName.equals("response")) inResponse = false;
		else if(localName.equals("body")) inBody = false;
	}

	@Override
	public void characters(char ch[], int start, int length) {
		String chars = new String(ch, start, length);
		chars = chars.trim();

		if(inHeader && inOtokou && inRoot && !inBody) {
			if (inErrorCode) {
				xmlErrorCode = chars;
			}
			else if (inErrorMessage) {
				xmlErrorMessage = chars;
			}
			else if (inResponse) {
				xmlResponseType = chars;
			}
		} 
	}
}
