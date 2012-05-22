package com.bl457xor.app.otokou;

public class OtokouException extends Exception {
	// constants
	private static final long serialVersionUID = 970776244923131331L;
	public static final String MESSAGE_UNDEFINED_CODE = "Unexpected exception code";
    public static final int CODE_asd = 0;
    public static final String MESSAGE_asd = "";
	
	// members
	private int exceptionCode;
	private boolean customMessage;
	
	public OtokouException(int exceptionCode) {
		super(messageFromCode(exceptionCode));
		this.exceptionCode = exceptionCode;
		this.customMessage = false;
	}
	
	public OtokouException(String detailMessage, int exceptionCode) {
		super(detailMessage);
		this.exceptionCode = exceptionCode;
		this.customMessage = true;
	}
	
	public static String messageFromCode(int exceptionCode) {
		switch (exceptionCode) {
		case CODE_asd: return MESSAGE_asd;
		default: return MESSAGE_UNDEFINED_CODE;
		}
	}
	
	public int getExceptionCode() {
		return exceptionCode;
	}
	
	public boolean isCustomMessage () {
		return customMessage;
	}
}
