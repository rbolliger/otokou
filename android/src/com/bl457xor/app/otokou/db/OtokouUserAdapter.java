package com.bl457xor.app.otokou.db;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

import com.bl457xor.app.otokou.OtokouUser;


/**
 * Adapter to the database containing the Users.<p>
 * 
 * databases structure:
 * -database: users
 * --table: users
 * ---field: id (primary key)
 * ---field: otokou_user_id (otokou database primary key)
 * ---field: first_name (first name of the user)
 * ---field: last_name (last name of the user)
 * ---field: username (username of the user)
 * ---field: apikey (apikey of the user)
 * ---field: vehicles_number (number of vehicles for this user)
 * ---field: last_update (last update of this user in the otokou database)
 * ---field: last_vehicles_update (last update of this user vehicles in the otokou database)
 * ---field: autoload (if user is loaded at start of application)
 * 
 * usage:<br>
 *  1. build an instance of this object using the constructor.<br>
 *  2. use the open() method to open a connection.<br>
 *  3. use the various method you need to perform actions on the database (see functionalities section).<br>
 *  4. use the close() method to close a connection.<p>
 *  functionalities:<br>
 *  1. Moves database connection<br>
 *  2. Moves database creation and upgrade<br>
 *  3. insert of a new User (insertUser() method)<br>
 *  4. delete a single User by ID (deleteUserById() method)<br>
 *  4. delete Users by apikey (deleteUsersByApikey() method)<br>
 *  5. update a single User by ID (updateUserById() method)<br>
 *  5. update a Users by apikey (updateUsersByApikey() method)<br>
 *  7. (getAllUsers() method)<br>
 *  8. (getUserById() method)<br>
 *  9. (getUsersByApikey() method)<br>
 *  10. (getMoveById() method)<br>
 *  
 *  @author Dave Bergomi
 *  @version 1
 */
public class OtokouUserAdapter {
	
	/** Version Database				**/ public static final int DB_VERSION = 1;
	/** Name of the Database			**/ public static final String DB_NAME = "users";
	/** Name of the Table				**/ public static final String TABLE_NAME = "users";
	/** Primary key field name			**/ public static final String COL_ID_NAME = "id";
	/** Primary key field type			**/ public static final String COL_ID_TYPE = "integer primary key autoincrement";
	/** column 1 field name				**/ public static final String COL_1_NAME = "otokou_user_id";
	/** column 1 field type				**/ public static final String COL_1_TYPE = "integer not null";
	/** column 1 field default value	**/ public static final long COL_1_DEFAULT = 0;
	/** column 2 field name				**/ public static final String COL_2_NAME = "first_name"; 
	/** column 2 field type				**/ public static final String COL_2_TYPE = "text not null";
	/** column 2 field default value	**/ public static final String COL_2_DEFAULT = "";
	/** column 3 field name				**/ public static final String COL_3_NAME = "last_name";
	/** column 3 field type				**/ public static final String COL_3_TYPE = "text not null";
	/** column 3 field default value	**/ public static final String COL_3_DEFAULT = "";
	/** column 4 field name				**/ public static final String COL_4_NAME = "username";
	/** column 4 field type				**/ public static final String COL_4_TYPE = "text not null";
	/** column 4 field default value	**/ public static final String COL_4_DEFAULT = "";
	/** column 5 field name				**/ public static final String COL_5_NAME = "apikey";
	/** column 5 field type				**/ public static final String COL_5_TYPE = "text not null";
	/** column 5 field default value	**/ public static final String COL_5_DEFAULT = "";	
	/** column 6 field name				**/ public static final String COL_6_NAME = "vehicles_number";
	/** column 6 field type				**/ public static final String COL_6_TYPE = "integer not null";
	/** column 6 field default value	**/ public static final long COL_6_DEFAULT = 0;	
	/** column 7 field name				**/ public static final String COL_7_NAME = "last_update";
	/** column 7 field type				**/ public static final String COL_7_TYPE = "text not null";
	/** column 7 field default value	**/ public static final String COL_7_DEFAULT = "";	
	/** column 8 field name				**/ public static final String COL_8_NAME = "last_vehicles_update";
	/** column 8 field type				**/ public static final String COL_8_TYPE = "text not null";
	/** column 8 field default value	**/ public static final String COL_8_DEFAULT = "";	
	/** column 9 field name				**/ public static final String COL_9_NAME = "autoload";
	/** column 9 field type				**/ public static final String COL_9_TYPE = "integer not null";
	/** column 9 field default value	**/ public static final long COL_9_DEFAULT = 0;
	/** column 9 field autoload on		**/ public static final long COL_9_AUTOLOAD_ON = 1;
	/** column 9 field autoload off		**/ public static final long COL_9_AUTOLOAD_OFF = COL_9_DEFAULT;
	private User dbHelper;
	private Context context;
	private SQLiteDatabase db;
	private boolean connectionOpen;
	
