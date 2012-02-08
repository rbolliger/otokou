package com.bl457xor.app.otokou;

import java.io.Serializable;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

public class OtokouVehicle implements Serializable {

	private static final long serialVersionUID = 4361161188955806700L;
	public long vehicleID;
	public String vehicle;
	
	public OtokouVehicle(long vehicleID, String vehicle) {
		this.vehicleID = vehicleID;
		this.vehicle = vehicle;
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
	
	@Override
	public String toString() {
		return vehicleID+" "+vehicle;
	}
}
