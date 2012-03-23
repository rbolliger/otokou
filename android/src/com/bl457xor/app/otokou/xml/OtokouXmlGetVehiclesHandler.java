package com.bl457xor.app.otokou.xml;

import java.util.ArrayList;

import org.xml.sax.Attributes;
import org.xml.sax.SAXException;

import com.bl457xor.app.otokou.OtokouVehicle;

public class OtokouXmlGetVehiclesHandler extends OtokouXmlResponseHandler {
	// booleans that check whether it's in a specific element
	protected boolean inVehiclesNumber = false;
	protected boolean inVehicle = false;
	protected boolean inVehicleId = false;
	protected boolean inVehicleName = false;
	
	// hold temporary data
	protected Long vehicleId = null;
	protected Long vehicleElementId = null;
	protected String vehicleName = null;
	
	// hold retrieved data
	protected Long xmlVehicleNumber = null;
	protected ArrayList<OtokouVehicle> xmlVehicles = new ArrayList<OtokouVehicle>();

	/**
	 * Returns the Number of Vehicles found in the XML.
	 *
	 * @return
	 */
	public long getXmlVehicleNumber() {
		return xmlVehicleNumber;
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
		if (xmlVehicleNumber != null) {
			if (xmlVehicleNumber == xmlVehicles.size()) {
				return true;
			}
		}
		return false;
	}
	
	
	@Override
	public void startElement(String namespaceURI, String localName, String qName, Attributes atts) throws SAXException {
		super.startElement(namespaceURI,localName,qName,atts);
		
		if(localName.equals("vehicles_number")) inVehiclesNumber = true;
		else if(localName.equals("vehicle")) {
			inVehicle = true;
			// TODO exception?
			vehicleElementId = Long.parseLong(atts.getValue("id"));
		}
		else if(localName.equals("vehicle_id")) inVehicleId = true;
		else if(localName.equals("vehicle_name")) inVehicleName = true;
	}
	
	@Override
	public void endElement(String namespaceURI, String localName, String qName) throws SAXException {
		super.endElement(namespaceURI,localName,qName);
		
		if(localName.equals("vehicles_number")) inVehiclesNumber = false;
		else if(localName.equals("vehicle")) {
			inVehicle = false;
			if (vehicleId != null && vehicleName != null && vehicleElementId != null) {
				xmlVehicles.add(new OtokouVehicle(vehicleId, vehicleName));
			}
			else {
				// TODO exception?
			}
			vehicleId = null;
			vehicleElementId = null;
			vehicleName = null;
		}
		else if(localName.equals("vehicle_id")) inVehicleId = false;
		else if(localName.equals("vehicle_name")) inVehicleName = false;
	}
	
	@Override
	public void characters(char ch[], int start, int length) {
		super.characters(ch,start,length);
		
		String chars = new String(ch, start, length);
		chars = chars.trim();

		if (inBody && inVehicle && inRoot && !inHeader) {
			if (inVehicleId) {
				// TODO exception?
				vehicleId = Long.parseLong(chars);
			}
			else if (inVehicleName) {
				vehicleName = chars;
			}
		}
		else if (inBody && inVehiclesNumber && inRoot && !inHeader) {
			// TODO exception?
			xmlVehicleNumber = Long.parseLong(chars);
		}
	}
}