	/**
	 * Since Version 1<p>
	 * 
	 * OtokouUserAdapter constructor.<p>
	 * 
	 * @param context	application context 
	 */	
	public OtokouUserAdapter(Context _context){
		context = _context;
		dbHelper = new User(context);
		connectionOpen = false;
	}	

	/**
	 * Since Version 1<p>
	 * 
	 * Extension of the SQLiteOpenHelper used in this Adapter.
	 */	
	private class User extends SQLiteOpenHelper{

		public User(Context context) {
			super(context, OtokouUserAdapter.DB_NAME, null, OtokouUserAdapter.DB_VERSION);
		}

		@Override
		public void onCreate(SQLiteDatabase db) {
			db.execSQL("create table " + OtokouUserAdapter.TABLE_NAME + " (" + OtokouUserAdapter.COL_ID_NAME + " " + OtokouUserAdapter.COL_ID_TYPE + ", "
					+ OtokouUserAdapter.COL_1_NAME + " " + OtokouUserAdapter.COL_1_TYPE + ","
					+ OtokouUserAdapter.COL_2_NAME + " " + OtokouUserAdapter.COL_2_TYPE + ","
					+ OtokouUserAdapter.COL_3_NAME + " " + OtokouUserAdapter.COL_3_TYPE + ","
					+ OtokouUserAdapter.COL_4_NAME + " " + OtokouUserAdapter.COL_4_TYPE + ","
					+ OtokouUserAdapter.COL_5_NAME + " " + OtokouUserAdapter.COL_5_TYPE + ","
					+ OtokouUserAdapter.COL_6_NAME + " " + OtokouUserAdapter.COL_6_TYPE + ","
					+ OtokouUserAdapter.COL_7_NAME + " " + OtokouUserAdapter.COL_7_TYPE + ","
					+ OtokouUserAdapter.COL_8_NAME + " " + OtokouUserAdapter.COL_8_TYPE + ","
					+ OtokouUserAdapter.COL_9_NAME + " " + OtokouUserAdapter.COL_9_TYPE
					+ ");");
		}

