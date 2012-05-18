package com.bl457xor.app.otokou.db;

import java.util.ArrayList;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

import com.bl457xor.app.otokou.OtokouUser;
import com.bl457xor.app.otokou.OtokouVehicle;

/**
 * Adapter to the database containing the Vehicles.<p>
 * 
 * databases structure:
 * -database: vehicles
 * --table: vehicles
 * ---field: id (primary key)
 * ---field: user_id (foreign key user id)
 * ---field: otokou_vehicle_id (otokou database primary key)
 * ---field: name (name of the vehicle)
 * 
 * usage:<br>
 *  1. build an instance of this object using the constructor.<br>
 *  2. use the open() method to open a connection.<br>
 *  3. use the various method you need to perform actions on the database (see functionalities section).<br>
 *  4. use the close() method to close a connection.<p>
 *  functionalities:<br>
 *  1. Vehicles database connection<br>
 *  2. Vehicles database creation and upgrade<br>
 *  3. delete all Vehicles (deleteAllVehicles() method)<br>
 *  4. insert of a new Vehicle (insertVehicle() method)<br>
 *  5. delete a single Vehicle by id (deleteVehicleById() method)<br>
 *  6. delete Vehicles by user_id (deleteVehicleByUserId() method)<br>
 *  7. update a single Vehicle (updateVehicleById() method)<br>
 *  8. (getAllVehicles() method)<br>
 *  9. (getVehicleById() method)<br>
 *  10. (getVehiclesByUserId() method)<br>
 *  
 *  note: related databases will also be affected (deleting a vehicle will also delete all of his charges)
 *  
 *  @author Dave Bergomi
 *  @version 1
 */
public class OtokouVehicleAdapter {
	
	/** Version Database				**/ public static final int DB_VERSION = 1;
	/** Name of the Database			**/ public static final String DB_NAME = "vehicles";
	/** Name of the Table				**/ public static final String TABLE_NAME = "vehicles";
	/** Primary key field name			**/ public static final String COL_ID_NAME = "id";
	/** Primary key field type			**/ public static final String COL_ID_TYPE = "integer primary key autoincrement";
	/** column 1 field name				**/ public static final String COL_1_NAME = "user_id"; 
	/** column 1 field type				**/ public static final String COL_1_TYPE = "integer not null";
	/** column 1 field default value	**/ public static final long COL_1_DEFAULT = 0;
	/** column 2 field name				**/ public static final String COL_2_NAME = "otokou_vehicle_id";
	/** column 2 field type				**/ public static final String COL_2_TYPE = "integer not null";
	/** column 2 field default value	**/ public static final long COL_2_DEFAULT = 0;
	/** column 3 field name				**/ public static final String COL_3_NAME = "name";
	/** column 3 field type				**/ public static final String COL_3_TYPE = "text not null";
	/** column 3 field default value	**/ public static final String COL_3_DEFAULT = "";
	private Vehicle dbHelper;
	private Context context;
	private SQLiteDatabase db;
	private boolean connectionOpen;
	
	/**
	 * Since Version 1<p>
	 * 
	 * OtokouVehicleAdapter constructor.<p>
	 * 
	 * @param context	application context 
	 */	
	public OtokouVehicleAdapter(Context _context){
		context = _context;
		dbHelper = new Vehicle(context);
		connectionOpen = false;
	}	

	/**
	 * Since Version 1<p>
	 * 
	 * Extension of the SQLiteOpenHelper used in this Adapter.
	 */	
	private class Vehicle extends SQLiteOpenHelper{

		public Vehicle(Context context) {
			super(context, OtokouVehicleAdapter.DB_NAME, null, OtokouVehicleAdapter.DB_VERSION);
		}

		@Override
		public void onCreate(SQLiteDatabase db) {
			db.execSQL("create table " + OtokouVehicleAdapter.TABLE_NAME + " (" + OtokouVehicleAdapter.COL_ID_NAME + " " + OtokouVehicleAdapter.COL_ID_TYPE + ", "
					+ OtokouVehicleAdapter.COL_1_NAME + " " + OtokouVehicleAdapter.COL_1_TYPE + ","
					+ OtokouVehicleAdapter.COL_2_NAME + " " + OtokouVehicleAdapter.COL_2_TYPE + ","
					+ OtokouVehicleAdapter.COL_3_NAME + " " + OtokouVehicleAdapter.COL_3_TYPE
					+ ");");
			
			// delete related databases data
			OtokouChargeAdapter otokouChargeAdapter = new OtokouChargeAdapter(context).open();
			otokouChargeAdapter.deleteAllCharges().close();
		}

