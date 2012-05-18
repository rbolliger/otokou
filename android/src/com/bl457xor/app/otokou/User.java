package com.bl457xor.app.otokou;

import java.util.ArrayList;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.SharedPreferences.OnSharedPreferenceChangeListener;
import android.database.Cursor;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
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

import com.bl457xor.app.otokou.db.OtokouUserAdapter;
import com.bl457xor.app.otokou.db.OtokouVehicleAdapter;

public class User extends Activity implements OnSharedPreferenceChangeListener, OnClickListener, Runnable {
	// messages constants
	public static final int RETURN_RESULT_OK = 1000;
	public static final int RETURN_RESULT_BACK = 1001;
	public static final int RETURN_RESULT_USER_NOT_FOUND = 1002;
	
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
	private OtokouUser otokouUser;
	private TextView txtUserWelcome;
	private SharedPreferences preferences;
	private ArrayList<OtokouVehicle> vehicles;
	private ProgressDialog progressDialog;
	private TextView txtUser;
	private boolean dataOK = false;
	private Button btnAddCharge;
	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.user);
        
        setResult(RETURN_RESULT_BACK, null);

		retrieveUserData();

        initializeUI();
        
        initializePreferences();
        
    	if (!dataOK) {
    		retrieveDataFromOtokou();
    	}
    }
    
    @Override
    protected void onResume() {
    	Log.i("resume","1");
    	registerPreferences();
    	Log.i("resume","2");
    	super.onResume();
    }
    
    @Override
    protected void onPause() {
    	unregisterPreferences();
    	super.onPause();
    }

	private void retrieveUserData() {		
		OtokouUserAdapter OUAdb = new OtokouUserAdapter(getApplicationContext()).open();
		Cursor c = OUAdb.getUserById(getIntent().getExtras().getLong("user_id"));

		if (c.getCount() == 1) {
			c.moveToLast();
			try {
				otokouUser = new OtokouUser(c);
				txtUserWelcome.setText(otokouUser.getFirstName());
			} catch (Exception e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			finally {
				c.close();
				OUAdb.close();			
			}
		}
		else {
			c.close();
			OUAdb.close();			
			setResult(RETURN_RESULT_USER_NOT_FOUND, null);
			finish();
		}	
	}
    
    private void initializePreferences() {
        preferences = PreferenceManager.getDefaultSharedPreferences(this);
        preferences.edit().putString("apikey", otokouUser.getApikey()).commit();
	}
    
    private void registerPreferences() {
        preferences.registerOnSharedPreferenceChangeListener(this);
	}
    
    private void unregisterPreferences() {
        preferences.unregisterOnSharedPreferenceChangeListener(this);
	}
    
	private void initializeUI() {		
		// create button to open the preferences
		((Button)findViewById(R.id.btnUserUserPreferences)).setOnClickListener(this);

		// create button to reload data from website
		((Button)findViewById(R.id.btnUserReloadData)).setOnClickListener(this);
		
		// create button to add a new charge
		btnAddCharge = (Button) findViewById(R.id.btnUserAddCharge);
		btnAddCharge.setOnClickListener(this);
		
		// TO DELETE button to test
		((Button)findViewById(R.id.btnUserTest)).setOnClickListener(this);
		
		// create text view for user communication
		txtUser = (TextView)findViewById(R.id.txtUserUser);
	}
	
	private void retrieveDataFromOtokou() {
		dataOK = false;
		
    	// create progress dialog
    	progressDialog = ProgressDialog.show(this,getString(R.string.user_dialog_title), getString(R.string.user_dialog_message_start), true, false);
    	
    	// launch thread, connection with otokou website
    	Thread thread = new Thread(this);
    	thread.start();
	}

	@Override
	public void run() {
		// TODO handle errors more detailed with exceptions from otokouAPI
		if (isOnline()) {
			String apiKey = otokouUser.getApikey();
			String username = otokouUser.getUsername();
			long userId = otokouUser.getId();
			
			if (!OtokouApiKey.checkKey(apiKey)) {
				handler.sendEmptyMessage(RUN_ERROR_API_KEY);
			}
			else {
				// load user data from Otokou
				handler.sendEmptyMessage(RUN_MSG_LOADING_USER);
				OtokouUser retrivedOtokouUser = OtokouAPI.getUserData(username, apiKey);
				
				if (retrivedOtokouUser != null) {
					retrivedOtokouUser.addLocalUserData(otokouUser);
					
					// save user data to database
					OtokouUserAdapter OUAdb = new OtokouUserAdapter(getApplicationContext()).open();
					OUAdb.updateUsersById(userId, retrivedOtokouUser);
					OUAdb.close();
					
					// check if vehicles data has changed
					if (otokouUser.vehiclesAreOutOfDate(retrivedOtokouUser)) {
						otokouUser = null;
						otokouUser = retrivedOtokouUser;
						retrivedOtokouUser = null;
						
						// load vehicles data from Otokou
						handler.sendEmptyMessage(RUN_MSG_LOADING_VEHICLES);
						vehicles = OtokouAPI.getVehiclesData(username, apiKey);
						
						if (vehicles != null) {
							OtokouVehicleAdapter OVAdb = new OtokouVehicleAdapter(getApplicationContext()).open();
							OVAdb.updateVehicleForUser(otokouUser, vehicles);
							OVAdb.close();
							handler.sendEmptyMessage(RUN_MSG_LOADING_OK);
						}
						else {
							handler.sendEmptyMessage(RUN_ERROR_VEHICLES);
						}						
					}
					else {
						otokouUser = null;
						otokouUser = retrivedOtokouUser;
						retrivedOtokouUser = null;
						
						OtokouVehicleAdapter OVAdb = new OtokouVehicleAdapter(getApplicationContext()).open();
						Cursor vehiclesCursor = OVAdb.getVehiclesByUserId(otokouUser.getId());
						vehicles = OtokouVehicle.getVehiclesFromCursor(vehiclesCursor);
						vehiclesCursor.close();
						OVAdb.close();
						handler.sendEmptyMessage(RUN_MSG_LOADING_OK);
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
				progressDialog.setMessage(getString(R.string.user_dialog_message_loading_user));
				break;
			case RUN_MSG_LOADING_VEHICLES:	
				progressDialog.setMessage(getString(R.string.user_dialog_message_loading_vehicles));
				break;
			case RUN_MSG_LOADING_OK:
				progressDialog.setMessage(getString(R.string.user_dialog_message_ok));
				txtUser.setText(otokouUser.toString());
				dataOK = true;
				btnAddCharge.setVisibility(Button.VISIBLE);
				break;
			case RUN_ERROR_NOT_CONNECTED:
				txtUser.setText(getString(R.string.user_txt_user_error_not_connected));
				dataOK = false;
				btnAddCharge.setVisibility(Button.INVISIBLE);
				break;
			case RUN_ERROR_API_KEY:
				txtUser.setText(getString(R.string.user_txt_user_error_api_key));
				dataOK = false;
				btnAddCharge.setVisibility(Button.INVISIBLE);
				break;
			case RUN_ERROR_USER:
				txtUser.setText(getString(R.string.user_txt_user_error_user));
				dataOK = false;
				btnAddCharge.setVisibility(Button.INVISIBLE);
				break;
			case RUN_ERROR_VEHICLES:
				txtUser.setText(otokouUser.toString()+"\n"+getString(R.string.user_txt_user_error_vehicle));
				dataOK = false;
				btnAddCharge.setVisibility(Button.INVISIBLE);
				break;
			}
		}
	};
	
	private void launchPreferencesUserActivity(){
		Intent i = new Intent(User.this, UserPreferenceActivity.class);
		startActivity(i);
	}
	
	private void launchAddChargeActivity() {
		Intent i = new Intent(User.this, AddCharge.class);
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
	
	private void launchTest() {
		// add a test here
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
		menu.add(Menu.NONE, MENU_ID_USER_PREFERENCES, Menu.NONE, R.string.user_menu_user_preferences).setIcon(R.drawable.menu_user_preferences);
		menu.add(Menu.NONE, MENU_ID_RELOAD_DATA, Menu.NONE, R.string.user_menu_reload_data).setIcon(R.drawable.menu_reload);
		menu.add(Menu.NONE, MENU_ID_ADD_CHARGE, Menu.NONE, R.string.user_menu_add_charge).setIcon(R.drawable.menu_add);
		menu.add(Menu.NONE, MENU_ID_EXIT, Menu.NONE, R.string.user_menu_exit).setIcon(R.drawable.exit);
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
		case R.id.btnUserUserPreferences:
			launchPreferencesUserActivity();
			break;
		case R.id.btnUserReloadData:
			retrieveDataFromOtokou();
			break;
		case R.id.btnUserAddCharge:
			launchAddChargeActivity();
			break;
		case R.id.btnUserTest:
			launchTest();
			break;			
		}		
	}
	
	@Override
	public void onSharedPreferenceChanged(SharedPreferences sharedPreferences,
			String key) {

		String newApiKey = preferences.getString("apikey", "");
		if (OtokouApiKey.checkKey(newApiKey)) {
			otokouUser.setApikey(newApiKey);
			OtokouUserAdapter OUAdb = new OtokouUserAdapter(getApplicationContext()).open();
			OUAdb.updateUsersById(otokouUser.getId(), otokouUser);
			OUAdb.close();
			retrieveDataFromOtokou();
		}
		else {
			txtUser.setText(R.string.user_txt_user_error_api_key);
		}	
	}
}
