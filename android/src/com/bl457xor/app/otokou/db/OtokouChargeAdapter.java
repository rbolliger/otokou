package com.bl457xor.app.otokou.db;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

import com.bl457xor.app.otokou.components.OtokouCharge;
import com.bl457xor.app.otokou.components.OtokouUser;
import com.bl457xor.app.otokou.components.OtokouVehicle;

/**
 * Adapter to the database containing the Charges.<p>
 * 
 * databases structure:
 * -database: charges
 * --table: charges
 * ---field: id (primary key)
 * ---field: user_id (foreign key user id)
 * ---field: vehicle_id (foreign key vehicle id)
 * ---field: category_id (category id)
 * ---field: sent (flag, content has been sent to Otokou site)
 * ---field: date (data of the charge)
 * ---field: kilometers (kilomenters of the charge)
 * ---field: amount (amount spent for the charge)
 * ---field: comment (comment of the charge)
 * ---field: quantity (quantity of the charge (optional))
 * 
 * usage:<br>
 *  1. build an instance of this object using the constructor.<br>
 *  2. use the open() method to open a connection.<br>
 *  3. use the various method you need to perform actions on the database (see functionalities section).<br>
 *  4. use the close() method to close a connection.<p>
 *  functionalities:<br>
 *  1. Charges database connection<br>
 *  2. Charges database creation and upgrade<br>
 *  3. delete all Charges (deleteAllCharges() method)<br>
 *  4. insert of a new Charge (insertCharge() method)<br>
 *  5. delete a single Charge by id (deleteChargeById() method)<br>
 *  6. delete Charges by user_id (deleteChargeByUserId() method)<br>
 *  7. delete Charges by vehicle_id (deleteChargeByVehicleId() method)<br>
 *  8. update a single Charge (updateChargeById() method)<br>
 *  9. (getAllCharges() method)<br>
 *  10. (getChargeById() method)<br>
 *  11. (getChargesByUserId() method)<br>
 *  12. (getChargesByVehicleId() method)<br>
 *  
 *  @author Dave Bergomi
 *  @version 1
 */
public class OtokouChargeAdapter {
	
	/** Version Database				**/ public static final int DB_VERSION = 1;
	/** Name of the Database			**/ public static final String DB_NAME = "charges";
	/** Name of the Table				**/ public static final String TABLE_NAME = "charges";
	/** Primary key field name			**/ public static final String COL_ID_NAME = "id";
	/** Primary key field type			**/ public static final String COL_ID_TYPE = "integer primary key autoincrement";
	/** column 1 field name				**/ public static final String COL_1_NAME = "user_id"; 
	/** column 1 field type				**/ public static final String COL_1_TYPE = "integer not null";
	/** column 1 field default value	**/ public static final long COL_1_DEFAULT = 0;
	/** column 2 field name				**/ public static final String COL_2_NAME = "vehicle_id"; 
	/** column 2 field type				**/ public static final String COL_2_TYPE = "integer not null";
	/** column 2 field default value	**/ public static final long COL_2_DEFAULT = 0;
	/** column 3 field name				**/ public static final String COL_3_NAME = "category_id";
	/** column 3 field type				**/ public static final String COL_3_TYPE = "integer not null";
	/** column 3 field default value	**/ public static final long  COL_3_DEFAULT = 0;
	/** column 4 field name				**/ public static final String COL_4_NAME = "sent"; 
	/** column 4 field type				**/ public static final String COL_4_TYPE = "integer not null";
	/** column 4 field sent value		**/ public static final long COL_4_SENT_VALUE = 1;
	/** column 4 field not sent value	**/ public static final long COL_4_NOT_SENT_VALUE = 0;
	/** column 4 field default value	**/ public static final long COL_4_DEFAULT = COL_4_NOT_SENT_VALUE;
	/** column 5 field name				**/ public static final String COL_5_NAME = "date";
	/** column 5 field type				**/ public static final String COL_5_TYPE = "text not null";
	/** column 5 field default value	**/ public static final String COL_5_DEFAULT = "";	
	/** column 6 field name				**/ public static final String COL_6_NAME = "kilometers";
	/** column 6 field type				**/ public static final String COL_6_TYPE = "real not null";
	/** column 6 field default value	**/ public static final double COL_6_DEFAULT = 0;	
	/** column 7 field name				**/ public static final String COL_7_NAME = "amount";
	/** column 7 field type				**/ public static final String COL_7_TYPE = "real not null";
	/** column 7 field default value	**/ public static final double COL_7_DEFAULT = 0;
	/** column 8 field name				**/ public static final String COL_8_NAME = "comment";
	/** column 8 field type				**/ public static final String COL_8_TYPE = "text not null";
	/** column 8 field default value	**/ public static final String COL_8_DEFAULT = "";
	/** column 9 field name				**/ public static final String COL_9_NAME = "quantity";
	/** column 9 field type				**/ public static final String COL_9_TYPE = "real not null";
	/** column 9 field default value	**/ public static final double COL_9_DEFAULT = 0;
	
	
	private Charge dbHelper;
	private Context context;
	private SQLiteDatabase db;
	private boolean connectionOpen;
	