		@Override
		public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
			if (oldVersion < newVersion) {
				db.execSQL("DROP TABLE IF EXISTS "+ OtokouVehicleAdapter.TABLE_NAME);
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
	public OtokouVehicleAdapter open(){
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
	public OtokouVehicleAdapter deleteAllVehicles(){
		if (connectionOpen) {
			// delete related databases data
			OtokouChargeAdapter otokouChargeAdapter = new OtokouChargeAdapter(context).open();
			otokouChargeAdapter.deleteAllCharges().close();
			
			db.execSQL("DELETE FROM "+ OtokouVehicleAdapter.TABLE_NAME);
		}
		return this;
	}

	/**
	 * Since Version 1<p>
	 * 
	 * Insert a row in the table.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param vehicle	OtokouVehicle instance
	 * @param user	 OtokouUser  instance (user owning the vehicle)
	 * @return id of the inserted row or -1 in case of an error
	 */	
	public long insertVehicle(OtokouVehicle vehicle, OtokouUser user) {
		if (!connectionOpen) return -1;
		
		ContentValues values = new ContentValues();
		values.put(OtokouVehicleAdapter.COL_1_NAME, user.getId());
		values.put(OtokouVehicleAdapter.COL_2_NAME, vehicle.getOtokouVehicleId());
		values.put(OtokouVehicleAdapter.COL_3_NAME, vehicle.getVehicleName());
		return db.insert(OtokouVehicleAdapter.TABLE_NAME, null, values);		
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
	public boolean deleteVehicleById(long id){
		if (!connectionOpen) return false;
		
		// delete related databases data
		OtokouChargeAdapter otokouChargeAdapter = new OtokouChargeAdapter(context).open();
		otokouChargeAdapter.deleteChargesByVehicleId(id);
		otokouChargeAdapter.close();
		
		return db.delete(OtokouVehicleAdapter.TABLE_NAME, OtokouVehicleAdapter.COL_ID_NAME+"="+id, null) == 1;
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
	public int deleteVehiclesByUserId(long user_id){
		if (!connectionOpen) return -1;
		
		// delete related databases data
		Cursor cursor = getVehiclesByUserId(user_id);
		if (cursor.getCount() > 0) {
			OtokouChargeAdapter otokouChargeAdapter = new OtokouChargeAdapter(context).open();
			cursor.moveToLast();
			do {
				otokouChargeAdapter.deleteChargesByVehicleId(cursor.getLong(cursor.getColumnIndex(COL_ID_NAME)));
			} while (cursor.moveToPrevious());
			otokouChargeAdapter.close();
		}
		cursor.close();
		
		return db.delete(OtokouVehicleAdapter.TABLE_NAME, OtokouVehicleAdapter.COL_1_NAME+"="+user_id, null);
	}
	
	/**
	 * Since Version 1<p>
	 * 
	 * Update a row in the table identified by the its ID.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param id	id of the row to update
	 * @param vehicle	OtokouVehicle instance
	 * @param user	 OtokouUser  instance (user owning the vehicle)
	 * @return true if the update of 1 database row executed correctly, false otherwise
	 */		
	public boolean updateVehicleById(long id, OtokouVehicle vehicle, OtokouUser user) {
		if (!connectionOpen) return false;
		
		ContentValues values = new ContentValues();
		values.put(OtokouVehicleAdapter.COL_1_NAME, user.getId());
		values.put(OtokouVehicleAdapter.COL_2_NAME, vehicle.getOtokouVehicleId());
		values.put(OtokouVehicleAdapter.COL_3_NAME, vehicle.getVehicleName());

		return db.update(OtokouVehicleAdapter.TABLE_NAME, values, OtokouVehicleAdapter.COL_ID_NAME+"="+id, null) == 1;
	}

	/**
	 * Since Version 1<p>
	 * 
	 * Get a cursor containing all the Table rows and columns.<p>
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @return cursor object containing the table data. null object will be returned in case of errors.
	 */		
	public Cursor getAllVehicles(){
		if (!connectionOpen) return null;
		
		return db.query(OtokouVehicleAdapter.TABLE_NAME, 
				new String[]{
					OtokouVehicleAdapter.COL_ID_NAME,
					OtokouVehicleAdapter.COL_1_NAME,
					OtokouVehicleAdapter.COL_2_NAME,
					OtokouVehicleAdapter.COL_3_NAME,
		 		},
		 		null,null, null, null, 
		 OtokouVehicleAdapter.COL_1_NAME);
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
	public Cursor getVehicleById(long id){
		if (!connectionOpen) return null;
		
		return db.query(OtokouVehicleAdapter.TABLE_NAME, 
				null, 
				""+OtokouVehicleAdapter.COL_ID_NAME+ "="+id, 
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
	public Cursor getVehiclesByUserId(long user_id){
		if (!connectionOpen) return null;
		
		return db.query(OtokouVehicleAdapter.TABLE_NAME, 
				null, 
				""+OtokouVehicleAdapter.COL_1_NAME+ "="+user_id, 
				null,
				null, 
				null, 
				null);
	}
	
	/**
	 * Since Version 1<p>
	 * 
	 * Updates all vehicles of an user.<p>
	 * Update existing vehicles (using otokou_vehicle_id), add new vehicles, delete not existing vehicles
	 * note: need a call to the open() method before a call to this method.
	 * 
	 * @param user	OtokouUser instance
	 * @param vehicles	collection of OtokouVehicle instances
	 * 
	 * @return false if error.
	 */		
	public boolean updateVehicleForUser(OtokouUser user, ArrayList<OtokouVehicle> vehicles){
		if (!connectionOpen) return false;
		
		// save vehicles data to database
		Cursor cursor = this.getVehiclesByUserId(user.getId());
		if (cursor.getCount() > 0) {
			cursor.moveToLast();
			do {
				long otokouVehicleId = cursor.getLong(cursor.getColumnIndex(COL_2_NAME));
				boolean found = false;
				for (OtokouVehicle vehicle : vehicles) {
					if (vehicle.getOtokouVehicleId() == otokouVehicleId) {
						long id = cursor.getLong(cursor.getColumnIndex(COL_ID_NAME));
						found = true;									
						vehicle.setFound(true);
						vehicle.setId(id);			
						this.updateVehicleById(id, vehicle, user);
					}
				}
				if (!found) {
					this.deleteVehicleById(cursor.getLong(cursor.getColumnIndex(COL_ID_NAME)));
				}
			} while (cursor.moveToPrevious());
			for (OtokouVehicle vehicle : vehicles) {
				if (!vehicle.isFound()) {
					vehicle.setId(this.insertVehicle(vehicle, user));
				}
			}
		}
		else {
			for (OtokouVehicle vehicle : vehicles) {
				vehicle.setId(this.insertVehicle(vehicle, user));
			}
		}
		cursor.close();
		
		return true;
	}
}
