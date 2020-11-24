import { Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Observable, of } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';


@Injectable({
  providedIn: 'root'
})
export class SupportService {

  private cors_http = "https://cors-anywhere.herokuapp.com/";
  private url_test = `${this.cors_http}http://82.253.71.109/prod/bdc_v11_04/api/index.php`;
  private url = `http://82.253.71.109/prod/bdc_v11_04/api/index.php`;
  private DOLAPIKEY = "3-8-13-12-7-8-24-8";


  constructor(private http: HttpClient, private router: Router) { }

  supportForm = new FormGroup({
    sujetTicket: new FormControl('', Validators.required),
    companyTicket: new FormControl('', Validators.required),
    messageTicket: new FormControl('', Validators.required)
  });

  sendTicket(value): Observable<any> {
    return null;
  }
}
