package com.bl457xor.app.otokou;

import java.io.Serializable;
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
		return firstName+" "+lastName+" ("+userID+")";	
	}
}
