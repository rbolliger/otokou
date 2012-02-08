package com.bl457xor.app.otokou;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.ObjectOutputStream;
import java.util.ArrayList;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.preference.PreferenceManager;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

public class Main extends Activity implements Runnable {	
	// general constants
	public static final String OTOKOU_API_URL = "http://otokou.donax.ch/api";
		
	// onOptionsItemSelected menu ids constants
	public static final int MENU_ID_USER_PREFERENCES = 2001;
	public static final int MENU_ID_ADD_CHARGE = 2002;
	public static final int MENU_ID_EXIT = 2200;
	
	// global variables initialization
	TextView txtLog;
	SharedPreferences preferences;
	OtokouUser otokouUser;
	ArrayList<OtokouVehicle> vehicles;
	ProgressDialog progressDialog;
	
	
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);

        preferences = PreferenceManager.getDefaultSharedPreferences(this);
        
		Button btnAddCharge = (Button) findViewById(R.id.addCharge);
		btnAddCharge.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {			
				launchAddChargeActivity();
			}
		});
        
		Button btnUserPreferences = (Button) findViewById(R.id.userPreferences);
		btnUserPreferences.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {			
				launchPreferencesUserActivity();
			}
		});
		
		initializeUI();
    }
    
    @Override
    protected void onStart() {
    	Log.i("Main","Start");   	
    	super.onStart();
    }
    
    @Override
    protected void onResume() {
    	Log.i("Main","Resume");	
    	   	
    	progressDialog = ProgressDialog.show(this, "Loading information from Otokou", "Connecting ...", true, false);

    	Thread thread = new Thread(this);
    	thread.start();
    	

    	super.onResume();
    }

	private void initializeUI() {
		txtLog = (TextView)findViewById(R.id.TxtLog);
	}
	
	private OtokouUser getUserData(String apiKey) {
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
	
	private ArrayList<OtokouVehicle> getVehiclesData(String apiKey, OtokouUser otokouUser2) {
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
	
	public void run() {
        String apiKey = preferences.getString("apikey", "n/a");
        if (!OtokouApiKey.checkKey(apiKey)) {
        	Toast.makeText(Main.this,"Api Key not set or invalid, please set it in the Preferences", Toast.LENGTH_LONG).show();
        	//txtLog.setText("api key needed");
        }
        else {
        	//txtLog.setText("connecting ...");
        	handler. sendEmptyMessage(1);
        	otokouUser = getUserData(apiKey);
        	
            if (otokouUser != null) {
            	handler. sendEmptyMessage(1);
            	vehicles = getVehiclesData(apiKey,otokouUser);
            	if (vehicles != null) {   
            		//txtLog.setText(otokouUser.toString()+vehicles);
            	}
            	else {
            		//txtLog.setText(otokouUser.toString());
            	}
            }
            else {
            	//txtLog.setText("user data not retrieved");
            }
        }
		handler.sendEmptyMessage(0);
	}

	private Handler handler = new Handler() {
		@Override
		public void handleMessage(Message msg) {
			switch (msg.what) {
			case 0:
				progressDialog.dismiss();
				break;
			case 1:	
				progressDialog.setMessage("Loading user data ...");
				break;
			case 2:	
				progressDialog.setMessage("Loading vehicle data ...");
				break;
			}
			progressDialog.dismiss();
			txtLog.setText("asd");
		}
	};
	
	private void launchPreferencesUserActivity(){
		Intent i = new Intent(Main.this, UserPreferenceActivity.class);
		startActivity(i);
	}
	
	private void launchAddChargeActivity() {
		Intent i = new Intent(Main.this, AddCharge.class);
		Bundle extras = new Bundle();
		
		int vehiclesNumber = 0;
		
		try {
			for (OtokouVehicle vehicle : vehicles) {
		        ByteArrayOutputStream b = new ByteArrayOutputStream();
		        ObjectOutputStream o = new ObjectOutputStream(b);
		        o.writeObject(vehicle);
		        extras.putByteArray("vehicle_"+vehiclesNumber, b.toByteArray());
		        vehiclesNumber++;
			}
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		try {
	        ByteArrayOutputStream b = new ByteArrayOutputStream();
	        ObjectOutputStream o = new ObjectOutputStream(b);
	        o.writeObject(otokouUser);
	        extras.putByteArray("user", b.toByteArray());
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}	
		
		extras.putInt("vehiclesNumber", vehiclesNumber);
		i.putExtras(extras);
		startActivity(i);
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {	
		menu.add(Menu.NONE, MENU_ID_USER_PREFERENCES, Menu.NONE, R.string.main_menu_user_preferences).setIcon(R.drawable.menu_user_preferences);
		menu.add(Menu.NONE, MENU_ID_ADD_CHARGE, Menu.NONE, R.string.main_menu_add_charge).setIcon(R.drawable.menu_add);
		menu.add(Menu.NONE, MENU_ID_EXIT, Menu.NONE, R.string.main_menu_exit).setIcon(R.drawable.exit);
		return super.onCreateOptionsMenu(menu);
	}
	
	@Override
	public boolean onPrepareOptionsMenu(Menu menu) {
		return super.onPrepareOptionsMenu(menu);
	}
	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		switch(item.getItemId()) {
			case MENU_ID_USER_PREFERENCES:
				launchPreferencesUserActivity();
				break;
			case MENU_ID_ADD_CHARGE:
				launchAddChargeActivity();
				break;
			case MENU_ID_EXIT:
				finish();
				break;
		}
		return super.onOptionsItemSelected(item);
	}

}