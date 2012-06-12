package com.bl457xor.app.otokou.components;

import java.util.ArrayList;

import android.database.Cursor;

public class OtokouVehicles extends OtokouComponent {
	public ArrayList<OtokouVehicle> items;
	
	public OtokouVehicles(int errorCode, String errorMessage) {
		super(errorCode,errorMessage);
	}
	
	public OtokouVehicles(ArrayList<OtokouVehicle> vehicles) {
		items = vehicles;
	}
	
	public OtokouVehicles(Cursor vehicles) {
		items = new ArrayList<OtokouVehicle>();
		
		if (vehicles.getCount() > 0) {
			vehicles.moveToFirst();
			do {
				items.add(new OtokouVehicle(vehicles));
			} while (vehicles.moveToNext());
		}
		
	}
}
