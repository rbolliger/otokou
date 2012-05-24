package com.bl457xor.app.otokou.xml;

import java.util.ArrayList;

import org.xml.sax.Attributes;
import org.xml.sax.SAXException;

import com.bl457xor.app.otokou.components.OtokouVehicle;

public class OtokouXmlGetVehiclesHandler extends OtokouXmlResponseHandler {
	// booleans that check whether it's in a specific element
	protected boolean inVehiclesNumber = false;
	protected boolean inVehicle = false;
	protected boolean inVehicleId = false;
	protected boolean inVehicleName = false;
	
	// hold temporary data
	protected String vehicleId = null;
	protected String xmlVehicleId = null;
	protected String xmlVehicleName = null;
	protected String xmlVehiclesNumberString = null;
	
	// hold retrieved data
	protected Long xmlVehiclesNumber = null;
	protected ArrayList<OtokouVehicle> xmlVehicles = new ArrayList<OtokouVehicle>();

	/**
	 * Returns the Number of Vehicles found in the XML.
	 *
	 * @return
	 */
	public long getXmlVehiclesNumber() {
		return xmlVehiclesNumber;
	}
	
	/**
	 * Returns the Vehicles found in the XML.
	 *
	 * @return
	 */
	public ArrayList<OtokouVehicle> getXmlVehicles() {
		return xmlVehicles;
	}
	
	/**
	 * Returns true if body data have been retrieved correctly.
	 *
	 * @return
	 */
	public boolean bodyOk() {
		if (xmlVehiclesNumber != null && xmlVehiclesNumber == xmlVehicles.size()) return true;
		return false;
	}
	
	
	@Override
	public void startElement(String namespaceURI, String localName, String qName, Attributes atts) throws SAXException {
		super.startElement(namespaceURI,localName,qName,atts);
		
		if(localName.equals("vehicles_number")) {
			inVehiclesNumber = true;
			xmlVehiclesNumberString = null;
		}
		else if(localName.equals("vehicle")) {
			inVehicle = true;
			vehicleId = atts.getValue("id");
		}
		else if(localName.equals("vehicle_id")) {
			inVehicleId = true;
			xmlVehicleId = null;		
		}
		else if(localName.equals("vehicle_name")) {
			inVehicleName = true;
			xmlVehicleName = null;
		}
	}
	
	@Override
	public void endElement(String namespaceURI, String localName, String qName) throws SAXException {
		super.endElement(namespaceURI,localName,qName);
		
		if(localName.equals("vehicles_number")) {
			inVehiclesNumber = false;
			xmlVehiclesNumberString = trimData(xmlVehiclesNumberString);
			// TODO parse exception?
			xmlVehiclesNumber = Long.parseLong(xmlVehiclesNumberString);
		}
		else if(localName.equals("vehicle")) {
			inVehicle = false;
			vehicleId = trimData(vehicleId);
			if (vehicleId != null && xmlVehicleName != null && xmlVehicleId != null) {
				// TODO parse exception?
				xmlVehicles.add(new OtokouVehicle(Long.parseLong(xmlVehicleId), xmlVehicleName));
			}
			else {
				// TODO wrong data exception?
			}
			vehicleId = null;
			xmlVehicleId = null;
			xmlVehicleName = null;
		}
		else if(localName.equals("vehicle_id")) {
			inVehicleId = false;
			xmlVehicleId = trimData(xmlVehicleId);
		}
		else if(localName.equals("vehicle_name")) {
			inVehicleName = false;
			xmlVehicleName = trimData(xmlVehicleName);
		}
	}
	
	@Override
	public void characters(char ch[], int start, int length) {
		super.characters(ch,start,length);
		
		String chars = new String(ch, start, length);

		if (inBody && inRoot && !inHeader) {
			if (inVehicle) {
				if (inVehicleId) xmlVehicleId = appendData(xmlVehicleId,chars);
				else if (inVehicleName) xmlVehicleName = appendData(xmlVehicleName,chars);
			}
			else if (inVehiclesNumber) xmlVehiclesNumberString = appendData(xmlVehiclesNumberString,chars);
		}
	}
}
