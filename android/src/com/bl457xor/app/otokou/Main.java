package com.bl457xor.app.otokou;

import java.util.ArrayList;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.preference.PreferenceManager;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.TextView;

public class Main extends Activity implements Runnable, OnClickListener {			
	// onOptionsItemSelected menu ids constants
	public static final int MENU_ID_USER_PREFERENCES = 2001;
	public static final int MENU_ID_ADD_CHARGE = 2002;
	public static final int MENU_ID_EXIT = 2200;
	
	// run messages constants
	public static final int RUN_END = 0;
	public static final int RUN_MSG_LOADING_USER = 10;
	public static final int RUN_MSG_LOADING_VEHICLES = 11;
	public static final int RUN_MSG_LOADING_OK = 12;
	public static final int RUN_ERROR_API_KEY = 100;
	public static final int RUN_ERROR_USER = 101;
	public static final int RUN_ERROR_VEHICLES = 102;
	
	// global variables initialization
	SharedPreferences preferences;
	OtokouUser otokouUser;
	ArrayList<OtokouVehicle> vehicles;
	ProgressDialog progressDialog;
	TextView txtUser;
	boolean dataOK = false;
	Button btnAddCharge;
	
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);

        preferences = PreferenceManager.getDefaultSharedPreferences(this);
		
		initializeUI();
    }
    
    @Override
    protected void onStart() { 	
    	super.onStart();
    }
    
    @Override
    protected void onResume() {
    	progressDialog = ProgressDialog.show(this,getString(R.string.main_dialog_title), getString(R.string.main_dialog_message_start), true, false);
    	Thread thread = new Thread(this);
    	thread.start();
    	
    	super.onResume();
    }

	private void initializeUI() {
		btnAddCharge = (Button) findViewById(R.id.btnAddCharge);
		btnAddCharge.setOnClickListener(this);
      
		Button btnUserPreferences = (Button) findViewById(R.id.btnUserPreferences);
		btnUserPreferences.setOnClickListener(this);
		
		txtUser = (TextView)findViewById(R.id.txtUser);
	}
	
	public void run() {
		// TODO check connection active, handle errors
		
        String apiKey = preferences.getString("apikey", "");
        if (!OtokouApiKey.checkKey(apiKey)) {
        	handler.sendEmptyMessage(RUN_ERROR_API_KEY);
        }
        else {
        	handler.sendEmptyMessage(RUN_MSG_LOADING_USER);
        	otokouUser = OtokouAPI.getUserData(apiKey);
        	
            if (otokouUser != null) {
            	handler.sendEmptyMessage(RUN_MSG_LOADING_VEHICLES);
            	vehicles = OtokouAPI.getVehiclesData(apiKey,otokouUser);
            	
            	if (vehicles != null) {
            		handler.sendEmptyMessage(RUN_MSG_LOADING_OK);
            	}
            	else {
            		handler.sendEmptyMessage(RUN_ERROR_VEHICLES);
            	}
            }
            else {
            	handler.sendEmptyMessage(RUN_ERROR_USER);
            }
        }
        
		handler.sendEmptyMessage(RUN_END);
	}

	private Handler handler = new Handler() {
		@Override
		public void handleMessage(Message msg) {
			switch (msg.what) {
			case RUN_END:
				progressDialog.dismiss();
				break;
			case RUN_MSG_LOADING_USER:	
				progressDialog.setMessage(getString(R.string.main_dialog_message_loading_user));
				break;
			case RUN_MSG_LOADING_VEHICLES:	
				progressDialog.setMessage(getString(R.string.main_dialog_message_loading_vehicles));
				break;
			case RUN_MSG_LOADING_OK:
				progressDialog.setMessage(getString(R.string.main_dialog_message_ok));
				txtUser.setText(otokouUser.toString());
				dataOK = true;
				btnAddCharge.setVisibility(Button.VISIBLE);
				break;
			case RUN_ERROR_API_KEY:
				txtUser.setText(getString(R.string.main_txt_user_error_api_key));
				dataOK = false;
				btnAddCharge.setVisibility(Button.INVISIBLE);
				break;
			case RUN_ERROR_USER:
				txtUser.setText(getString(R.string.main_txt_user_error_user));
				dataOK = false;
				btnAddCharge.setVisibility(Button.INVISIBLE);
				break;
			case RUN_ERROR_VEHICLES:
				txtUser.setText(otokouUser.toString()+"\n"+getString(R.string.main_txt_user_error_vehicle));
				dataOK = false;
				btnAddCharge.setVisibility(Button.INVISIBLE);
				break;
			}
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
		for (OtokouVehicle vehicle : vehicles) {
			extras.putByteArray("vehicle_"+vehiclesNumber, vehicle.toByteArray());
			vehiclesNumber++;
		}
		extras.putInt("vehiclesNumber", vehiclesNumber);
		
		extras.putByteArray("user", otokouUser.toByteArray());	
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
		if (dataOK) {
			menu.findItem(MENU_ID_ADD_CHARGE).setVisible(true);
		}
		else {
			menu.findItem(MENU_ID_ADD_CHARGE).setVisible(false);
		}
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

	@Override
	public void onClick(View v) {
		switch (v.getId()) {
		case R.id.btnAddCharge:
			launchAddChargeActivity();
			break;
		case R.id.btnUserPreferences:
			launchPreferencesUserActivity();
			break;
		}
		
	}
}