		@Override
		public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
			if (oldVersion < newVersion) {
				db.execSQL("DROP TABLE IF EXISTS "+ OtokouUserAdapter.TABLE_NAME);
				onCreate(db);
				db.setVersion(newVersion);
			}
		}
	}

	/**
	 * Since Version 1<p>
	 * 
	 * Open the connection to the database.<p>
	 * note: used to start a connection to the database, need to be used before the other commands.<br>
	 * note: the close method need to be used to end the connection.
	 * 
	 * @return this (for chaining)
	 */
	public OtokouUserAdapter open(){
		db = dbHelper.getWritableDatabase();
		connectionOpen = true;
		if (db.getVersion() < DB_VERSION) upgradeDB();
		return this;
	}
	
	/**
	 * Since Version 1<p>
	 * 
	 * Upgrade the database to a new version.<p>
	 * note: only used when changing database structure, will delete all rows.<br>
	 * note: need a call to the open() method before a call to this method.
	 */	
	private void upgradeDB() {
		if (connectionOpen) dbHelper.onUpgrade(db, db.getVersion(), DB_VERSION);
	}
	
	/**
	 * Since Version 1<p>
	 * 
	 * Close the connection to the database.<p>
	 * note: need a call to the open() method before a call to this method.<br>
	 * note: the close method need to be used to end the connection.
	 */	
	public void close(){
		if (connectionOpen) {
			db.close();
			connectionOpen = false;
		}
	}

	/**
	 * Since Version 1<p>
	 * 
	 * Delete all row in the table.<p>
	 * note: need a call to the open() method before a call to this method.
	 * @return this (for chaining)
	 */	
	public OtokouUserAdapter deleteAllUsers(){
		if (connectionOpen) db.execSQL("DELETE FROM "+ OtokouUserAdapter.TABLE_NAME);
		return this;
	}

	/**
	 * Since Version 1<p>
	 * 
	 * Insert a row in the table.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param user	OtokouUser instance
	 * @return id of the inserted row or -1 in case of an error
	 */	
	public long insertUser(OtokouUser user) {
		if (!connectionOpen) return -1;
		
		ContentValues values = new ContentValues();
		values.put(OtokouUserAdapter.COL_1_NAME, user.getOtokouUserId());
		values.put(OtokouUserAdapter.COL_2_NAME, user.getFirstName());
		values.put(OtokouUserAdapter.COL_3_NAME, user.getLastName());
		values.put(OtokouUserAdapter.COL_4_NAME, user.getUsername());
		values.put(OtokouUserAdapter.COL_5_NAME, user.getApikey());
		values.put(OtokouUserAdapter.COL_6_NAME, user.getVehiclesNumber());
		values.put(OtokouUserAdapter.COL_7_NAME, user.getLastUpdate());
		values.put(OtokouUserAdapter.COL_8_NAME, user.getLastVehiclesUpdate());
		values.put(OtokouUserAdapter.COL_9_NAME, user.getAutoload());
		return db.insert(OtokouUserAdapter.TABLE_NAME, null, values);		
	}

	/**
	 * Since Version 1<p>
	 * 
	 * Delete a row in the table using the id as row identifier.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param id identifier of the row to delete
	 * @return true if one row has been deleted correctly.
	 */	
	public boolean deleteUserById(long id){
		if (!connectionOpen) return false;
		
		return db.delete(OtokouUserAdapter.TABLE_NAME, OtokouUserAdapter.COL_ID_NAME+"="+id, null) == 1;
	}
	
	/**
	 * Since Version 1<p>
	 * 
	 * Delete rows in the table using the apikey as row identifier.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param id identifier of the rows to delete
	 * @return number of row deleted, -1 if error
	 */	
	public int deleteUsersByApikey(String apikey){
		if (!connectionOpen) return -1;
		
		return db.delete(OtokouUserAdapter.TABLE_NAME, OtokouUserAdapter.COL_5_NAME+"='"+apikey+"'", null);
	}

	/**
	 * Since Version 1<p>
	 * 
	 * Update a row in the table identified by the its id.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param user	OtokouUser instance
	 * @return true if the update of 1 database row executed correctly, false otherwise
	 */		
	public boolean updateUser(OtokouUser user) {
		if (!connectionOpen) return false;
		
		ContentValues values = new ContentValues();
		values.put(OtokouUserAdapter.COL_1_NAME, user.getOtokouUserId());
		values.put(OtokouUserAdapter.COL_2_NAME, user.getFirstName());
		values.put(OtokouUserAdapter.COL_3_NAME, user.getLastName());
		values.put(OtokouUserAdapter.COL_4_NAME, user.getUsername());
		values.put(OtokouUserAdapter.COL_5_NAME, user.getApikey());
		values.put(OtokouUserAdapter.COL_6_NAME, user.getVehiclesNumber());
		values.put(OtokouUserAdapter.COL_7_NAME, user.getLastUpdate());
		values.put(OtokouUserAdapter.COL_8_NAME, user.getLastVehiclesUpdate());
		values.put(OtokouUserAdapter.COL_9_NAME, user.getAutoload());

		return db.update(OtokouUserAdapter.TABLE_NAME, values, OtokouUserAdapter.COL_ID_NAME+"="+user.getId() , null) == 1;
	}
	
	/**
	 * Since Version 1<p>
	 * 
	 * Update a row in the table identified by the its apikey.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param apikey	apikey of the row to update
	 * @param user	OtokouUser instance
	 * @return true if the update of 1 database row executed correctly, false otherwise
	 */		
	public boolean updateUsersByApikey(String apikey, OtokouUser user) {
		if (!connectionOpen) return false;
		
		ContentValues values = new ContentValues();
		values.put(OtokouUserAdapter.COL_1_NAME, user.getOtokouUserId());
		values.put(OtokouUserAdapter.COL_2_NAME, user.getFirstName());
		values.put(OtokouUserAdapter.COL_3_NAME, user.getLastName());
		values.put(OtokouUserAdapter.COL_4_NAME, user.getUsername());
		values.put(OtokouUserAdapter.COL_6_NAME, user.getVehiclesNumber());
		values.put(OtokouUserAdapter.COL_7_NAME, user.getLastUpdate());
		values.put(OtokouUserAdapter.COL_8_NAME, user.getLastVehiclesUpdate());		
		values.put(OtokouUserAdapter.COL_9_NAME, (user.getAutoload() ? OtokouUserAdapter.COL_9_AUTOLOAD_ON :  OtokouUserAdapter.COL_9_AUTOLOAD_OFF) );

		return db.update(OtokouUserAdapter.TABLE_NAME, values, OtokouUserAdapter.COL_5_NAME+"='"+apikey+"'" , null) == 1;
	}

	/**
	 * Since Version 1<p>
	 * 
	 * Get a cursor containing all the Table rows and columns.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @return cursor object containing the table data. null object will be returned in case of errors.
	 */		
	public Cursor getAllUsers(){
		if (!connectionOpen) return null;
		
		return db.query(OtokouUserAdapter.TABLE_NAME, 
				new String[]{
					OtokouUserAdapter.COL_ID_NAME,
					OtokouUserAdapter.COL_1_NAME,
					OtokouUserAdapter.COL_2_NAME,
					OtokouUserAdapter.COL_3_NAME,
					OtokouUserAdapter.COL_4_NAME,
					OtokouUserAdapter.COL_5_NAME,
					OtokouUserAdapter.COL_6_NAME,
					OtokouUserAdapter.COL_7_NAME,
					OtokouUserAdapter.COL_8_NAME,
					OtokouUserAdapter.COL_9_NAME,
		 		},
		 		null,null, null, null, 
		 OtokouUserAdapter.COL_1_NAME);
	}

	/**
	 * Since Version 1<p>
	 * 
	 * Get a row identified by its id.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param id	id of the row to update
	 * @return cursor object containing the row data. null object will be returned in case of errors.
	 */		
	public Cursor getUserById(long id){
		if (!connectionOpen) return null;
		
		return db.query(OtokouUserAdapter.TABLE_NAME, 
				null, 
				""+OtokouUserAdapter.COL_ID_NAME+ "="+id, 
				null,
				null, 
				null, 
				null);
	}
	
	/**
	 * Since Version 1<p>
	 * 
	 * Get a row identified by its otokou user database id.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param otokou_user_id	otokou user database id of the row to get
	 * @return cursor object containing the row data. null object will be returned in case of errors.
	 */		
	public Cursor getUserByOtokouUserId(long otokou_user_id){
		if (!connectionOpen) return null;
		
		return db.query(OtokouUserAdapter.TABLE_NAME, 
				null, 
				""+OtokouUserAdapter.COL_1_NAME+ "="+otokou_user_id, 
				null,
				null, 
				null, 
				null);
	}
	
	/**
	 * Since Version 1<p>
	 * 
	 * Get rows identified by its apikey.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param apikey	apikey of the row to update
	 * @return cursor object containing the row data. null object will be returned in case of errors.
	 */		
	public Cursor getUsersByApikey(String apikey){
		if (!connectionOpen) return null;
		
		return db.query(OtokouUserAdapter.TABLE_NAME, 
				null, 
				""+OtokouUserAdapter.COL_5_NAME+ "='"+apikey+"'", 
				null,
				null, 
				null, 
				null);
	}
}
