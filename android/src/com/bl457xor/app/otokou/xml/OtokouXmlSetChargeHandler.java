package com.bl457xor.app.otokou.xml;

import org.xml.sax.Attributes;
import org.xml.sax.SAXException;

public class OtokouXmlSetChargeHandler extends OtokouXmlResponseHandler {
	// booleans that check whether it's in a specific element
	protected boolean inResult = false;
	
	// hold retrieved data
	protected String xmlResult = null;

	/**
	 * Returns the Result found in the XML.
	 *
	 * @return
	 */
	public String getResult() {
		return xmlResult;
	}
	
	/**
	 * Returns true if body data have been retrieved correctly.
	 *
	 * @return
	 */
	public boolean bodyOk() {
		if (xmlResult != null) {
			return true;
		}
		else {
			return false;
		}
	}
	
	@Override
	public void startElement(String namespaceURI, String localName, String qName, Attributes atts) throws SAXException {
		super.startElement(namespaceURI,localName,qName,atts);
		
		if(localName.equals("result")) inResult = true;
	}
	
	@Override
	public void endElement(String namespaceURI, String localName, String qName) throws SAXException {
		super.endElement(namespaceURI,localName,qName);
		
		if(localName.equals("result")) inResult = false;
	}
	
	@Override
	public void characters(char ch[], int start, int length) {
		super.characters(ch,start,length);
		
		String chars = new String(ch, start, length);
		chars = chars.trim();

		if(inBody && inOtokou && inRoot && !inHeader) {
			if (inResult) {
				xmlResult = chars;
			}
		} 
	}
}
