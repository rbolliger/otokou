package com.bl457xor.app.otokou.components;

public class OtokouComponent {
	//constants
	public static final int NO_ERROR_CODE = 0;
	public static final String NO_ERROR_MESSAGE = "";
	
	// global variables initialization
	protected int errorCode;
	protected String errorMessage;
	protected boolean isValid;
	
	public OtokouComponent() {
		 this.errorCode = NO_ERROR_CODE;
		 this.errorMessage = NO_ERROR_MESSAGE;
		 this.isValid = true;
	}
	
	public OtokouComponent(int errorCode, String errorMessage) {
		 this.errorCode = errorCode;
		 this.errorMessage = errorMessage;
		 this.isValid = false;		
	}	
	
	public int getErrorCode() {
		return errorCode;
	}
	
	public void setErrorCode(int errorCode) {
		this.errorCode = errorCode;
	}
	
	public String getErrorMessage() {
		return errorMessage;
	}
	
	public void setErrorMessage(String errorMessage) {
		this.errorMessage = errorMessage;
	}
	
	public boolean isValid() {
		return isValid;
	}
	
	public void setErrorCode(boolean isValid) {
		this.isValid = isValid;
	}
}
