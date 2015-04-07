/**
 * @author anit
 */
package web.assets.bdo;

import java.util.HashMap;
import java.io.IOException;
import java.io.StringWriter;
import java.security.InvalidKeyException;
import java.security.KeyStoreException;
import java.security.NoSuchAlgorithmException;
import java.security.SignatureException;
import java.security.UnrecoverableKeyException;
import java.security.cert.CertificateException;
import com.bdo.remit.pki.impl.PKIImpl;
import com.bdo.remit.cipher.impl.CipherImpl;


/**
 * @author anit
 *
 */
public class SignedData {
	
	private static String[] statusKeys = {
			"SignatureType",
			"CLEAR_BRS_PASSWORD",
			"TRANSACTION_REFERENCE_NUMBER",
			"KEYSTORE_FILE",
			"KEYSTORE_PASSWORD",
			"KEY_NAME",
			"KEY_PASSWORD"};

	private static String[] txnKeys = {
			"SignatureType",
			"CLEAR_BRS_PASSWORD",
			"TRANSACTION_REFERENCE_NUMBER",
			"LANDED_AMOUNT",
			"TRANSACTION_DATE",
			"ACCOUNT_NUMBER",						
			"KEYSTORE_FILE",
			"KEYSTORE_PASSWORD",
			"KEY_NAME",
			"KEY_PASSWORD"};
	
	/**
	 * @param args
	 */
	public static void main(String[] args) {
			
		HashMap<String, String> hMap = changeStringArgsToKeyValue(args);
		String signature = null;
//		System.out.println(hMap.size());		

		if ( hMap.size() > 0
			&& (
					hMap.containsKey("SignatureType")
					|| hMap.get("SignatureType").equals("TXN")
					|| hMap.get("SignatureType").equals("STATUS")
				)
		) {
			
			signature = createSignature(hMap);
		}
		
		
		System.out.println(signature);		
	}
	
	
	
	/**
	 * Checks the input array
	 * 
	 * @return boolean 
	 */
	private static HashMap<String, String> changeStringArgsToKeyValue(String[] args) 
	{	
		HashMap<String, String> hMap = new HashMap<String, String>();
		boolean allValuesAreSetProperly = true;
		
		if(args.length == 1) {
			try {
				String[] keyVal = args[0].split(",");
							
				for(int i = 0; i < keyVal.length && allValuesAreSetProperly; i++) {
					String[] tempArray = keyVal[i].split("=");
					
					if(tempArray[0] == null || tempArray[1] == null) {					
						allValuesAreSetProperly = false;
						
					} else {						
						hMap.put(tempArray[0], tempArray[1]);
					}
				}
	
			} catch ( ArrayIndexOutOfBoundsException
					| NullPointerException e) {
//				 System.out.println("Error in setting key value pairs!");
				 hMap.clear();
			}
			
		}

		return hMap;
	}
	
	/**
	 * 
	 * @param hTable
	 * 
	 * @return String
	 */
	private static String createSignature(HashMap<String, String> hTable)
	{
		 String signature = null;
 
	 	 switch (hTable.get("SignatureType")) {
	         case "TXN":
	        	 signature = getTxnSignature(hTable);
	             break;
	              
	         case "STATUS":	        	 
	        	 signature = getStatusSignature(hTable);
	         	 break;
	         	
	     	default:
	     		break;         		
		 }
 
		 return signature;
	}
	

	/**
	 * 
	 * @return String 
	 */
	private static String getStatusSignature(HashMap<String, String> hMap) {
		String stsSignedData = null;		
	
		if( checkKeysExists(hMap, statusKeys) ) {		
			try {
				stsSignedData = PKIImpl.getStsSignedData(
						hMap.get("CLEAR_BRS_PASSWORD"),
						hMap.get("TRANSACTION_REFERENCE_NUMBER"),
						hMap.get("KEYSTORE_FILE"),
						hMap.get("KEYSTORE_PASSWORD"),
						hMap.get("KEY_NAME"),
						hMap.get("KEY_PASSWORD")
						);
			} catch (UnrecoverableKeyException | InvalidKeyException
					| KeyStoreException | NoSuchAlgorithmException
					| CertificateException | SignatureException
					| IOException | ArrayIndexOutOfBoundsException
					| NullPointerException e) {				
			}
		}

		return stsSignedData;
		
	}
	

	/**
	 * 
	 * @return String 
	 */
	private static String getTxnSignature(HashMap<String, String> hMap) {
		String stsSignedData = null;		
	
		if( checkKeysExists(hMap, txnKeys) ) {		
			try {
				stsSignedData = PKIImpl.getTxnSignedData(
						hMap.get("CLEAR_BRS_PASSWORD"),
						hMap.get("TRANSACTION_REFERENCE_NUMBER"),
						hMap.get("LANDED_AMOUNT"),
						hMap.get("TRANSACTION_DATE"),
						hMap.get("ACCOUNT_NUMBER"),						
						hMap.get("KEYSTORE_FILE"),
						hMap.get("KEYSTORE_PASSWORD"),
						hMap.get("KEY_NAME"),
						hMap.get("KEY_PASSWORD")
						);
			} catch (UnrecoverableKeyException | InvalidKeyException
					| KeyStoreException | NoSuchAlgorithmException
					| CertificateException | SignatureException
					| IOException | ArrayIndexOutOfBoundsException
					| NullPointerException e) {
//				System.out.println("All parameters to generate TXN signature are not set properly!");
			}
		}

		return stsSignedData;
		
	}
	
	/**
	 * 
	 * @param hMap
	 * @param statusKeys
	 * @return
	 */
	private static boolean checkKeysExists(
			HashMap<String, String> hMap,
			String[] allKeys
	) {
		boolean allKeysExists = true;
		
		String[] hMapKeyVal = hMap.keySet().toArray(new String[hMap.size()]);
		
		try {
		
			if(hMapKeyVal.length == allKeys.length) {
				for(int i=0; i < allKeys.length && allKeysExists; i++) {

					if( !hMap.containsKey(allKeys[i])
							||  hMap.get(allKeys[i]) == null) {
						allKeysExists = false;
					}
				}
				
			} else {
				allKeysExists = false;				
			}
		} catch ( ArrayIndexOutOfBoundsException
				| NullPointerException e) {
		}
		
		return allKeysExists;
	}
	
	
}
