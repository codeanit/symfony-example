/**
 * @author anit
 */


import java.util.HashMap;
import com.bdo.remit.cipher.impl.CipherImpl;

/**
 * 
 * @author anit
 *
 */
public class EncryptPassword {
	
	/**
	 * Encrypt BDO password
	 * 
	 * @param bdoPassword
	 * 
	 * @return String encrypted password
	 */
	private static String encryptPassword(String bdoPassword)
	{
		String encryptedPassword = null;
		
		try {
			encryptedPassword = CipherImpl.encrypt(bdoPassword);
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		return encryptedPassword;
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
//				String[] keyVal = args[0].split(",");
							
//				for(int i = 0; i < keyVal.length && allValuesAreSetProperly; i++) {
					String[] tempArray = args[0].split("=");
					
					if(tempArray[0] == null || tempArray[1] == null) {					
						allValuesAreSetProperly = false;
						
					} else {						
						hMap.put(tempArray[0], tempArray[1]);
					}
//				}
	
			} catch ( ArrayIndexOutOfBoundsException
					| NullPointerException e) {
//				 System.out.println("Error in setting key value pairs!");
				 hMap.clear();
			}
			
		}

		return hMap;
	}
	
	/**
	 * @param args
	 */
	public static void main(String[] args) {
			
		HashMap<String, String> hMap = changeStringArgsToKeyValue(args);
		String encryptedPassword = null;
//		System.out.println(hMap.size());		

		if ( hMap.size() > 0 && hMap.containsKey("password")) {
			encryptedPassword = encryptPassword(hMap.get("password"));
		}
		
		System.out.println(encryptedPassword);		
	}
}
