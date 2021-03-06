import { Component, OnInit } from '@angular/core';
import { FormBuilder } from "@angular/forms";
import { Status } from '../utils/status';
import { AuthenticationService } from '../services/authentication/authentication-service.service';
import { CommandeService } from '../services/commande/commande.service';
import { SuccessOrder } from '../utils/Models/SuccessOrder';
import { $ } from 'protractor';
import { resolve } from 'dns';
import { NullVisitor } from '@angular/compiler/src/render3/r3_ast';

@Component({
  selector: 'app-suivi-commande',
  templateUrl: './suivi-commande.component.html',
  styleUrls: ['./suivi-commande.component.css']
})
export class SuiviCommandeComponent implements OnInit {

  loading = true; // var to show loading UI
  show = false;   // var to show content UI after loading
  total_cmds = "Aucun";
  current_page: number = 1;
  total_pages = 1;
  nbOFcmdInList: string = "";
  seletedCmdListValue = '12';
  statuts = [
    {label: "", value: 0}
  ];
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
    sortfield: "c.rowid",
    sortorder: "DESC",
    limit: this.commandeService.limitForm.value.limit,
    page: (this.current_page - 1),
    filter: {"ref":"","ref_client":"","client1":"","userCreated":"","assigned":"","creation_date":"","dateCommande":"","delivery_date":"","total_ht":"","total_tva":"","total_ttc":"","statut":"","billed":"","limit":"25"}
  };

  orders = [];

  constructor(public fb: FormBuilder,
    private authenticationService: AuthenticationService,
    public commandeService: CommandeService) { }

  ngOnInit(): void {
    this.loadStatus();
    // this.getOrders(this.ordersParams);
    this.getOrders_v3(this.ordersParams);
  }

  loadStatus(){
    const status = new Status();
    this.statuts = status.getStatus_v2();
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

  /*
  getOrderByLimit() {
    // console.log("limitForm ", this.commandeService.limitForm.value);
    this.ordersParams.limit = this.commandeService.filterForm.value.limit;
    this.getOrders(this.ordersParams);
  }
  */

  getOrderFromForm(value) {
    this.ordersParams.filter = value;
    this.getOrders_v3(this.ordersParams);
  }

  /*
  async getOrders(params){
    // this.showLoadingUI(true);

    const res: SuccessOrder = await new Promise(async (resolved) => {
      await this.commandeService.getOrders(params).subscribe(async (data) => {
        await resolved(data);
      });
    });

    // console.log("res : ", res);

    // console.log("orders ", res.success);
    this.total_cmds = res.success.total_cmd + "";
    this.current_page = res.success.current_page;
    this.total_pages = res.success.total_pages;
    var pageLabel = document.getElementById('page-current-all');
    pageLabel.innerText = (this.current_page + 1)+"/"+this.total_pages;
    this.orders = res.success.cmds;

    // this.showLoadingUI(false);
  }*/

  async getOrders_v3(params){
    // this.showLoadingUI(true);
    const res: SuccessOrder = await new Promise(async (resolved) => {
      await this.commandeService.getOrders_v3(params).subscribe(async (data) => {
        await resolved(data);
      });
    });

    console.log("res : ", res);

    // console.log("orders ", res.success);
    if(res.success != null){
      this.total_cmds = res.success.total_cmd + "";
      this.current_page = res.success.current_page;
      this.total_pages = res.success.total_pages;
      var pageLabel = document.getElementById('page-current-all');
      pageLabel.innerText = (this.current_page + 1)+"/"+(this.total_pages + 1);
      this.orders = res.success.cmds;
    }

    // this.showLoadingUI(false);
  }

  loadPreviousPage(){
    // this.showLoadingUI(true);
    const previousPage = (this.current_page - 1);
    if(previousPage > -1){
      // console.log("loadPreviousPage => "+previousPage);
      this.ordersParams.page = previousPage;
      
      var pageLabel = document.getElementById('page-current-all');
      pageLabel.innerText = previousPage+"/"+(this.total_pages + 1);
      this.getOrders_v3(this.ordersParams);
    }
    // this.showLoadingUI(false);
  }

  loadNextPage(){
    // this.showLoadingUI(true);

    const nextPage = (this.current_page + 1);
    if(nextPage <= this.total_pages){
      // console.log("loadPreviousPage => "+nextPage);
      this.ordersParams.page = nextPage;
      
      var pageLabel = document.getElementById('page-current-all');
      pageLabel.innerText = nextPage +"/"+(this.total_pages + 1);
      this.getOrders_v3(this.ordersParams);
    }

    // this.showLoadingUI(false);
  }
}
