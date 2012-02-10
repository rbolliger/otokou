package com.bl457xor.app.otokou;

import android.os.Bundle;
import android.preference.PreferenceActivity;

public class UserPreferenceActivity extends PreferenceActivity {
	@Override
	public void onCreate(Bundle savedInstanceState) {
	    super.onCreate(savedInstanceState);
	    addPreferencesFromResource(R.xml.userpreferences);
	}
}
