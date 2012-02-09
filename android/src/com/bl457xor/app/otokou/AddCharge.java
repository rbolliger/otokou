package com.bl457xor.app.otokou;

import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.StreamCorruptedException;
import java.util.ArrayList;

import android.app.Activity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.Spinner;

public class AddCharge extends Activity {
	// general constants
	public static final String OTOKOU_API_URL = "http://otokou.donax.ch/api";
	
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
        
		byte[] otokouUserBytes = getIntent().getExtras().getByteArray("user");
		int vehiclesNumber = getIntent().getExtras().getInt("vehiclesNumber");
		
		for (int i=0; i<vehiclesNumber; i++) {
			try {
				byte[] vehiclesBytes = getIntent().getExtras().getByteArray("vehicle_"+i);
				ByteArrayInputStream b = new ByteArrayInputStream(vehiclesBytes);
				ObjectInputStream o = new ObjectInputStream(b);
				vehicles.add((OtokouVehicle)o.readObject());
			} catch (StreamCorruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			} catch (ClassNotFoundException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}			

		try {
			ByteArrayInputStream b2 = new ByteArrayInputStream(otokouUserBytes);
			ObjectInputStream o2 = new ObjectInputStream(b2);
			otokouUser = (OtokouUser)o2.readObject();
		} catch (StreamCorruptedException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (ClassNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		initializeUI();
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
	
	private void addCharge(OtokouCharge charge, OtokouUser user) {
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
	
	private void submit() {	
		OtokouCharge charge = new OtokouCharge(vehicles.get((int)spnVehicle.getSelectedItemId()).vehicleID, 
												vehicles.get((int)spnVehicle.getSelectedItemId()).vehicle, 
												(int)(spnChargeCategory.getSelectedItemId()+1), 
												""+datePicker.getYear()+"-"+(datePicker.getMonth()+1)+"-"+datePicker.getDayOfMonth(),
												Double.parseDouble(edtKilometers.getText().toString()),
												Double.parseDouble(edtAmount.getText().toString()),
												edtComment.getText().toString(),
												Double.parseDouble(edtQuantity.getText().toString()));
		
		
		addCharge(charge, otokouUser);
		
		//finish();
	}
}
