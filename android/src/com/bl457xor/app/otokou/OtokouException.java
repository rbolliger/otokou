package com.bl457xor.app.otokou;

public class OtokouException extends Exception {
	//// constants
	private static final long serialVersionUID = 970776244923131331L;
	
	/// exceptions codes
	public static final String MESSAGE_UNDEFINED_CODE = "Unexpected exception code";
	// 10000-19999 -> no solution errors. request not send. response not retrieved.
	public static final int CODE_WRITE_GET_USER_XML_FAIL = 11001;
	public static final String MESSAGE_WRITE_GET_USER_XML_FAIL = "Coundn't create getUser XML string";	
	public static final int CODE_WRITE_GET_VEHICLES_XML_FAIL = 11002;
	public static final String MESSAGE_WRITE_GET_VEHICLES_XML_FAIL = "Coundn't create getVehicles XML string";		
	public static final int CODE_WRITE_SET_CHARGE_XML_FAIL = 11003;
	public static final String MESSAGE_WRITE_SET_CHARGE_XML_FAIL = "Coundn't create setCharge XML string";
	// 20000-29999 -> no solution errors. request maybe sent. response not retrieved.
	public static final int CODE_HTTP_CLIENT_FAIL = 21001;
	public static final String MESSAGE_HTTP_CLIENT_FAIL = "Error during HTTP connection";
	public static final int CODE_HTTP_CLIENT_EMPTY_RESPONSE = 21002;
	public static final String MESSAGE_HTTP_CLIENT_EMPTY_RESPONSE = "No response from Otokou Server received";
	// 30000-39999 -> no solution errors. request sent. response not correct.
	public static final int CODE_RESPONSE_GET_USER_PARSE_FAIL = 31101;
	public static final String MESSAGE_RESPONSE_GET_USER_PARSE_FAIL = "Couldn't parse getUser response";	
	public static final int CODE_RESPONSE_GET_USER_XML_HEADER_PARSE_FAIL = 31102;
	public static final String MESSAGE_RESPONSE_GET_USER_XML_HEADER_PARSE_FAIL = "Couldn't parse getUser XML header";	
	public static final int CODE_RESPONSE_GET_USER_XML_BODY_PARSE_FAIL = 31103;
	public static final String MESSAGE_RESPONSE_GET_USER_XML_BODY_PARSE_FAIL = "Couldn't parse getUser XML body";	
	public static final int CODE_RESPONSE_GET_VEHICLES_PARSE_FAIL = 31201;
	public static final String MESSAGE_RESPONSE_GET_VEHICLES_PARSE_FAIL = "Couldn't parse getVehicles response";	
	public static final int CODE_RESPONSE_GET_VEHICLES_XML_HEADER_PARSE_FAIL = 31202;
	public static final String MESSAGE_RESPONSE_GET_VEHICLES_XML_HEADER_PARSE_FAIL = "Couldn't parse getVehicles XML header";	
	public static final int CODE_RESPONSE_GET_VEHICLES_XML_BODY_PARSE_FAIL = 31203;
	public static final String MESSAGE_RESPONSE_GET_VEHICLES_XML_BODY_PARSE_FAIL = "Couldn't parse getVehicles XML body";
	public static final int CODE_RESPONSE_SET_CHARGE_PARSE_FAIL = 31301;
	public static final String MESSAGE_RESPONSE_SET_CHARGE_PARSE_FAIL = "Couldn't parse setCharge response";	
	public static final int CODE_RESPONSE_SET_CHARGE_XML_HEADER_PARSE_FAIL = 31302;
	public static final String MESSAGE_RESPONSE_SET_CHARGE_XML_HEADER_PARSE_FAIL = "Couldn't parse setCharge XML header";	
	public static final int CODE_RESPONSE_SET_CHARGE_XML_BODY_PARSE_FAIL = 31303;
	public static final String MESSAGE_RESPONSE_SET_CHARGE_XML_BODY_PARSE_FAIL = "Couldn't parse setCharge XML body";
	// 40000-49999 -> identified errors. request sent. response received.
	public static final int CODE_RESPONSE_SET_CHARGE_NOT_OK = 41001;
	public static final String MESSAGE_RESPONSE_SET_CHARGE_NOT_OK = "setCharge request failed";
	public static final int CODE_RESPONSE_GET_USER_INCORRECT_LOGIN = 42001;
	public static final String MESSAGE_RESPONSE_GET_USER_INCORRECT_LOGIN = "login data incorrect";
	public static final int CODE_RESPONSE_GET_VEHICLES_INCORRECT_LOGIN = 42002;
	public static final String MESSAGE_RESPONSE_GET_VEHICLES_INCORRECT_LOGIN = "login data incorrect";
	
	//// members
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
		case CODE_WRITE_GET_USER_XML_FAIL: return MESSAGE_WRITE_GET_USER_XML_FAIL;
		case CODE_WRITE_GET_VEHICLES_XML_FAIL: return MESSAGE_WRITE_GET_VEHICLES_XML_FAIL;
		case CODE_WRITE_SET_CHARGE_XML_FAIL: return MESSAGE_WRITE_SET_CHARGE_XML_FAIL;
		case CODE_HTTP_CLIENT_FAIL: return MESSAGE_HTTP_CLIENT_FAIL;
		case CODE_HTTP_CLIENT_EMPTY_RESPONSE: return MESSAGE_HTTP_CLIENT_EMPTY_RESPONSE;
		case CODE_RESPONSE_GET_USER_PARSE_FAIL: return MESSAGE_RESPONSE_GET_USER_PARSE_FAIL;
		case CODE_RESPONSE_GET_USER_XML_HEADER_PARSE_FAIL: return MESSAGE_RESPONSE_GET_USER_XML_HEADER_PARSE_FAIL;
		case CODE_RESPONSE_GET_USER_XML_BODY_PARSE_FAIL: return MESSAGE_RESPONSE_GET_USER_XML_BODY_PARSE_FAIL;
		case CODE_RESPONSE_GET_VEHICLES_PARSE_FAIL: return MESSAGE_RESPONSE_GET_VEHICLES_PARSE_FAIL;
		case CODE_RESPONSE_GET_VEHICLES_XML_HEADER_PARSE_FAIL: return MESSAGE_RESPONSE_GET_VEHICLES_XML_HEADER_PARSE_FAIL;
		case CODE_RESPONSE_GET_VEHICLES_XML_BODY_PARSE_FAIL: return MESSAGE_RESPONSE_GET_VEHICLES_XML_BODY_PARSE_FAIL;
		case CODE_RESPONSE_SET_CHARGE_PARSE_FAIL: return MESSAGE_RESPONSE_SET_CHARGE_PARSE_FAIL;
		case CODE_RESPONSE_SET_CHARGE_XML_HEADER_PARSE_FAIL: return MESSAGE_RESPONSE_SET_CHARGE_XML_HEADER_PARSE_FAIL;
		case CODE_RESPONSE_SET_CHARGE_XML_BODY_PARSE_FAIL: return MESSAGE_RESPONSE_SET_CHARGE_XML_BODY_PARSE_FAIL;
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
