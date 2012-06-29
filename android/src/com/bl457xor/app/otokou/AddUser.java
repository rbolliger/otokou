package com.bl457xor.app.otokou;

import android.app.ProgressDialog;
import android.database.Cursor;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;

import com.bl457xor.app.otokou.components.OtokouUser;
import com.bl457xor.app.otokou.db.OtokouUserAdapter;

public class AddUser extends OnlineActivity implements OnClickListener, Runnable {
	// return messages constants
	public static final int RETURN_RESULT_BACK = 2001;
	public static final int RETURN_RESULT_OFFLINE = 2002; 
	public static final int RETURN_RESULT_USER_ADDED = 2003;
	public static final int RETURN_RESULT_UNEXPECTED = 2100; 
	
	// onOptionsItemSelected menu ids constants
	private static final int MENU_ID_ADD = 10001;
	private static final int MENU_ID_BACK = 10002;
	private static final int MENU_ID_CLEAR = 10003;
	
	// run messages constants
	private static final int RUN_END = 0;
	private static final int RUN_MSG_LOADING_USER = 10;
	private static final int RUN_MSG_LOADING_OK = 12;
	private static final int RUN_ERROR_NOT_CONNECTED = 100;
	private static final int RUN_ERROR_USER = 101;
	private static final int RUN_ERROR_USER_LOGIN = 102;
	
	// global variables initialization
	private EditText edtAUUsername;
	private EditText edtAUAPikey;
	private TextView txtAUErrorMessage;
	private TextView txtAUErrorUsername;
	private TextView txtAUErrorApikey;
	private ProgressDialog progressDialog;
	
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.add_user);
        
        setResult(RETURN_RESULT_UNEXPECTED, null);
        
		initializeUI();
    }
    
	@Override
	protected void onResume() {
		super.onResume();
		
		if (!isOnline()) offline();
	}

	@Override
	public void onBackPressed() {
		setResult(RETURN_RESULT_BACK, null);
		super.onBackPressed();
	}
	
	private void initializeUI() {
		((Button)findViewById(R.id.btnAddUserAdd)).setOnClickListener(this);
		
		edtAUUsername = (EditText)findViewById(R.id.edtAddUserUsername);	
		edtAUAPikey = (EditText)findViewById(R.id.edtAddUserApikey);
		
		txtAUErrorMessage = (TextView)findViewById(R.id.txtAddUserErrorMessage);
		txtAUErrorUsername = (TextView)findViewById(R.id.txtAddUserErrorUsername);
		txtAUErrorApikey = (TextView)findViewById(R.id.txtAddUserErrorApikey);
	}

	@Override
	public void onClick(View v) {
		switch (v.getId()) {
		case R.id.btnAddUserAdd:
			submit();
			break;
		}	
	}
	
	private void submit() {	
		//check if data in form is vlid
		if (formIsValid(edtAUUsername.getText().toString(),edtAUAPikey.getText().toString())) {
			if (isOnline()) {
				//get user data
				progressDialog = ProgressDialog.show(this,getString(R.string.add_user_dialog_title), getString(R.string.add_user_dialog_message_start), true, false);
				
		    	Thread thread = new Thread(this);
		    	thread.start();
			}
			else {
				//exit
				offline();
			}
		}
	}
	
	private void offline() {
		setResult(RETURN_RESULT_OFFLINE, null);
		finish();
	}
	
	private void clear() {
		// delete data in form fields
		edtAUUsername.setText("");
		edtAUAPikey.setText("");
	}
	
	@Override
	public void run() {
		if (isOnline()) {
			handler.sendEmptyMessage(RUN_MSG_LOADING_USER);
			OtokouUser otokouUser = OtokouAPI.getUserData(edtAUUsername.getText().toString(), edtAUAPikey.getText().toString());
			if (otokouUser.isValid()) {
				handler.sendEmptyMessage(RUN_MSG_LOADING_OK);
				// save user data to database
				OtokouUserAdapter OUAdb = new OtokouUserAdapter(getApplicationContext()).open();
				OUAdb.insertUserWithoutVehicles(otokouUser);
				OUAdb.close();
				handler.sendEmptyMessage(RUN_END);
			}
			else {
				if (otokouUser.getErrorCode() == OtokouException.CODE_RESPONSE_GET_USER_INCORRECT_LOGIN) {
					handler.sendEmptyMessage(RUN_ERROR_USER_LOGIN);
				}
				else {
					handler.sendEmptyMessage(RUN_ERROR_USER);
				}
			}
		}
		else {
			handler.sendEmptyMessage(RUN_ERROR_NOT_CONNECTED);
		}		
	}
	
	private Handler handler = new Handler() {
		@Override
		public void handleMessage(Message msg) {
			switch (msg.what) {
			case RUN_END:
				progressDialog.dismiss();
				setResult(RETURN_RESULT_USER_ADDED, null);
				finish();
				break;
			case RUN_MSG_LOADING_USER:
				progressDialog.setMessage(getString(R.string.add_user_dialog_message_loading_user));
				break;
			case RUN_MSG_LOADING_OK:
				progressDialog.setMessage(getString(R.string.add_user_dialog_message_ok));			
				break;
			case RUN_ERROR_USER:
				progressDialog.dismiss();
				txtAUErrorMessage.setText(R.string.add_user_error_user);	
				break;
			case RUN_ERROR_USER_LOGIN:
				progressDialog.dismiss();
				txtAUErrorMessage.setText(R.string.add_user_error_user_login);	
				break;
			case RUN_ERROR_NOT_CONNECTED:
				progressDialog.dismiss();
				offline();
				break;	
			}
		}
	};

	private boolean formIsValid(String username, String apikey) {
		// TODO check apikey format
		
		// check form data
		txtAUErrorUsername.setText("");
		txtAUErrorApikey.setText("");
		
		boolean result = true;
		
		if (username.contentEquals("")) {
			txtAUErrorUsername.setText("  "+getString(R.string.add_user_etxt_username_empty_field));
			result = false;
		}
		else {
			// check if user already exists
			OtokouUserAdapter OUAdb = new OtokouUserAdapter(getApplicationContext()).open();		
			Cursor userCursor = OUAdb.getUserByUsername(username);
			if (userCursor.getCount() > 0) {
				txtAUErrorUsername.setText("  "+getString(R.string.add_user_etxt_username_duplicate));
				result = false;
			}
			userCursor.close();
		}
		
		if (apikey.contentEquals("")) {
			txtAUErrorApikey.setText("  "+getString(R.string.add_user_etxt_apikey_empty_field));
			result = false;
		}	
		
		return result;
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {	
		menu.add(Menu.NONE, MENU_ID_ADD, Menu.NONE, R.string.add_user_menu_add);
		menu.add(Menu.NONE, MENU_ID_BACK, Menu.NONE, R.string.add_user_menu_back);
		menu.add(Menu.NONE, MENU_ID_CLEAR, Menu.NONE, R.string.add_user_menu_clear);
		return super.onCreateOptionsMenu(menu);
	}
	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		switch(item.getItemId()) {
			case MENU_ID_ADD:
				submit();
				break;
			case MENU_ID_BACK:
				setResult(RETURN_RESULT_BACK, null);
				finish();
				break;
			case MENU_ID_CLEAR:
				clear();
				break;
		}
		return super.onOptionsItemSelected(item);
	}
}
