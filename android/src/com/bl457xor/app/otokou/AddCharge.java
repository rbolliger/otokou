package com.bl457xor.app.otokou;

import java.util.ArrayList;

import android.content.Intent;
import android.os.Bundle;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.TextView;

import com.bl457xor.app.otokou.components.OtokouCharge;
import com.bl457xor.app.otokou.components.OtokouComponent;
import com.bl457xor.app.otokou.components.OtokouUser;
import com.bl457xor.app.otokou.components.OtokouVehicle;
import com.bl457xor.app.otokou.db.OtokouChargeAdapter;

public class AddCharge extends OnlineActivity implements OnClickListener, OnItemSelectedListener {
	// onOptionsItemSelected menu ids constants
	private static final int MENU_ID_ADD_CHARGE = 2002;
	private static final int MENU_ID_BACK = 2100;
	
	// messages constants
	public static final int RETURN_RESULT_CHARGE_ADDED = 3000;
	public static final int RETURN_RESULT_BACK = 3001;
	public static final int RETURN_RESULT_ERROR = 3002;
	public static final int RETURN_RESULT_UNEXPECTED = 3100;
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
	private TextView etxtKilometers;
	private TextView etxtAmount;
	private TextView etxtComment;
	private TextView etxtQuantity;
	private TextView etxtChargeAdd;
	private TextView txtQuantity;
	private DatePicker datePicker;
	private Spinner spnVehicle;
	private Spinner spnChargeCategory;
	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.add_charge);
        
        setResult(RETURN_RESULT_UNEXPECTED, null);
        
		retrieveDataFromExtras();
		
		initializeUI();
    }
    
	@Override
	protected void onResume() {
		super.onResume();
		updateUI();
	}

	@Override
	public void onBackPressed() {
		setResult(RETURN_RESULT_BACK, null);
		super.onBackPressed();
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
		datePicker = (DatePicker) findViewById(R.id.dtpAddChargeDate);
		
		spnVehicle = (Spinner) findViewById(R.id.spnAddChargeVehicle);
		String[] items = new String[vehicles.size()];
	    int i=0;
	    for (OtokouVehicle vehicle : vehicles) {
	    	items[i]=vehicle.getVehicleName();
	    	i++;
	    }
	    ArrayAdapter<String> vehicleAdapter = new ArrayAdapter<String>(this,android.R.layout.simple_spinner_item,items);
	    vehicleAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
	    spnVehicle.setAdapter(vehicleAdapter);
	    
		spnChargeCategory = (Spinner)findViewById(R.id.spnAddChargeCategory);
		ArrayAdapter<CharSequence> chargeTypeAdapter = ArrayAdapter.createFromResource(this, R.array.charge_categories, android.R.layout.simple_spinner_item);
		chargeTypeAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
		spnChargeCategory.setAdapter(chargeTypeAdapter);
		spnChargeCategory.setOnItemSelectedListener(this);
		
		
		txtQuantity = (TextView)findViewById(R.id.txtAddChargeQuantity);
		
		edtKilometers = (EditText)findViewById(R.id.edtAddChargeKilometers);
		edtAmount = (EditText)findViewById(R.id.edtAddChargeAmount);
		edtComment = (EditText)findViewById(R.id.edtAddChargeComment);
		edtQuantity = (EditText)findViewById(R.id.edtAddChargeQuantity);
		
		etxtKilometers = (TextView)findViewById(R.id.etxtAddChargeKilometers);
		etxtAmount = (TextView)findViewById(R.id.etxtAddChargeAmount);
		etxtComment = (TextView)findViewById(R.id.etxtAddChargeComment);
		etxtQuantity = (TextView)findViewById(R.id.etxtAddChargeQuantity);
		etxtChargeAdd = (TextView)findViewById(R.id.etxtAddChargeAdd);
		
		((Button)findViewById(R.id.btnAddChargeAdd)).setOnClickListener(this);
	}
	
	private void updateUI() {
		if (isOnline()) {
			if (etxtChargeAdd.getText().toString().contentEquals(getString(R.string.add_charge_warning_offline))) {
				etxtChargeAdd.setText(R.string.add_charge_warning_online);
			}
		}
		else {
			etxtChargeAdd.setText(R.string.add_charge_warning_offline);
		}
	}
	
	private void submit() {		
		if (checkCorrectValues()) {
			String quantity = "0";
			if (isNumeric(edtQuantity.getText().toString())) {
				quantity = edtQuantity.getText().toString();
			}
			OtokouCharge charge = new OtokouCharge(vehicles.get((int)spnVehicle.getSelectedItemId()).getOtokouVehicleId(), 
					vehicles.get((int)spnVehicle.getSelectedItemId()).getVehicleName(), 
					(int)(spnChargeCategory.getSelectedItemId()+1), 
					""+datePicker.getYear()+"-"+(datePicker.getMonth()+1)+"-"+datePicker.getDayOfMonth(),
					Double.parseDouble(edtKilometers.getText().toString()),
					Double.parseDouble(edtAmount.getText().toString()),
					edtComment.getText().toString(),
					Double.parseDouble(quantity));

			if (isOnline()) {
				OtokouComponent otokouComponent = OtokouAPI.setNewChargeData(otokouUser.getUsername(), otokouUser.getApikey(), charge);
				if (otokouComponent.isValid()) {
					OtokouChargeAdapter OCAdb = new OtokouChargeAdapter(getApplicationContext()).open();
					OCAdb.insertCharge(charge, otokouUser, vehicles.get((int)spnVehicle.getSelectedItemId()), OtokouChargeAdapter.COL_4_SENT_VALUE);
					OCAdb.close();	
					setResult(RETURN_RESULT_CHARGE_ADDED, null);
					finish();			
				}
				else {
					errorHandler(otokouComponent);		
				}		
			}
			else {
				OtokouChargeAdapter OCAdb = new OtokouChargeAdapter(getApplicationContext()).open();
				OCAdb.insertCharge(charge, otokouUser, vehicles.get((int)spnVehicle.getSelectedItemId()), OtokouChargeAdapter.COL_4_NOT_SENT_VALUE);
				OCAdb.close();

				Intent i = new Intent();
				Bundle extras = new Bundle();
				extras.putInt(RETURN_ERROR_EXTRA_KEY, RETURN_ERROR_NO_CONNECTION);
				i.putExtras(extras);
				setResult(RETURN_RESULT_ERROR, i);
				finish();
			}
		}
	}
	
	private boolean checkCorrectValues() {
		boolean returnValue = true;
		etxtKilometers.setText("");
		etxtAmount.setText("");
		etxtComment.setText("");
		etxtQuantity.setText("");
		
		if (!isNumeric(edtKilometers.getText().toString())) {
			etxtKilometers.setText(" "+getString(R.string.add_charge_error_kilometers));
			returnValue = false;
		}
		
		if (!isNumeric(edtAmount.getText().toString())) {
			etxtAmount.setText(" "+getString(R.string.add_charge_error_amount));
			returnValue = false;
		}
		
		if (!isNumeric(edtQuantity.getText().toString()) && spnChargeCategory.getSelectedItemId()==0 ) {
			etxtQuantity.setText(" "+getString(R.string.add_charge_error_quantity));
			returnValue = false;
		}
		
		return returnValue;
	}

	private boolean isNumeric(String string) {
		try {
			Double.parseDouble(string);
			return true;
		}
		catch(NumberFormatException nfe) {
			return false;
		}
	}

	private void errorHandler(OtokouComponent otokouComponent) {
		switch (otokouComponent.getErrorCode()) {
		case OtokouException.CODE_RESPONSE_SET_CHARGE_INCORRECT_LOGIN:
			etxtChargeAdd.setText(R.string.add_charge_error_user_login);
			break;
		default:
			etxtChargeAdd.setText(R.string.add_charge_error_set_charge);
		}		
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {	
		menu.add(Menu.NONE, MENU_ID_ADD_CHARGE, Menu.NONE, R.string.add_charge_menu_send);
		menu.add(Menu.NONE, MENU_ID_BACK, Menu.NONE, R.string.add_charge_menu_back);
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
		case R.id.btnAddChargeAdd:
			submit();
			break;
		}			
	}

	@Override
	public void onItemSelected(AdapterView<?> arg0, View arg1, int position,
			long id) {
		if (id == 0) {
			txtQuantity.setVisibility(View.VISIBLE);
			etxtQuantity.setVisibility(View.VISIBLE);
			edtQuantity.setVisibility(View.VISIBLE);
		}
		else {
			txtQuantity.setVisibility(View.INVISIBLE);
			etxtQuantity.setVisibility(View.INVISIBLE);
			edtQuantity.setVisibility(View.INVISIBLE);
		}		
	}

	@Override
	public void onNothingSelected(AdapterView<?> arg0) {
		txtQuantity.setVisibility(View.VISIBLE);
		etxtQuantity.setVisibility(View.VISIBLE);
		edtQuantity.setVisibility(View.VISIBLE);
	}
}
