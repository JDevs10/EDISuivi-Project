import { Injectable } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';

@Injectable({
  providedIn: 'root'
})
export class AuthenticationService {

  constructor() { }

  loginForm = new FormGroup({
    user: new FormControl('', Validators.required),
    password: new FormControl('', Validators.required)
  });

  loggedIn(){
    // if the user token exist the return is true or else false
    let res = false;
    if(!!localStorage.getItem("userToken")){
      const data = JSON.parse(localStorage.getItem("userToken"));
      
      console.log("validTime : ", data.valideData);

      const now = new Date();
      const valid_date = new Date(data.valideData);

      if(now.getTime() < valid_date.getTime()){
        console.log("now : ", now.getTime());
        console.log("valid_date : ", valid_date);
        res = true;
      }else{
        this.doLogout();
      }
    }
    return res
  }

  doLogin(value){
    // alert("user : "+value.user+"\npassword: "+value.password);
    const d = new Date();
    const validTime = d.getTime() + 900000;
    const data = {
      token: "hello test token JL => "+value.user, 
      valideData: validTime
    }
    localStorage.setItem("userToken", JSON.stringify(data));
    window.location.href="home";
  }

  doLogout() {
    localStorage.removeItem('userUid');
    localStorage.removeItem('userEmail');
    localStorage.removeItem("userToken");
    window.location.href="login";
 }

}
