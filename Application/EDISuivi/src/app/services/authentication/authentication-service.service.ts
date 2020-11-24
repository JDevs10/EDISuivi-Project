import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Observable, of } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class AuthenticationService {

  private cors_http = "https://cors-anywhere.herokuapp.com/";
  private url_test = `${this.cors_http}http://82.253.71.109/prod/bdc_v11_04/api/index.php`;
  private url = `http://82.253.71.109/prod/bdc_v11_04/api/index.php`;
  private DOLAPIKEY = "3-8-13-12-7-8-24-8";

  constructor(private http: HttpClient, private router: Router) { }

  loginForm = new FormGroup({
    user: new FormControl('JL_test', Validators.required),
    password: new FormControl('anexys1,', Validators.required)
  });

  loggedIn(){
    // if the user token exist the return is true or else false
    let res = false;
    if(!!localStorage.getItem("userSuccess")){
      const data = JSON.parse(localStorage.getItem("userSuccess"));
      
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

  doLogin(value): Observable<any[]>{
    console.log("url : ", this.url_test+`/edisuiviapi/login?login=${value.user}&password=${value.password}&DOLAPIKEY=${this.DOLAPIKEY}`);
    return this.http.post<any[]>(this.url_test+`/edisuiviapi/login?login=${value.user}&password=${value.password}&DOLAPIKEY=${this.DOLAPIKEY}`, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}});
  }

  getLoggedInUserInfo(){
    if(!!localStorage.getItem("userSuccess")){
      const data = JSON.parse(localStorage.getItem("userSuccess"));
      //console.log("success : ", data.success);
      return data.success;
    }
    return null;
  }

  doLogout() {
    localStorage.removeItem("userSuccess");
    // window.location.href="/#/login";
 }

 doLogout_() {
  localStorage.removeItem("userSuccess");
  this.router.navigate(['../login']);
}

}
