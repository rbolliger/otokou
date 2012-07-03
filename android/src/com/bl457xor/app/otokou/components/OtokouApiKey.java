package com.bl457xor.app.otokou.components;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class OtokouApiKey {

	public static boolean checkKey(String apiKey) {
		if (apiKey == "") return false;
		else {
			Pattern p = Pattern.compile("^[a-zA-Z0-9]{10}$");
			Matcher m = p.matcher(apiKey);
			if (m.find()) return true;
			else return false;
		}
	}

}
