package com.bl457xor.app.otokou;

import java.util.ArrayList;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.SharedPreferences.OnSharedPreferenceChangeListener;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
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

public class Main extends Activity implements Runnable, OnClickListener, OnSharedPreferenceChangeListener {			
	// onOptionsItemSelected menu ids constants
	private static final int MENU_ID_USER_PREFERENCES = 2001;
	private static final int MENU_ID_RELOAD_DATA = 2002;
	private static final int MENU_ID_ADD_CHARGE = 2003;
	private static final int MENU_ID_EXIT = 2200;
	
	// run messages constants
	private static final int RUN_END = 0;
	private static final int RUN_MSG_LOADING_USER = 10;
	private static final int RUN_MSG_LOADING_VEHICLES = 11;
	private static final int RUN_MSG_LOADING_OK = 12;
	private static final int RUN_ERROR_NOT_CONNECTED = 100;
	private static final int RUN_ERROR_API_KEY = 101;
	private static final int RUN_ERROR_USER = 102;
	private static final int RUN_ERROR_VEHICLES = 103;

	// global variables initialization
	private SharedPreferences preferences;
	private OtokouUser otokouUser;
	private ArrayList<OtokouVehicle> vehicles;
	private ProgressDialog progressDialog;
	private TextView txtUser;
	private boolean dataOK = false;
	private Button btnAddCharge;
	
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
        
		initializePreferences();
        
		initializeUI();
		
    	if (!dataOK) {
    		retrieveDataFromOtokou();
    	}
    }

	private void retrieveDataFromOtokou() {
		dataOK = false;
		
    	// create progress dialog
    	progressDialog = ProgressDialog.show(this,getString(R.string.main_dialog_title), getString(R.string.main_dialog_message_start), true, false);
    	
    	// launch thread, connection with otokou website
    	Thread thread = new Thread(this);
    	thread.start();
	}

    private void initializePreferences() {
        // load user preferences
        preferences = PreferenceManager.getDefaultSharedPreferences(this);
        preferences.registerOnSharedPreferenceChangeListener(this);
	}
	
	private void initializeUI() {		
		// create button to open the preferences
		((Button)findViewById(R.id.btnUserPreferences)).setOnClickListener(this);

		// create button to reload data from website
		((Button)findViewById(R.id.btnReloadData)).setOnClickListener(this);
		
		// create button to add a new charge
		btnAddCharge = (Button) findViewById(R.id.btnAddCharge);
		btnAddCharge.setOnClickListener(this);
		
		// create text view for user communication
		txtUser = (TextView)findViewById(R.id.txtUser);
	}
	
	public void run() {
		// TODO handle errors more detailed with exceptions from otokouAPI
		
		if (isOnline()) {
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
		}
		else {
			handler.sendEmptyMessage(RUN_ERROR_NOT_CONNECTED);
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
			case RUN_ERROR_NOT_CONNECTED:
				txtUser.setText(getString(R.string.main_txt_user_error_not_connected));
				dataOK = false;
				btnAddCharge.setVisibility(Button.INVISIBLE);
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
		
		startActivityForResult(i, 0);
	}
	
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		// TODO manage returns of
		if (resultCode == AddCharge.RETURN_RESULT_OK) {
		    
		}
		else if (resultCode == AddCharge.RETURN_RESULT_BACK) {

		}
		else if (resultCode == AddCharge.RETURN_RESULT_ERROR) {
			switch (data.getExtras().getInt(AddCharge.RETURN_ERROR_EXTRA_KEY,AddCharge.RETURN_ERROR_UNKNOWN)) {
			case AddCharge.RETURN_ERROR_UNKNOWN:
				break;
			case AddCharge.RETURN_ERROR_NO_CONNECTION:
				break;
			}
		}
		else {
			
		}
	}
	
	private boolean isOnline() {
	    ConnectivityManager cm = (ConnectivityManager)getSystemService(Context.CONNECTIVITY_SERVICE);
	    NetworkInfo netInfo = cm.getActiveNetworkInfo();
	    if (netInfo != null && netInfo.isConnectedOrConnecting()) {
	        return true;
	    }
	    return false;
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {	
		menu.add(Menu.NONE, MENU_ID_USER_PREFERENCES, Menu.NONE, R.string.main_menu_user_preferences).setIcon(R.drawable.menu_user_preferences);
		menu.add(Menu.NONE, MENU_ID_RELOAD_DATA, Menu.NONE, R.string.main_menu_reload_data).setIcon(R.drawable.menu_reload);
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
			case MENU_ID_RELOAD_DATA:
				retrieveDataFromOtokou();
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
		case R.id.btnUserPreferences:
			launchPreferencesUserActivity();
			break;
		case R.id.btnReloadData:
			retrieveDataFromOtokou();
			break;
		case R.id.btnAddCharge:
			launchAddChargeActivity();
			break;
		}		
	}

	@Override
	public void onSharedPreferenceChanged(SharedPreferences sharedPreferences, String key) {
		retrieveDataFromOtokou();		
	}
}