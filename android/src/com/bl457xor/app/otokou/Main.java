package com.bl457xor.app.otokou;

import java.util.ArrayList;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.database.Cursor;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.BaseAdapter;
import android.widget.Button;
import android.widget.CheckBox;
import android.widget.CompoundButton;
import android.widget.CompoundButton.OnCheckedChangeListener;
import android.widget.Filter;
import android.widget.Filterable;
import android.widget.ImageButton;
import android.widget.TextView;
import android.widget.Toast;

import com.bl457xor.app.otokou.components.OtokouUser;
import com.bl457xor.app.otokou.db.OtokouUserAdapter;


// TODO
// other (need more finalized otokou site):
//  - apikey (only server side)
//  - final site URL (need server side)
//  - distribution on market
// next versions:
//  - check if online status change live
//  - find better way to choose when synchronize with data on website
//  - multilingue
//  - activity return codes,... -> send debug information to server
//  - more comments on code?


public class Main extends OtokouListActivity implements OnClickListener {
	// menu constants
	private static final int MENU_ID_ADD_USER = 10001;
	private static final int MENU_ID_HELP = 10101;
	private static final int MENU_ID_EXIT = 10201;
	
	// global variables initialization
	private MainAdapter listAdapter;
	private ArrayList<OtokouUser> users;
	private boolean autoload = true;
	private boolean isOnline = false;
	private TextView txtMessage;
	
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		
		requestWindowFeature(Window.FEATURE_NO_TITLE);
		setContentView(R.layout.main);
		
