package com.bl457xor.app.otokou;

import java.util.ArrayList;

import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.os.Bundle;
import android.view.LayoutInflater;
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
import android.widget.TextView;

import com.bl457xor.app.otokou.components.OtokouUser;
import com.bl457xor.app.otokou.db.OtokouUserAdapter;


// TODO
// offline behavior 
//  - main -> button addUser off if offline, notify
//  - adduser -> no access, can't add user
//  - user -> notify
//  - addcharge -> save on database
// layouts
// ...


public class Main extends OnlineListActivity implements OnClickListener {
	// global variables initialization
	private EfficientAdapter adap;
	private ArrayList<OtokouUser> users;
	private boolean autoload = true;
	private boolean isOnline = false;
	private Button btnAddUser;
	
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
		
		if (adap != null) {
			adap = null;
		}
		adap = new EfficientAdapter(this);
		setListAdapter(adap);	
	}

	private void initializeUI() {
		btnAddUser = (Button)findViewById(R.id.btnAddUser);
		btnAddUser.setOnClickListener(this);	
	}
	
	private void updateUI() {
		if (isOnline) btnAddUser.setClickable(true);
		else btnAddUser.setClickable(false);
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
	
	public class EfficientAdapter extends BaseAdapter implements Filterable {
		private LayoutInflater mInflater;
		//private Context context;

		public EfficientAdapter(Context context) {
			// Cache the LayoutInflate to avoid asking for a new one each time.
			mInflater = LayoutInflater.from(context);
			//this.context = context;
		}

		/**
		 * Make a view to hold each row.
		 * 
		 * @see android.widget.ListAdapter#getView(int, android.view.View,
		 *      android.view.ViewGroup)
		 */
		public View getView(final int position, View convertView, ViewGroup parent) {
			// A ViewHolder keeps references to children views to avoid
			// unneccessary calls
			// to findViewById() on each row.
			ViewHolder holder;
			/*for (int i = 0; i < holder.length; i++) {
				holder[i] = new ViewHolder();
			}*/

			// When convertView is not null, we can reuse it directly, there is
			// no need
			// to reinflate it. We only inflate a new View when the convertView
			// supplied
			// by ListView is null.
			//if (convertView == null) {
				convertView = mInflater.inflate(R.layout.list_user_item, null);

				// Creates a ViewHolder and store references to the two children
				// views
				// we want to bind data to.
				holder = new ViewHolder();
				holder.userTxt = (TextView) convertView.findViewById(R.id.userTxt);
				holder.userBtnDelete = (Button) convertView.findViewById(R.id.userBtnDelete);
				holder.userChb = (CheckBox) convertView.findViewById(R.id.userChb);
				
				if (((OtokouUser)getItem(position)).getAutoload()) {
					holder.userChb.setChecked(true);
				}
				else {
					holder.userChb.setChecked(false);
				}
				
				convertView.setOnClickListener(new OnClickListener() {
					private long user_id = ((OtokouUser)getItem(position)).getId();

					@Override
					public void onClick(View v) {
						launchUserActivity(user_id);  
					}
				});

				holder.userBtnDelete.setOnClickListener(new OnClickListener() {
					private long user_id = ((OtokouUser)getItem(position)).getId();

					@Override
					public void onClick(View v) {
						OtokouUserAdapter OUAdb = new OtokouUserAdapter(getApplicationContext()).open();
						OUAdb.deleteUserById(user_id);
						OUAdb.close();
						loadUsers();
						adap.notifyDataSetChanged();
					}
				});
				
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
							adap.notifyDataSetChanged();
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
			/*} else {
				// Get the ViewHolder back to get fast access to the TextView
				// and the ImageView.
				holder = (ViewHolder)convertView.getTag();
				
				if (!((OtokouUser)getItem(position)).getAutoload()) {
					holder.userChb.setChecked(false);
				}
			}*/

			// Bind the data efficiently with the holder.
			holder.userTxt.setText(((OtokouUser)getItem(position)).getFirstName() + " " +  ((OtokouUser)getItem(position)).getLastName());
			
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
		case R.id.btnAddUser:
			launchAddUserActivity();
			break;
		}
	}

	private void launchAddUserActivity() {
		Intent i = new Intent(Main.this, AddUser.class);
		startActivity(i);
	}
	
	private void launchUserActivity(long usedId) {
		Intent i = new Intent(Main.this, User.class);		
		Bundle extras = new Bundle();		
		extras.putLong("user_id", usedId);	
		i.putExtras(extras);
		startActivityForResult(i,0);
	}
	
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		// TODO handle errors on result return
		autoload = false;
	}
}
