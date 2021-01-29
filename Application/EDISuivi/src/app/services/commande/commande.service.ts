import { Injectable } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Observable, of } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { SuccessOrder } from 'src/app/utils/Models/SuccessOrder';
import { environment } from '../../../environments/environment';


@Injectable({
  providedIn: 'root'
})
export class CommandeService {

  private cors_http = "https://cors-anywhere.herokuapp.com/";
  private url_test = `${this.cors_http}http://82.253.71.109/prod/bdc_v11_04/api/index.php`;
  private url = environment.api.service;
  private DOLAPIKEY = "3-8-13-12-7-8-24-8";

  constructor(private http: HttpClient) { }

  limitForm = new FormGroup({
    limit: new FormControl('25')
  });

  filterForm = new FormGroup({
    ref: new FormControl(''),
    ref_client: new FormControl(''),
    client1: new FormControl(''),
    userCreated: new FormControl(''),
    assigned: new FormControl(''),
    dateCommande: new FormControl(''),
    delivery_date: new FormControl(''),
    total_ht: new FormControl(''),
    total_tva: new FormControl(''),
    total_ttc: new FormControl(''),
    statut: new FormControl(''),
    billed: new FormControl(''),
    limit: new FormControl('25')
  });

  commentForm = new FormGroup({
    origin_id: new FormControl(''),
    fk_soc: new FormControl(''),
    user: new FormControl(''),
    comment: new FormControl('')
  });

  getOrders(value): Observable<SuccessOrder>{
    // console.log("url : ", this.url+`/edisuiviapi/orders/of-user?socId=${value.socId}&status_mode=${value.status_mode}&sortfield=${value.sortfield}&sortorder=${value.sortorder}&limit=${value.limit}&page=${value.page}&DOLAPIKEY=${this.DOLAPIKEY}`);
    return this.http.get<SuccessOrder>(this.url+`/edisuiviapi/orders/of-user?socId=${value.socId}&status_mode=${value.status_mode}&sortfield=${value.sortfield}&sortorder=${value.sortorder}&limit=${value.limit}&page=${value.page}&DOLAPIKEY=${this.DOLAPIKEY}`, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}});
  }

  getOrders_v3(value): Observable<SuccessOrder>{
    // const data = {
    //   test1: 1,
    //   test2: 1.541,
    //   test3: 1684,
    //   test4: "dvbfdbfd",
    //   test5: "dfbdb",
    // };

    console.log("url : ", this.url+`/edisuiviapi/orders/of-user/v3?socId=${value.socId}&filter=${JSON.stringify(value.filter)}&status_mode=${value.status_mode}&sortfield=${value.sortfield}&sortorder=${value.sortorder}&limit=${value.limit}&page=${value.page}&DOLAPIKEY=${this.DOLAPIKEY}`);
    return this.http.get<SuccessOrder>(this.url+`/edisuiviapi/orders/of-user/v3?socId=${value.socId}&filter=${JSON.stringify(value.filter)}&status_mode=${value.status_mode}&sortfield=${value.sortfield}&sortorder=${value.sortorder}&limit=${value.limit}&page=${value.page}&DOLAPIKEY=${this.DOLAPIKEY}`, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}});
  }

  getOrderById(id): Observable<any>{
    // console.log("url : ", `${this.url}/edisuiviapi/order/id?id=${id}&DOLAPIKEY=${this.DOLAPIKEY}`);
    return this.http.get<any>(`${this.url}/edisuiviapi/order/id?id=${id}&DOLAPIKEY=${this.DOLAPIKEY}`, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}});
  }

  addOrderComment(value): Observable<any[]>{
    return this.http.post<any[]>(this.url+`/edisuiviapi/comment/order?origin_id=${value.origin_id}&message=${value.comment}&fk_soc=${value.fk_soc}&user=${value.user}&DOLAPIKEY=${this.DOLAPIKEY}`, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}});
  }

  getOrderComments(id): Observable<any>{
    // console.log("url : ", `${this.url}/edisuiviapi/order/id?id=${id}&DOLAPIKEY=${this.DOLAPIKEY}`);
    return this.http.get<any>(`${this.url}/edisuiviapi/comments/order/id?orderId=${id}&DOLAPIKEY=${this.DOLAPIKEY}`, {headers: {'Content-Type': 'application/x-www-form-urlencoded'}});
  }
}
