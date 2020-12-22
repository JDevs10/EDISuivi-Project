import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Observable, of } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { EncrDecrService } from '../encryption/encr-decr.service';
import { environment } from '../../../environments/environment';


@Injectable({
  providedIn: 'root'
})
export class AuthenticationService {

  private cors_http = "https://cors-anywhere.herokuapp.com/";
  private url_test = `${this.cors_http}http://82.253.71.109/prod/bdc_v11_04/api/index.php`;
  private url = environment.api.service;
  private DOLAPIKEY = "3-8-13-12-7-8-24-8";

  private HEADERS = {
    'Referrer-Policy': 'no-referrer',
    'Content-Type': 'application/x-www-form-urlencoded', 
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Methods': 'GET, POST, DELETE, PUT, OPTIONS',
    'Access-Control-Allow-Headers': 'DNT,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,Range',
    'Access-Control-Expose-Headers': 'Content-Length,Content-Range'
  };

  constructor(private http: HttpClient, 
    private router: Router,
    private encrDecrService: EncrDecrService) { }

  loginForm = new FormGroup({
    user: new FormControl('JL', Validators.required),
    password: new FormControl('anexys1,', Validators.required)
  });

  loggedIn(){
    // if the user token exist the return is true or else false
    let res = false;
    if(!!localStorage.getItem("userSuccess")){
      const data = this.getLoggedInUserInfo();
      const now = new Date();
      const valid_date = new Date(data.valideData);

      if(now.getTime() < valid_date.getTime()){
        // console.log("now : ", now.getTime());
        // console.log("valid_date : ", valid_date);
        res = true;
      }else{
        this.doLogout();
      }
    }
    return res
  }

  doLogin(value): Observable<any[]>{
    //console.log("url : ", this.url+`/edisuiviapi/login?login=${value.user}&password=${value.password}&DOLAPIKEY=${this.DOLAPIKEY}`);
    return this.http.post<any[]>(this.url+`/edisuiviapi/login?login=${value.user}&password=${value.password}&DOLAPIKEY=${this.DOLAPIKEY}`, {headers: this.HEADERS});
  }

  createUserLocalToken(res){
    const d = new Date();
    const validTime = d.getTime() + 1800000;
    const data = {
      success: res.success,
      valideData: validTime
    }

    localStorage.setItem("userSuccess", this.encrDecrService.encrypt(JSON.stringify(data)));
  }

  getLoggedInUserInfo(){
    if(!!localStorage.getItem("userSuccess")){
      const data = JSON.parse(this.encrDecrService.decrypt(localStorage.getItem("userSuccess")));
      //console.log("success : ", data.success);
      return data;
    }
    return null;
  }

  doLogout() {
    localStorage.removeItem("userSuccess");
 }

 doLogout_() {
  localStorage.removeItem("userSuccess");
  this.router.navigate(['../login']);
}

}
