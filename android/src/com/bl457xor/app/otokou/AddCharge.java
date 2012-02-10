package com.bl457xor.app.otokou;

import java.util.ArrayList;

import android.app.Activity;
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

public class AddCharge extends Activity {
	// onOptionsItemSelected menu ids constants
	public static final int MENU_ID_ADD_CHARGE = 2002;
	public static final int MENU_ID_BACK = 2100;
	
	OtokouUser otokouUser;
	ArrayList<OtokouVehicle> vehicles = new ArrayList<OtokouVehicle>();
	EditText edtKilometers;
	EditText edtAmount;
	EditText edtComment;
	EditText edtQuantity;
	DatePicker datePicker;
	Spinner spnVehicle;
	Spinner spnChargeCategory;
	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.add_charge);
              
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
	    
		spnChargeCategory = (Spinner) findViewById(R.id.spnChargeCategory);
		ArrayAdapter<CharSequence> chargeTypeAdapter = ArrayAdapter.createFromResource(this, R.array.charge_categories, android.R.layout.simple_spinner_item);
		chargeTypeAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
		spnChargeCategory.setAdapter(chargeTypeAdapter);

		edtKilometers = (EditText) findViewById(R.id.edtKilometers);
		edtAmount = (EditText) findViewById(R.id.edtAmount);
		edtComment = (EditText) findViewById(R.id.edtComment);
		edtQuantity = (EditText) findViewById(R.id.edtQuantity);
		
		Button btnSubmit = (Button) findViewById(R.id.btnAdd);
		btnSubmit.setOnClickListener(new OnClickListener() {
			public void onClick(View v) {			
				submit();
			}
		});
	}
	
	private void submit() {	
		// TODO evaluate values, notify errors
		
		OtokouCharge charge = new OtokouCharge(vehicles.get((int)spnVehicle.getSelectedItemId()).vehicleID, 
												vehicles.get((int)spnVehicle.getSelectedItemId()).vehicle, 
												(int)(spnChargeCategory.getSelectedItemId()+1), 
												""+datePicker.getYear()+"-"+(datePicker.getMonth()+1)+"-"+datePicker.getDayOfMonth(),
												Double.parseDouble(edtKilometers.getText().toString()),
												Double.parseDouble(edtAmount.getText().toString()),
												edtComment.getText().toString(),
												Double.parseDouble(edtQuantity.getText().toString()));
		
		
		OtokouAPI.setNewCharge(charge, otokouUser);
		
		finish();
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {	
		menu.add(Menu.NONE, MENU_ID_ADD_CHARGE, Menu.NONE, R.string.add_charge_menu_add).setIcon(R.drawable.menu_add);
		menu.add(Menu.NONE, MENU_ID_BACK, Menu.NONE, R.string.add_charge_menu_back).setIcon(R.drawable.exit);
		return super.onCreateOptionsMenu(menu);
	}
	
	@Override
	public boolean onPrepareOptionsMenu(Menu menu) {
		return super.onPrepareOptionsMenu(menu);
	}
	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		switch(item.getItemId()) {
			case MENU_ID_ADD_CHARGE:
				submit();
				break;
			case MENU_ID_BACK:
				finish();
				break;
		}
		return super.onOptionsItemSelected(item);
	}
}
