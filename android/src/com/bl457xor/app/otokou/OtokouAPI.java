package com.bl457xor.app.otokou;

import java.util.ArrayList;

import android.util.Log;

public class OtokouAPI {
	// general constants
	public static final String OTOKOU_API_URL = "http://otokou.donax.ch/api";
	
	public static void setNewCharge(OtokouCharge charge, OtokouUser user) {
		// TODO evaluate response errors, exceptions
		
		String getRequest = OTOKOU_API_URL;
		getRequest += "?request=set_charge";
		getRequest += ","+user.apiKey;
		getRequest += ","+charge.vehicleID+","+charge.categoryID+","+charge.date+","+charge.kilometers+","+charge.amount+","+charge.comment+","+charge.quantity;
		HttpHelper httpHelper = new HttpHelper();
    	try {
    		String getResponse = httpHelper.executeHttpGet(getRequest);
    		Log.i("request",getRequest);
    		Log.i("response",getResponse);
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}	
	}
	
	public static OtokouUser getUserData(String apiKey) {
		String getRequest = OTOKOU_API_URL;
		getRequest += "?request=get_user";
		getRequest += ","+apiKey;
		HttpHelper httpHelper = new HttpHelper();
    	try {		
    		return new OtokouUser(httpHelper.executeHttpGet(getRequest),apiKey);
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return null;
		}		
	}
	
	public static ArrayList<OtokouVehicle> getVehiclesData(String apiKey, OtokouUser otokouUser2) {
		String getRequest = OTOKOU_API_URL;
		getRequest += "?request=get_vehicles";
		getRequest += ","+apiKey;
		HttpHelper httpHelper = new HttpHelper();
    	try {		
    		return OtokouVehicle.CollectionFromString(httpHelper.executeHttpGet(getRequest));
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
			return null;
		}
	}
	
	
}
