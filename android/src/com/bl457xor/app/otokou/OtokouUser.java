package com.bl457xor.app.otokou;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.io.Serializable;
import java.io.StreamCorruptedException;
import java.util.Arrays;
import java.util.List;

public class OtokouUser implements Serializable{

	private static final long serialVersionUID = 2592158732300070601L;
	public long userID;
	//public String title;
	public String firstName;
	//public String middleName;
	public String lastName;
	//public String userName;
	//public String password
	//public String email;
	public String apiKey;
	
	public OtokouUser(String rawData, String apikey) throws Exception {
		List<String> userData = Arrays.asList(rawData.split(","));
		if (userData.size() >= 2) {
			if (userData.get(0).equals("000")) {
				if (userData.size() == 5) {
					this.userID = Long.parseLong(userData.get(2));
					this.firstName =userData.get(3);
					this.lastName =userData.get(4);
				}
				else {
					throw new Exception("Api Error: number of filds incorrect");
				}
			}
			else {
				throw new Exception("Api Error: "+userData.get(0));
			}
		}
		else {
			throw new Exception("Api Undefined Error");
		}
		this.apiKey = apikey;
	}
	
	@Override
	public String toString() {
		return firstName+" "+lastName;	
	}
	
	public static OtokouUser OtokouUserFromByteArray(byte[] byteArray) {
		try {
			ByteArrayInputStream b = new ByteArrayInputStream(byteArray);
			ObjectInputStream o = new ObjectInputStream(b);
			return (OtokouUser)o.readObject();
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
		return null;
	}

	public byte[] toByteArray() {
		try {
	        ByteArrayOutputStream b = new ByteArrayOutputStream();
	        ObjectOutputStream o = new ObjectOutputStream(b);
	        o.writeObject(this);
	        return b.toByteArray();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return null;
	}
}