	/**
	 * Since Version 1<p>
	 * 
	 * OtokouChargeAdapter constructor.<p>
	 * 
	 * @param context	application context 
	 */	
	public OtokouChargeAdapter(Context _context){
		context = _context;
		dbHelper = new Charge(context);
		connectionOpen = false;
	}	

	/**
	 * Since Version 1<p>
	 * 
	 * Extension of the SQLiteOpenHelper used in this Adapter.
	 */	
	private class Charge extends SQLiteOpenHelper{

		public Charge(Context context) {
			super(context, OtokouChargeAdapter.DB_NAME, null, OtokouChargeAdapter.DB_VERSION);
		}

		@Override
		public void onCreate(SQLiteDatabase db) {
			db.execSQL("create table " + OtokouChargeAdapter.TABLE_NAME + " (" + OtokouChargeAdapter.COL_ID_NAME + " " + OtokouChargeAdapter.COL_ID_TYPE + ", "
					+ OtokouChargeAdapter.COL_1_NAME + " " + OtokouChargeAdapter.COL_1_TYPE + ","
					+ OtokouChargeAdapter.COL_2_NAME + " " + OtokouChargeAdapter.COL_2_TYPE + ","
					+ OtokouChargeAdapter.COL_3_NAME + " " + OtokouChargeAdapter.COL_3_TYPE + ","
					+ OtokouChargeAdapter.COL_4_NAME + " " + OtokouChargeAdapter.COL_4_TYPE + ","
					+ OtokouChargeAdapter.COL_5_NAME + " " + OtokouChargeAdapter.COL_5_TYPE + ","
					+ OtokouChargeAdapter.COL_6_NAME + " " + OtokouChargeAdapter.COL_6_TYPE + ","
					+ OtokouChargeAdapter.COL_7_NAME + " " + OtokouChargeAdapter.COL_7_TYPE + ","
					+ OtokouChargeAdapter.COL_8_NAME + " " + OtokouChargeAdapter.COL_8_TYPE + ","
					+ OtokouChargeAdapter.COL_9_NAME + " " + OtokouChargeAdapter.COL_9_TYPE
					+ ");");
		}

