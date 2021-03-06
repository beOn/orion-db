<?php

/*

OrionDB database Authentication Module

*/

class OrionDB_authmodule_ORIONDB {
 
   function auth(array $data_array){
     
      // check the contents of the $data_array
      /*
  	$ORIONDBCFG_auth_server[1]["name"] = "name"; // name to show on the screen (send back to SC)
	$ORIONDBCFG_auth_server[1]["type"] = "ORIONDB"; // An authentication type which allows authentication against the database	
   $ORIONDBCFG_auth_server[1]["usertables"] = ["users"]; // an array of tables where user information can be found
   $ORIONDBCFG_auth_server[1]["passwordfield"] = ["passwd"]; // the field in the tables where password info is stored


      */  
      //logmessage("This place is being reached");
      //print_r($data_array);
      $usertable_exists = array_key_exists("usertable",$data_array);
      $passwordfield_exists = array_key_exists("passwordfield",$data_array);
      $user_exists = array_key_exists("user_name",$data_array);
      $passwd_exists = array_key_exists("passwd",$data_array);
      //$md5_exists = array_key_exists("passwords_stored_in_md5",$data_array); // true or false
      $usernamefield_exists = array_key_exists("usernamefield",$data_array);
      //echo(join(array($usertable_exists,$passwordfield_exists,$user_exists,$passwd_exists,$usernamefield_exists),","));
      if(($usertable_exists) && ($passwordfield_exists) && ($user_exists) && ($passwd_exists) && ($usernamefield_exists)){
         //logmessage("Valid data for ORIONDB auth");
         $user = $data_array["user_name"];
         $passwd = $data_array["passwd"];
         $table = $data_array["usertable"];
         $passwdfield = $data_array["passwordfield"];
         $usernamefield = $data_array["usernamefield"];
            
         $tmpUser = eval("return new " . $table . "_class;");
         $tmpQueryInfo = new OrionDB_QueryInfo;
         $tmpQueryInfo->tablename = $table;
         $tmpQueryInfo->conditions[$usernamefield] = $user;
         $tmpUser->init_by_query($tmpQueryInfo);
         
         //print_r($tmpUser);
         // search the user table for the user 
         if($tmpUser){
            $password_in_user = eval("return \$tmpUser->" . $passwdfield . ";");
            //logmessage("password in db: " . $password_in_user);
            if($data_array["passwords_stored_in_md5"]){
               // The passwords are stored in md5, so make an md5 of the password received and check it against
               // the md5 from the database
               if($passwd == $password_in_user){
                  // valid auth
                  return true;
               } 
               else {
                  // check whether the password has been sent from SC in cleartext
                  $md5passwd = md5($passwd);
                  //logmessage("MD5 Passwd: " . $md5passwd);
                  //logmessage("Crypt Passwd: " . sha1(sha1($passwd)));
                  if($md5passwd == $password_in_user){
                     // valid auth
                     return true;
                  }  
                  else {
                     // no valid auth
                     return false;  
                  }
               }          
            } else {
               if($passwd == $password_in_user){
                  //valid auth
                  return true;  
               } 
               else {
                  // no valid auth
                  return false;   
               }
            }
         } // end if($tmpUser)
      }
   }  // end function auth
   
}


?>