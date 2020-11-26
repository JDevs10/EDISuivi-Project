import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Observable, of } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';


@Injectable({
  providedIn: 'root'
})
export class SupportService {

  private cors_http = "https://cors-anywhere.herokuapp.com/";
  private url_test = `${this.cors_http}https://bdc.bdcloud.fr/api/index.php`;
  private url = environment.api.support;
  private DOLAPIKEY = "d39b4b7c442c0fb309b77cf9b86d4005ce8acc60"; //EDISuivi


  constructor(private http: HttpClient, private router: Router) { }

  supportFormExtern = new FormGroup({
    sujetTicket: new FormControl({value: 'Changer Mon mot de passe', disabled: true}, Validators.required),
    companyTicket: new FormControl('', Validators.required),
    messageTicket: new FormControl('', Validators.required)
  });

  supportFormIntern = new FormGroup({
    sujetTicket: new FormControl('', Validators.required),
    companyTicket: new FormControl('', Validators.required),
    messageTicket: new FormControl('', Validators.required)
  });

  sendTicket(data): Observable<any>{
    const headers = { 
      'Content-Type': 'application/json', 
      'Accept': "application/json",
      'DOLAPIKEY': `${this.DOLAPIKEY}` 
    };
    return this.http.post<any>(`${this.url}/tickets`, data, { headers });

  }
}