		@Override
		public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
			if (oldVersion < newVersion) {
				db.execSQL("DROP TABLE IF EXISTS "+ OtokouChargeAdapter.TABLE_NAME);
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
	public OtokouChargeAdapter open(){
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
	public OtokouChargeAdapter deleteAllCharges(){
		if (connectionOpen) db.execSQL("DELETE FROM "+ OtokouChargeAdapter.TABLE_NAME);
		return this;
	}

	/**
	 * Since Version 1<p>
	 * 
	 * Insert a row in the table.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param charge	OtokouCharge instance
	 * @param user	 OtokouUser  instance (user owning the charge)
	 * @param vehicle   Vehicle  instance (vehicle owning the charge)
	 * @param sent	 sent field value
	 * @return id of the inserted row or -1 in case of an error
	 */	
	public long insertCharge(OtokouCharge charge, OtokouUser user, OtokouVehicle vehicle, long sent) {
		if (!connectionOpen) return -1;
		
		ContentValues values = new ContentValues();
		values.put(OtokouChargeAdapter.COL_1_NAME, user.getId());
		values.put(OtokouChargeAdapter.COL_2_NAME, vehicle.getId());
		values.put(OtokouChargeAdapter.COL_3_NAME, charge.getCategory());
		values.put(OtokouChargeAdapter.COL_4_NAME, sent);
		values.put(OtokouChargeAdapter.COL_5_NAME, charge.getDate());
		values.put(OtokouChargeAdapter.COL_6_NAME, charge.getKilometers());
		values.put(OtokouChargeAdapter.COL_7_NAME, charge.getAmount());
		values.put(OtokouChargeAdapter.COL_8_NAME, charge.getComment());
		values.put(OtokouChargeAdapter.COL_9_NAME, charge.getQuantity());
		return db.insert(OtokouChargeAdapter.TABLE_NAME, null, values);		
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
	public boolean deleteChargeById(long id){
		if (!connectionOpen) return false;
		
		return db.delete(OtokouChargeAdapter.TABLE_NAME, OtokouChargeAdapter.COL_ID_NAME+"="+id, null) == 1;
	}
	
	/**
	 * Since Version 1<p>
	 * 
	 * Delete rows in the table using the user_id as row identifier.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param id identifier of the rows to delete
	 * @return number of row deleted, -1 if error
	 */	
	public int deleteChargesByUserId(long user_id){
		if (!connectionOpen) return -1;
		
		return db.delete(OtokouChargeAdapter.TABLE_NAME, OtokouChargeAdapter.COL_1_NAME+"="+user_id, null);
	}
	
	/**
	 * Since Version 1<p>
	 * 
	 * Delete rows in the table using the vehicle_id as row identifier.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param id identifier of the rows to delete
	 * @return number of row deleted, -1 if error
	 */	
	public int deleteChargesByVehicleId(long vehicle_id){
		if (!connectionOpen) return -1;
		
		return db.delete(OtokouChargeAdapter.TABLE_NAME, OtokouChargeAdapter.COL_2_NAME+"="+vehicle_id, null);
	}
	
	/**
	 * Since Version 1<p>
	 * 
	 * Update a row in the table identified by the its ID.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param id	id of the row to update
	 * @param charge	OtokouCharge instance
	 * @param user	 OtokouUser  instance (user owning the charge)
	 * @param vehicle   Vehicle  instance (vehicle owning the charge)
	 * @param sent	 sent field value
	 * @return true if the update of 1 database row executed correctly, false otherwise
	 */		
	public boolean updateChargeById(long id, OtokouCharge charge, OtokouUser user, OtokouVehicle vehicle, long sent) {
		if (!connectionOpen) return false;
		
		ContentValues values = new ContentValues();
		values.put(OtokouChargeAdapter.COL_1_NAME, user.getId());
		values.put(OtokouChargeAdapter.COL_2_NAME, vehicle.getId());
		values.put(OtokouChargeAdapter.COL_3_NAME, charge.getCategory());
		values.put(OtokouChargeAdapter.COL_4_NAME, sent);
		values.put(OtokouChargeAdapter.COL_5_NAME, charge.getDate());
		values.put(OtokouChargeAdapter.COL_6_NAME, charge.getKilometers());
		values.put(OtokouChargeAdapter.COL_7_NAME, charge.getAmount());
		values.put(OtokouChargeAdapter.COL_8_NAME, charge.getComment());
		values.put(OtokouChargeAdapter.COL_9_NAME, charge.getQuantity());
		
		return db.update(OtokouChargeAdapter.TABLE_NAME, values, OtokouChargeAdapter.COL_ID_NAME+"="+id, null) == 1;
	}

	/**
	 * Since Version 1<p>
	 * 
	 * Get a cursor containing all the Table rows and columns.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @return cursor object containing the table data. null object will be returned in case of errors.
	 */		
	public Cursor getAllCharges(){
		if (!connectionOpen) return null;
		
		return db.query(OtokouChargeAdapter.TABLE_NAME, 
				new String[]{
					OtokouChargeAdapter.COL_ID_NAME,
					OtokouChargeAdapter.COL_1_NAME,
					OtokouChargeAdapter.COL_2_NAME,
					OtokouChargeAdapter.COL_3_NAME,
					OtokouChargeAdapter.COL_4_NAME,
					OtokouChargeAdapter.COL_5_NAME,
					OtokouChargeAdapter.COL_6_NAME,
					OtokouChargeAdapter.COL_7_NAME,
					OtokouChargeAdapter.COL_8_NAME,
					OtokouChargeAdapter.COL_9_NAME,
		 		},
		 		null,null, null, null, 
		 OtokouChargeAdapter.COL_1_NAME);
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
	public Cursor getChargeById(long id){
		if (!connectionOpen) return null;
		
		return db.query(OtokouChargeAdapter.TABLE_NAME, 
				null, 
				""+OtokouChargeAdapter.COL_ID_NAME+ "="+id, 
				null,
				null, 
				null, 
				null);
	}
	
	/**
	 * Since Version 1<p>
	 * 
	 * Get a row identified by its user_id.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param user_id	user_id the row to get
	 * @return cursor object containing the row data. null object will be returned in case of errors.
	 */		
	public Cursor getChargesByUserId(long user_id){
		if (!connectionOpen) return null;
		
		return db.query(OtokouChargeAdapter.TABLE_NAME, 
				null, 
				""+OtokouChargeAdapter.COL_1_NAME+ "="+user_id, 
				null,
				null, 
				null, 
				null);
	}
	
	/**
	 * Since Version 1<p>
	 * 
	 * Get a row identified by its vehicle_id.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param user_id	user_id the row to get
	 * @return cursor object containing the row data. null object will be returned in case of errors.
	 */		
	public Cursor getChargesByVehicleId(long vehicle_id){
		if (!connectionOpen) return null;
		
		return db.query(OtokouChargeAdapter.TABLE_NAME, 
				null, 
				""+OtokouChargeAdapter.COL_2_NAME+ "="+vehicle_id, 
				null,
				null, 
				null, 
				null);
	}
}

