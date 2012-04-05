package com.bl457xor.app.otokou;

import javax.net.ssl.SSLException;

import org.apache.http.conn.ssl.AbstractVerifier;
import org.apache.http.conn.ssl.X509HostnameVerifier;

public class OtokouSslVerifier extends AbstractVerifier {

    private final X509HostnameVerifier delegate;

    public OtokouSslVerifier(final X509HostnameVerifier delegate) {
        this.delegate = delegate;
    }
    
	@Override
	public void verify(String host, String[] cns, String[] subjectAlts)
			throws SSLException {
		boolean ok = false;
        try {
            delegate.verify(host, cns, subjectAlts);
        } catch (SSLException e) {
            for (String cn : cns) {
            	if (cn.equals("*.kreativmedia.ch")) {
            		ok = true;
            	}
            }
            if(!ok) throw e;
        }

	}

}
