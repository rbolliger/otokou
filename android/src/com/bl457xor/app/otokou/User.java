package com.bl457xor.app.otokou;

import android.app.ProgressDialog;
import android.content.Intent;
import android.content.SharedPreferences;
import android.database.Cursor;
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
import android.widget.Toast;

import com.bl457xor.app.otokou.components.OtokouApiKey;
import com.bl457xor.app.otokou.components.OtokouCharge;
import com.bl457xor.app.otokou.components.OtokouComponent;
import com.bl457xor.app.otokou.components.OtokouUser;
import com.bl457xor.app.otokou.components.OtokouVehicle;
import com.bl457xor.app.otokou.components.OtokouVehicles;
import com.bl457xor.app.otokou.db.OtokouChargeAdapter;
import com.bl457xor.app.otokou.db.OtokouUserAdapter;
import com.bl457xor.app.otokou.db.OtokouVehicleAdapter;

public class User extends OnlineActivity implements OnClickListener, Runnable {
	// messages constants
	public static final int RETURN_RESULT_OK = 1000;
	public static final int RETURN_RESULT_BACK = 1001;
	public static final int RETURN_RESULT_USER_NOT_FOUND = 1002;
	public static final int RETURN_RESULT_UNEXPECTED = 1100;
	
	// onOptionsItemSelected menu ids constants
	private static final int MENU_ID_USER_PREFERENCES = 10001;
	private static final int MENU_ID_RELOAD_DATA = 10002;
	private static final int MENU_ID_ADD_CHARGE = 10003;
	private static final int MENU_ID_EXIT = 10004;
	
	// run messages constants
	private static final int RUN_END = 0;
	private static final int RUN_MSG_LOADING_USER = 10;
	private static final int RUN_MSG_LOADING_VEHICLES = 11;
	private static final int RUN_MSG_SENDING_CHARGES = 12;
	private static final int RUN_MSG_LOADING_OK = 13;	
	private static final int RUN_ERROR_NOT_CONNECTED = 100;
	private static final int RUN_ERROR_API_KEY = 101;
	private static final int RUN_ERROR_USER = 102;
	private static final int RUN_ERROR_VEHICLES = 103;
	private static final int RUN_ERROR_USER_LOGIN = 104;
	
