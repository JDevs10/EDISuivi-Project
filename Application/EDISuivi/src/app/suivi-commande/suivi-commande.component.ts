import { Component, OnInit } from '@angular/core';
import { FormBuilder } from "@angular/forms";
import { Status } from '../utils/status';
import { AuthenticationService } from '../services/authentication/authentication-service.service';
import { CommandeService } from '../services/commande/commande.service';
import { SuccessOrder } from '../utils/Models/SuccessOrder';
import { $ } from 'protractor';
import { resolve } from 'dns';

@Component({
  selector: 'app-suivi-commande',
  templateUrl: './suivi-commande.component.html',
  styleUrls: ['./suivi-commande.component.css']
})
export class SuiviCommandeComponent implements OnInit {

  loading = true; // var to show loading UI
  show = false;   // var to show content UI after loading
  total_cmds = "NaN";
  current_page: number = 1;
  total_pages = 1;
  nbOFcmdInList: string = "";
  seletedCmdListValue = '12';
  tt_pages_ = [
    {id: 0, value: 25}, 
    {id: 1, value: 50}, 
    {id: 2, value: 75}, 
    {id: 3, value: 100}
  ];

  user = this.authenticationService.getLoggedInUserInfo().success;
  ordersParams = {
    socId: this.user.socid,
    status_mode: 1,
    sortfield: "cmd.rowid",
    sortorder: "DESC",
    limit: this.commandeService.limitForm.value.limit,
    page: (this.current_page - 1),
  };

  orders = [
    {rowid: 0, ref: "Chargement", platforme: "Chargement", date_creation: 123456789, date_livraison: 123456789, zip: 99999, town: "Chargement", billed: 0, total_ttc: "Chargement", statut: new Status().getLabelById(1)},
  ];

  constructor(public fb: FormBuilder,
    private authenticationService: AuthenticationService,
    public commandeService: CommandeService) { }

  ngOnInit(): void {
    this.getOrders(this.ordersParams);
  }

  showLoadingUI(value){
    var loading = document.getElementById('main-content-loading');
    var content = document.getElementById('main-content-s-cmd');

    if(value){
      loading.style.display = "block";
      content.style.display = "none";
    }else{
      loading.style.display = "none";
      content.style.display = "block";
    }
    this.loading = value;
    this.show = !value;
  }

  getOrderByLimit() {
    console.log("limitForm ", this.commandeService.limitForm.value);
    this.ordersParams.limit = this.commandeService.limitForm.value.limit;
    this.getOrders(this.ordersParams);
  }

  async getOrders(params){
    // this.showLoadingUI(true);

    const res: SuccessOrder = await new Promise(async (resolved) => {
      await this.commandeService.getOrders(params).subscribe(async (data) => {
        await resolved(data);
      });
    });

    console.log("res : ", res);

    console.log("orders ", res.success);
    this.total_cmds = res.success.total_cmd + "";
    this.current_page = res.success.current_page;
    this.total_pages = res.success.total_pages;
    var pageLabel = document.getElementById('page-current-all');
    pageLabel.innerText = (this.current_page + 1)+"/"+this.total_pages;
    this.orders = res.success.cmds;

    // this.showLoadingUI(false);
  }

  loadPreviousPage(){
    // this.showLoadingUI(true);
    const previousPage = (this.current_page - 1);
    if(previousPage > -1){
      console.log("loadPreviousPage => "+previousPage);
      this.ordersParams.page = previousPage;
      
      var pageLabel = document.getElementById('page-current-all');
      pageLabel.innerText = previousPage+"/"+this.total_pages;
      this.getOrders(this.ordersParams);
    }
    // this.showLoadingUI(false);
  }

  loadNextPage(){
    // this.showLoadingUI(true);

    const nextPage = (this.current_page + 1);
    if(nextPage < this.total_pages){
      console.log("loadPreviousPage => "+nextPage);
      this.ordersParams.page = nextPage;
      
      var pageLabel = document.getElementById('page-current-all');
      pageLabel.innerText = nextPage +"/"+this.total_pages;
      this.getOrders(this.ordersParams);
    }

    // this.showLoadingUI(false);
  }
}
