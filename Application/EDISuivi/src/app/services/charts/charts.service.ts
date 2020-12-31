import { Injectable } from '@angular/core';
import { Observable, of } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import { ChartData } from 'src/app/utils/Models/ChartData';

@Injectable({
  providedIn: 'root'
})
export class ChartsService {
  private url = environment.api.service;
  private DOLAPIKEY = "3-8-13-12-7-8-24-8";

  constructor(private http: HttpClient) { }

  getNbOrdersByMonthOfTwoYears(socId): Observable<ChartData>{
    // console.log("url : ", this.url+`/edisuiviapi/chart/nb-orders-2-years?socId=${socId}&DOLAPIKEY=${this.DOLAPIKEY}`);
    return this.http.get<ChartData>(this.url+`/edisuiviapi/chart/nb-orders-2-years?socId=${socId}&DOLAPIKEY=${this.DOLAPIKEY}`, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}});
  }
}
