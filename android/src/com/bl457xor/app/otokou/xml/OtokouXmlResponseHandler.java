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
	protected String xmlResponse = null;

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
		return xmlResponse;
	}
	
	/**
	 * Returns true if header data have been retrieved correctly.
	 *
	 * @return
	 */
	public boolean headerOk() {
		if (xmlVersion != null && xmlErrorCode != null && xmlErrorMessage != null && xmlResponse != null) return true;
		else return false;
	}

	/**
	 * Append additional data to xml element, taking care of null dataStore.
	 * 
	 * This function is necessary because DefaultHandler.characters() method add data by chunks
	 * 
	 * @param dataStore
	 * @param dataToAppend
	 * @return appended dataStore
	 */
	protected String appendData(String dataStore, String dataToAppend) {
	    if (dataStore != null) return dataStore + dataToAppend;
	    else return dataToAppend;
	}
	
	/**
	 * trim dataStore, taking care of null dataStore.
	 * 
	 * @param dataStore
	 * @param dataToAppend
	 * @return trimmed dataStore
	 */	
	protected String trimData(String dataStore) {
	    if (dataStore != null) return dataStore.trim();
	    else return null;
	}
	
	@Override
	public void startElement(String namespaceURI, String localName, String qName, Attributes atts) throws SAXException {

		if(localName.equals("header")) inHeader = true;
		else if(localName.equals("otokou")) {
			inOtokou = true;
			xmlVersion = atts.getValue("version");
		} 
		else if(localName.equals("root")) inRoot = true;
		else if(localName.equals("error_code")) {
			inErrorCode = true;
			xmlErrorCode = null;
		}
		else if(localName.equals("error_message")) {
			inErrorMessage = true;
			xmlErrorMessage = null;
		}
		else if(localName.equals("response")) {
			inResponse = true;
			xmlResponse = null;
		}
		else if(localName.equals("body")) inBody = true;
	}

	@Override
	public void endElement(String namespaceURI, String localName, String qName) throws SAXException {
		if(localName.equals("header")) inHeader = false;
		else if(localName.equals("otokou")) {
			inOtokou = false;
			xmlVersion = trimData(xmlVersion);
		}
		else if(localName.equals("root")) inRoot = false;
		else if(localName.equals("error_code")) {
			inErrorCode = false;
			xmlErrorCode = trimData(xmlErrorCode);
		}
		else if(localName.equals("error_message")) {
			inErrorMessage = false;
			xmlErrorMessage = trimData(xmlErrorMessage);
			
		}
		else if(localName.equals("response")) {
			inResponse = false;
			xmlResponse = trimData(xmlResponse);
			
		}
		else if(localName.equals("body")) inBody = false;
	}

	@Override
	public void characters(char ch[], int start, int length) {
		String chars = new String(ch, start, length);

		if(inHeader && inOtokou && inRoot && !inBody) {
			if (inErrorCode) xmlErrorCode = appendData(xmlErrorCode,chars);
			else if (inErrorMessage) xmlErrorMessage = appendData(xmlErrorMessage,chars);
			else if (inResponse) xmlResponse = appendData(xmlResponse,chars);
		} 
	}
	

}
