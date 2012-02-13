package com.bl457xor.app.otokou;

import java.util.ArrayList;

public class OtokouAPI {
	// general constants
	public static final String OTOKOU_API_URL = "http://otokou.donax.ch/api/";
	public static final String OTOKOU_SET_CHARGE_ACTION = "set_charge";
	public static final String OTOKOU_GET_VEHICLES_ACTION = "set_vehicles";
	public static final String OTOKOU_GET_USER_ACTION = "set_user";
	
	public static void setNewCharge(OtokouCharge charge, OtokouUser user) {
		// TODO all
	}
	
	public static OtokouUser getUserData(String apiKey) {
		// TODO all	
		return null;		
	}
	
	public static ArrayList<OtokouVehicle> getVehiclesData(String apiKey, OtokouUser otokouUser2) {
		// TODO all
		return null;	
	}
}
