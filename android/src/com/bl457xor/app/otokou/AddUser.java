package com.bl457xor.app.otokou;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

import com.bl457xor.app.otokou.db.OtokouUserAdapter;

public class AddUser extends Activity implements OnClickListener, Runnable {
	// run messages constants
	private static final int RUN_END = 0;
	private static final int RUN_MSG_LOADING_USER = 10;
	private static final int RUN_MSG_LOADING_OK = 12;
	private static final int RUN_ERROR_API_KEY = 101;
	private static final int RUN_ERROR_USER = 102;
	
	// global variables initialization
	private EditText edtAUUsername;
	private EditText edtAUAPikey;
	private TextView txtAUErrorMessage;
	private ProgressDialog progressDialog;
	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.add_user);
        
		initializeUI();
    }

	private void initializeUI() {
		((Button)findViewById(R.id.btnAddUserAdd)).setOnClickListener(this);
		((Button)findViewById(R.id.btnAddUserBack)).setOnClickListener(this);
		
		edtAUUsername = (EditText)findViewById(R.id.edtAddUserUsername);
		
		edtAUAPikey = (EditText)findViewById(R.id.edtAddUserApikey);
		
		txtAUErrorMessage = (TextView)findViewById(R.id.txtAddUserErrorMessage);
	}

	@Override
	public void onClick(View v) {
		switch (v.getId()) {
		case R.id.btnAddUserAdd:
			submit();
			break;
		case R.id.btnAddUserBack:
			back();
			break;
		}	
	}
	
	private void submit() {		
		if (formIsValid(edtAUUsername.getText().toString(),edtAUAPikey.getText().toString())) {
			
			// TODO check otokou and add user to database

			if (isOnline()) {
				progressDialog = ProgressDialog.show(this,getString(R.string.main_dialog_title), getString(R.string.main_dialog_message_start), true, false);
				
		    	Thread thread = new Thread(this);
		    	thread.start();
			}
			else {
				// TODO if not online behavior
				finish();
			}
		}
	}
	
	private void back() {
		finish();
	}
	
	@Override
	public void run() {
		// TODO Auto-generated method stub
		OtokouUser otokouUser = OtokouAPI.getUserData(edtAUUsername.getText().toString(), edtAUAPikey.getText().toString());
		if (otokouUser != null) {
			handler.sendEmptyMessage(RUN_MSG_LOADING_OK);
			// save user data to database
			OtokouUserAdapter OUAdb = new OtokouUserAdapter(getApplicationContext()).open();
			OUAdb.insertUser(otokouUser);
			OUAdb.close();
		}
		else {
			handler.sendEmptyMessage(RUN_ERROR_USER);
		}
		handler.sendEmptyMessage(RUN_END);
	}
	
	private Handler handler = new Handler() {
		@Override
		public void handleMessage(Message msg) {
			switch (msg.what) {
			case RUN_END:
				progressDialog.dismiss();
				finish();
				break;
			case RUN_MSG_LOADING_USER:	
				progressDialog.setMessage(getString(R.string.add_user_dialog_message_loading_user));
				break;
			case RUN_MSG_LOADING_OK:
				progressDialog.setMessage(getString(R.string.add_user_dialog_message_ok));			
				break;
			}
		}
	};

	private boolean formIsValid(String username, String apikey) {
		// TODO check apikey format
		
		String message = "";
		boolean result = true;
		
		if (username.contentEquals("")) {
			message += "empty username  ";
			result = false;
		}
		
		if (apikey.contentEquals("")) {
			message += "empty apikey";
			result = false;
		}
		
		txtAUErrorMessage.setText(message);	
		return result;
	}
	
	private boolean isOnline() {
	    ConnectivityManager cm = (ConnectivityManager)getSystemService(Context.CONNECTIVITY_SERVICE);
	    NetworkInfo netInfo = cm.getActiveNetworkInfo();
	    if (netInfo != null && netInfo.isConnectedOrConnecting()) {
	        return true;
	    }
	    return false;
	}
}
