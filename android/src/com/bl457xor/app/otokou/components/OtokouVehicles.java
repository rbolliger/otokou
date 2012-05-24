package com.bl457xor.app.otokou.components;

import java.util.ArrayList;

public class OtokouVehicles extends OtokouComponent {
	public ArrayList<OtokouVehicle> items;
	
	public OtokouVehicles(int errorCode, String errorMessage) {
		super(errorCode,errorMessage);
	}
	
	public OtokouVehicles(ArrayList<OtokouVehicle> vehicles) {
		items = vehicles;
	}
}
