package com.bl457xor.app.otokou;

import java.util.ArrayList;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.Spinner;

public class AddCharge extends Activity implements OnClickListener {
	// onOptionsItemSelected menu ids constants
	private static final int MENU_ID_ADD_CHARGE = 2002;
	private static final int MENU_ID_BACK = 2100;
	
	// messages constants
	public static final int RETURN_RESULT_OK = 1000;
	public static final int RETURN_RESULT_BACK = 1001;
	public static final int RETURN_RESULT_ERROR = 1002;
	public static final String RETURN_ERROR_EXTRA_KEY = "code";
	public static final int RETURN_ERROR_UNKNOWN = 0;
	public static final int RETURN_ERROR_NO_CONNECTION = 1;
	
	// global variables initialization
	private OtokouUser otokouUser;
	private ArrayList<OtokouVehicle> vehicles = new ArrayList<OtokouVehicle>();
	private EditText edtKilometers;
	private EditText edtAmount;
	private EditText edtComment;
	private EditText edtQuantity;
	private DatePicker datePicker;
	private Spinner spnVehicle;
	private Spinner spnChargeCategory;
	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.add_charge);
        
        setResult(RETURN_RESULT_BACK, null);
        
		retrieveDataFromExtras();
		
		initializeUI();
    }
    
	private void retrieveDataFromExtras() {
		// TODO check extras loaded correctly
		
		int vehiclesNumber = getIntent().getExtras().getInt("vehiclesNumber");
		
		for (int i=0; i<vehiclesNumber; i++) {
			vehicles.add(OtokouVehicle.OtokouVehicleFromByteArray(getIntent().getExtras().getByteArray("vehicle_"+i)));
		}			

		otokouUser = OtokouUser.OtokouUserFromByteArray(getIntent().getExtras().getByteArray("user"));		
	}

	private void initializeUI() {		
		datePicker = (DatePicker) findViewById(R.id.datePicker);
		
		spnVehicle = (Spinner) findViewById(R.id.spnVehicle);
		String[] items = new String[vehicles.size()];
	    int i=0;
	    for (OtokouVehicle vehicle : vehicles) {
	    	items[i]=vehicle.vehicle;
	    	i++;
	    }
	    ArrayAdapter<String> vehicleAdapter = new ArrayAdapter<String>(this,android.R.layout.simple_spinner_item,items);
	    vehicleAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
	    spnVehicle.setAdapter(vehicleAdapter);
	    
		spnChargeCategory = (Spinner)findViewById(R.id.spnChargeCategory);
		ArrayAdapter<CharSequence> chargeTypeAdapter = ArrayAdapter.createFromResource(this, R.array.charge_categories, android.R.layout.simple_spinner_item);
		chargeTypeAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
		spnChargeCategory.setAdapter(chargeTypeAdapter);

		edtKilometers = (EditText)findViewById(R.id.edtKilometers);
		edtAmount = (EditText)findViewById(R.id.edtAmount);
		edtComment = (EditText)findViewById(R.id.edtComment);
		edtQuantity = (EditText)findViewById(R.id.edtQuantity);
		
		((Button)findViewById(R.id.btnAdd)).setOnClickListener(this);
	}
	
	private void submit() {	
		// TODO evaluate values, notify errors, exceptions from otokouAPI
		
		if (isOnline()) {
			OtokouCharge charge = new OtokouCharge(vehicles.get((int)spnVehicle.getSelectedItemId()).vehicleID, 
					vehicles.get((int)spnVehicle.getSelectedItemId()).vehicle, 
					(int)(spnChargeCategory.getSelectedItemId()+1), 
					""+datePicker.getYear()+"-"+(datePicker.getMonth()+1)+"-"+datePicker.getDayOfMonth(),
					Double.parseDouble(edtKilometers.getText().toString()),
					Double.parseDouble(edtAmount.getText().toString()),
					edtComment.getText().toString(),
					Double.parseDouble(edtQuantity.getText().toString()));


			OtokouTestAPI.setNewCharge(charge, otokouUser);
			setResult(RETURN_RESULT_OK, null);
			finish();
		}
		else {					
			Intent i = new Intent();
			Bundle extras = new Bundle();
			extras.putInt(RETURN_ERROR_EXTRA_KEY, RETURN_ERROR_NO_CONNECTION);
			i.putExtras(extras);
			setResult(RETURN_RESULT_ERROR, null);
			finish();
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
		menu.add(Menu.NONE, MENU_ID_ADD_CHARGE, Menu.NONE, R.string.add_charge_menu_add).setIcon(R.drawable.menu_add);
		menu.add(Menu.NONE, MENU_ID_BACK, Menu.NONE, R.string.add_charge_menu_back).setIcon(R.drawable.exit);
		return super.onCreateOptionsMenu(menu);
	}
	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		switch(item.getItemId()) {
			case MENU_ID_ADD_CHARGE:
				submit();
				break;
			case MENU_ID_BACK:
				setResult(RETURN_RESULT_BACK, null);
				finish();
				break;
		}
		return super.onOptionsItemSelected(item);
	}

	@Override
	public void onClick(View v) {
		switch (v.getId()) {
		case R.id.btnAdd:
			submit();
			break;
		}			
	}
}