		initializeUI();
	}
	
	@Override
	protected void onResume() {
		super.onResume();
		
		if (isOnline()) isOnline = true;
		else isOnline = false;
		
		loadUsers();
		
		updateUI();
		
		// create a new adapter
		if (listAdapter != null) {
			listAdapter = null;
		}
		listAdapter = new MainAdapter(this);
		setListAdapter(listAdapter);	
	}

	private void initializeUI() {
		((Button)findViewById(R.id.btnMainAddUser)).setOnClickListener(this);
		((ImageButton)findViewById(R.id.imbMainHelp)).setOnClickListener(this);
		txtMessage = (TextView)findViewById(R.id.txtMainMessage);
	}
	
	private void updateUI() {
		// set warning message if not connected
		if (isOnline) {
			txtMessage.setText(R.string.main_txt_online);
		}
		else {
			txtMessage.setText(R.string.main_txt_offline);
		}
	}

	private void loadUsers() {
		if (users != null) {
			users = null;
		}
		users = new ArrayList<OtokouUser>();
		OtokouUserAdapter OUAdb = new OtokouUserAdapter(getApplicationContext()).open();
		Cursor c = OUAdb.getAllUsers();
		
		if (c.getCount() > 0) {
			c.moveToFirst();
			do {				
				users.add(new OtokouUser(c));
			} while (c.moveToNext());
		}	
		
		c.close();
		OUAdb.close();
		
		if (autoload && users.size() >= 1) {
			for (OtokouUser user : users) {
				if (user.getAutoload()) {
					launchUserActivity(user.getId());
				}
			}
		}
	}
	
	public class MainAdapter extends BaseAdapter implements Filterable {
		private LayoutInflater mInflater;
		private Context context;

		public MainAdapter(Context context) {
			// Cache the LayoutInflate.
			mInflater = LayoutInflater.from(context);
			this.context = context;
		}

		public View getView(final int position, View convertView, ViewGroup parent) {
			ViewHolder holder;

			convertView = mInflater.inflate(R.layout.main_list_user_item, null);

			// store listview elements
			holder = new ViewHolder();
			holder.userTxt = (TextView) convertView.findViewById(R.id.userTxt);
			holder.userBtnDelete = (Button) convertView.findViewById(R.id.userBtnDelete);
			holder.userChb = (CheckBox) convertView.findViewById(R.id.userChb);

			// check automatic login box if needed
			if (((OtokouUser)getItem(position)).getAutoload()) {
				holder.userChb.setChecked(true);
			}
			else {
				holder.userChb.setChecked(false);
			}

			// set listener for list item click
			convertView.setOnClickListener(new OnClickListener() {
				private long user_id = ((OtokouUser)getItem(position)).getId();

				@Override
				public void onClick(View v) {
					launchUserActivity(user_id);  
				}
			});

			// set listener for delete user button
			holder.userBtnDelete.setOnClickListener(new OnClickListener() {
				private long user_id = ((OtokouUser)getItem(position)).getId();

				@Override
				public void onClick(View v) {
					AlertDialog.Builder builder = new AlertDialog.Builder(context);
				    builder.setMessage(R.string.main_dialog_text_unlink)
				           .setCancelable(false)
				           .setPositiveButton(R.string.main_dialog_yes, new DialogInterface.OnClickListener() {
				               public void onClick(DialogInterface dialog, int id) {
									OtokouUserAdapter OUAdb = new OtokouUserAdapter(getApplicationContext()).open();
									OUAdb.deleteUserById(user_id);
									OUAdb.close();
									loadUsers();
									dialog.cancel();
									listAdapter.notifyDataSetChanged();
				               }
				           })
				           .setNegativeButton(R.string.main_dialog_no, new DialogInterface.OnClickListener() {
				               public void onClick(DialogInterface dialog, int id) {
				                    dialog.cancel();
				               }
				           });
				    AlertDialog alert = builder.create();
				    alert.show();
				}
			});

			// set listener for check box
			holder.userChb.setOnCheckedChangeListener(new OnCheckedChangeListener() {
				private int pos = position;

				@Override
				public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
					if (isChecked) {
						for (OtokouUser user : users) {
							if (((OtokouUser)getItem(pos)).getId() == user.getId()) {
								user.setAutoload(true);
								OtokouUserAdapter OUAdb = new OtokouUserAdapter(getApplicationContext()).open();
								OUAdb.updateUser(user);
								OUAdb.close();
							}
							else if (user.getAutoload()) {						
								user.setAutoload(false);
								OtokouUserAdapter OUAdb = new OtokouUserAdapter(getApplicationContext()).open();
								OUAdb.updateUser(user);
								OUAdb.close();
							}						
						}
						listAdapter.notifyDataSetChanged();
					}
					else {			
						for (OtokouUser user : users) {
							if (user.getAutoload()) {
								user.setAutoload(false);
								OtokouUserAdapter OUAdb = new OtokouUserAdapter(getApplicationContext()).open();
								OUAdb.updateUser(user);
								OUAdb.close();
							}
						}
					}				
				}
			});

			convertView.setTag(holder);

			holder.userTxt.setText(((OtokouUser)getItem(position)).toString());
			
			return convertView;
		}

		public class ViewHolder {
			TextView userTxt;
			Button userBtnDelete;
			CheckBox userChb;
		}

		@Override
		public Filter getFilter() {
			return null;
		}

		@Override
		public long getItemId(int position) {
			return 0;
		}

		@Override
		public int getCount() {
			return users.size();
		}

		@Override
		public Object getItem(int position) {
			return users.get(position);
		}
	}

	@Override
	public void onClick(View v) {
		switch (v.getId()) {
		case R.id.btnMainAddUser:
			launchAddUserActivity();
			break;
		case R.id.imbMainHelp:
			HelpAlertDialog(getString(R.string.main_dialog_help_message));
			break;
		}
	}

	private void launchAddUserActivity() {
		if (isOnline()) {
			Intent i = new Intent(Main.this, AddUser.class);
			startActivityForResult(i,0);
		}
		else {
			Toast.makeText(getApplicationContext(), R.string.main_toast_offline_add_user,Toast.LENGTH_SHORT).show();
		}
	}
	
	private void launchUserActivity(long usedId) {
		Intent i = new Intent(Main.this, User.class);		
		Bundle extras = new Bundle();		
		extras.putLong(User.PARAMETER_USER_ID, usedId);	
		i.putExtras(extras);
		startActivityForResult(i,0);
	}
	
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		// TODO, if needed, handle returns from called activities
		switch (resultCode) {
			case AddUser.RETURN_RESULT_BACK:
			case AddUser.RETURN_RESULT_OFFLINE:
			case AddUser.RETURN_RESULT_USER_ADDED:
			case AddUser.RETURN_RESULT_UNEXPECTED:
			case User.RETURN_RESULT_BACK:
			case User.RETURN_RESULT_USER_NOT_FOUND:
			case User.RETURN_RESULT_UNEXPECTED:
			case User.RETURN_RESULT_WRONG_EXTRAS:
				break;
				
		}	
		autoload = false;
	}
	
	@Override
	public boolean onCreateOptionsMenu(Menu menu) {	
		menu.add(Menu.NONE, MENU_ID_ADD_USER, Menu.NONE, R.string.main_menu_add_user);
		menu.add(Menu.NONE, MENU_ID_HELP, Menu.NONE, R.string.main_menu_help);
		menu.add(Menu.NONE, MENU_ID_EXIT, Menu.NONE, R.string.main_menu_exit);
		return super.onCreateOptionsMenu(menu);
	}
	

	
	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		switch(item.getItemId()) {
			case MENU_ID_ADD_USER:
				launchAddUserActivity();
				break;
			case MENU_ID_HELP:
				HelpAlertDialog(getString(R.string.main_dialog_help_message));
				break;
			case MENU_ID_EXIT:
				finish();
				break;
		}
		return super.onOptionsItemSelected(item);
	}
}