	// global variables initialization
	private OtokouUser otokouUser;
	private SharedPreferences preferences;
	private OtokouVehicles vehicles;
	private ProgressDialog progressDialog;
	private TextView txtUser;
	private TextView txtVehicles;
	private TextView txtUserWarning;
	private boolean dataOK = false;
	private Button btnAddCharge;
	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.user);
        
        setResult(RETURN_RESULT_UNEXPECTED, null);

		retrieveUserData();

        initializeUI();
        
        initializePreferences();
    }
    
    @Override
    protected void onResume() {
    	if (!checkPreferencesChanges()) {
        	if (!dataOK) {
        		retrieveDataFromOtokou();
        	}
    	}
    	
    	updateUI();
    	
    	super.onResume();
    }

	private void retrieveUserData() {		
		OtokouUserAdapter OUAdb = new OtokouUserAdapter(getApplicationContext()).open();
		Cursor c = OUAdb.getUserById(getIntent().getExtras().getLong("user_id"));

		if (c.getCount() == 1) {
			c.moveToLast();
			otokouUser = new OtokouUser(c);
			c.close();
			OUAdb.close();
			
			OtokouVehicleAdapter OVAdb = new OtokouVehicleAdapter(getApplicationContext()).open();
			Cursor cv = OVAdb.getVehiclesByUserId(otokouUser.getId());
			vehicles = new OtokouVehicles(cv);
			cv.close();
			OVAdb.close();
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
    
    private boolean checkPreferencesChanges() {
		String newApiKey = preferences.getString("apikey", "");
		
		if (!otokouUser.getApikey().equals(newApiKey)) {
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
			return true;
		}
		
		return false;
	}
    
	private void initializeUI() {		
		// create button to open the preferences
		((Button)findViewById(R.id.btnUserUserPreferences)).setOnClickListener(this);
		
		// create button to add a new charge
		btnAddCharge = (Button) findViewById(R.id.btnUserAddCharge);
		btnAddCharge.setOnClickListener(this);
		
		// create text view for user communication
		txtUser = (TextView)findViewById(R.id.txtUserUser);
		txtVehicles = (TextView)findViewById(R.id.txtUserVehicles);
		
		txtUserWarning = (TextView)findViewById(R.id.txtUserWarning);
	}
	
	private void updateUI() {
		if (isOnline()) {
			txtUserWarning.setText(getString(R.string.user_txt_user_warning_online));
		}
		else {
			txtUserWarning.setText(getString(R.string.user_txt_user_warning_offline));
		}
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
		if (isOnline()) {
			long userId = otokouUser.getId();
			String username = otokouUser.getUsername();
			String apiKey = otokouUser.getApikey();		
					
			if (!OtokouApiKey.checkKey(apiKey)) {
				handler.sendEmptyMessage(RUN_ERROR_API_KEY);
			}
			else {
				// load user data from Otokou
				handler.sendEmptyMessage(RUN_MSG_LOADING_USER);
				OtokouUser retrivedOtokouUser = OtokouAPI.getUserData(username, apiKey);
				
				if (retrivedOtokouUser.isValid()) {
					// check if vehicles data has changed
					if (otokouUser.vehiclesAreOutOfDate(retrivedOtokouUser)) {						
						// load vehicles data from Otokou
						handler.sendEmptyMessage(RUN_MSG_LOADING_VEHICLES);
						OtokouVehicles retrivedVehicles = OtokouAPI.getVehiclesData(username, apiKey);
						
						if (retrivedVehicles.isValid()) {
							OtokouVehicleAdapter OVAdb = new OtokouVehicleAdapter(getApplicationContext()).open();
							OVAdb.updateVehicleForUser(userId, retrivedVehicles.items);
							OVAdb.close();
							vehicles = null;
							vehicles = retrivedVehicles;
							retrivedVehicles = null;
													
							sendUnsentCharges(userId, username, apiKey);
							
							handler.sendEmptyMessage(RUN_MSG_LOADING_OK);
						}
						else {
							if (retrivedVehicles.getErrorCode() == OtokouException.CODE_RESPONSE_GET_VEHICLES_INCORRECT_LOGIN) {
								handler.sendEmptyMessage(RUN_ERROR_USER_LOGIN);
							}
							else {
								handler.sendEmptyMessage(RUN_ERROR_VEHICLES);
							}
						}					
					}
					else {		
						
						sendUnsentCharges(userId, username, apiKey);
						
						handler.sendEmptyMessage(RUN_MSG_LOADING_OK);
					}
					
					// update user data
					otokouUser.updateData(retrivedOtokouUser);
					retrivedOtokouUser = null;
					
					// save user data to database
					OtokouUserAdapter OUAdb = new OtokouUserAdapter(getApplicationContext()).open();
					OUAdb.updateUsersById(userId, otokouUser);
					OUAdb.close();
				}
				else {
					if (retrivedOtokouUser.getErrorCode() == OtokouException.CODE_RESPONSE_GET_USER_INCORRECT_LOGIN) {
						handler.sendEmptyMessage(RUN_ERROR_USER_LOGIN);
					}
					else {
						handler.sendEmptyMessage(RUN_ERROR_USER);
					}
				}
			}
		}
		else {
			handler.sendEmptyMessage(RUN_ERROR_NOT_CONNECTED);
		}
		handler.sendEmptyMessage(RUN_END);
	}

	private void sendUnsentCharges(long userId, String username, String apiKey) {		
		OtokouChargeAdapter OCAdb = new OtokouChargeAdapter(getApplicationContext()).open();
		Cursor cCharges = OCAdb.getUnsentChargesByUserId(userId);
		if (cCharges.getCount() > 0) {
			handler.sendEmptyMessage(RUN_MSG_SENDING_CHARGES);
			OtokouVehicleAdapter OVAdb = new OtokouVehicleAdapter(getApplicationContext()).open();
			cCharges.moveToFirst();
			do {
				OtokouCharge charge = new OtokouCharge(cCharges);
				Cursor cVehicle = OVAdb.getVehicleById(charge.getVehicleId());
				if (cVehicle.getCount() == 1) {
					cVehicle.moveToLast();
					charge.setOtokouVehicleId(cVehicle.getLong(cVehicle.getColumnIndex(OtokouVehicleAdapter.COL_2_NAME)));
					OtokouComponent otokouComponent = OtokouAPI.setNewChargeData(username, apiKey, charge);
					if (otokouComponent.isValid()) {
						OCAdb.setChargeToSent(charge.getId());	
					}
				}
				cVehicle.close();
			} while (cCharges.moveToNext());		
			OVAdb.close();
		}
		cCharges.close();
		OCAdb.close();			
	}

	private Handler handler = new Handler() {
		private static final int VEHICLES_SHOW = 0;
		private static final int VEHICLES_HIDE = 1;
		private static final int VEHICLES_ERROR = 2;
		
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
				setTxtVehicles(VEHICLES_SHOW);
				txtUserWarning.setText(getString(R.string.user_txt_user_warning_online));
				dataOK = true;
				btnAddCharge.setVisibility(Button.VISIBLE);
				break;
			case RUN_MSG_SENDING_CHARGES:
				progressDialog.setMessage(getString(R.string.user_dialog_message_sending_charges));
				break;
			case RUN_ERROR_NOT_CONNECTED:
				txtUser.setText(otokouUser.toString());
				setTxtVehicles(VEHICLES_SHOW);
				txtUserWarning.setText(getString(R.string.user_txt_user_warning_offline));
				dataOK = true;
				btnAddCharge.setVisibility(Button.VISIBLE);
				break;
			case RUN_ERROR_API_KEY:
				txtUser.setText(getString(R.string.user_txt_user_error_api_key));
				setTxtVehicles(VEHICLES_HIDE);
				txtUserWarning.setText(getString(R.string.user_txt_user_warning_online));
				dataOK = false;
				btnAddCharge.setVisibility(Button.INVISIBLE);
				break;
			case RUN_ERROR_USER:
				txtUser.setText(getString(R.string.user_txt_user_error_user));
				setTxtVehicles(VEHICLES_HIDE);
				txtUserWarning.setText(getString(R.string.user_txt_user_warning_online));
				dataOK = false;
				btnAddCharge.setVisibility(Button.INVISIBLE);
				break;
			case RUN_ERROR_VEHICLES:
				txtUser.setText(otokouUser.toString());
				setTxtVehicles(VEHICLES_ERROR);
				txtUserWarning.setText(getString(R.string.user_txt_user_warning_online));
				dataOK = false;
				btnAddCharge.setVisibility(Button.INVISIBLE);
				break;
			case RUN_ERROR_USER_LOGIN:
				txtUser.setText(getString(R.string.user_txt_user_error_user_login));
				setTxtVehicles(VEHICLES_HIDE);
				txtUserWarning.setText(getString(R.string.user_txt_user_warning_online));
				dataOK = false;
				btnAddCharge.setVisibility(Button.INVISIBLE);
				break;
			}
		}

		private void setTxtVehicles(int setCode) {
			switch (setCode) {
			case VEHICLES_SHOW:
				if (vehicles.items.size() == 0) {
					txtVehicles.setText(vehicles.items.size()+"no vehicles found");
				}
				else if (vehicles.items.size() == 1) {
					txtVehicles.setText(vehicles.items.size()+" vehicle found.");
				}
				else if (vehicles.items.size() > 1) {
					txtVehicles.setText(vehicles.items.size()+" vehicles found.");
				}
				else {
					txtVehicles.setText("");
				}
				break;
			case VEHICLES_ERROR:
				txtVehicles.setText(getString(R.string.user_txt_user_error_vehicle));
				break;
			case VEHICLES_HIDE:
			default:	
				txtVehicles.setText("");
			}
		}
	};
	
	private void launchPreferencesUserActivity(){
		Intent i = new Intent(User.this, UserPreferenceActivity.class);
		startActivity(i);
	}
	
	private void launchAddChargeActivity() {
		if (dataOK) {
			Intent i = new Intent(User.this, AddCharge.class);
			Bundle extras = new Bundle();

			int vehiclesNumber = 0;
			for (OtokouVehicle vehicle : vehicles.items) {
				extras.putByteArray("vehicle_"+vehiclesNumber, vehicle.toByteArray());
				vehiclesNumber++;
			}
			extras.putInt("vehiclesNumber", vehiclesNumber);

			extras.putByteArray("user", otokouUser.toByteArray());		
			i.putExtras(extras);

			startActivityForResult(i, 0);
		}
		else {
			Toast.makeText(getApplicationContext(), "Can't create a Charge while not syncronized with otokou website.",Toast.LENGTH_SHORT).show();
		}
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
				txtUserWarning.setText(R.string.user_txt_user_warning_unsent_charge);
				break;
			}
		}
		else {
			
		}
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {	
		menu.add(Menu.NONE, MENU_ID_USER_PREFERENCES, Menu.NONE, R.string.user_menu_user_preferences);
		menu.add(Menu.NONE, MENU_ID_RELOAD_DATA, Menu.NONE, R.string.user_menu_reload_data);
		menu.add(Menu.NONE, MENU_ID_ADD_CHARGE, Menu.NONE, R.string.user_menu_add_charge);
		menu.add(Menu.NONE, MENU_ID_EXIT, Menu.NONE, R.string.user_menu_back);
		return super.onCreateOptionsMenu(menu);
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
				setResult(RETURN_RESULT_BACK, null);
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
		case R.id.btnUserAddCharge:
			launchAddChargeActivity();
			break;		
		}		
	}
}
