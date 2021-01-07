import { Component, OnInit } from '@angular/core';
import { Router, Event, NavigationStart, NavigationEnd, NavigationError } from '@angular/router';
import { CommandeService } from '../services/commande/commande.service';
import { DownloadService } from '../services/download/dowload.service';
import { DownloadFile } from '../utils/Models/DownloadFile';

@Component({
  selector: 'app-suivi-commande-detail',
  templateUrl: './suivi-commande-detail.component.html',
  styleUrls: ['./suivi-commande-detail.component.css']
})
export class SuiviCommandeDetailComponent implements OnInit {

  loading = true; // var to show loading UI
  show = false;   // var to show content UI after loading
  endFirstLoad = false;

  order_lines_length = 0;
  order = {
    rowid: 0,
    ref: "Chargement...",
    client1: "Chargement...",
    client2: "Chargement...",
    assign: "Chargement...",
    createDate: "Chargement...",
    validDate: "Chargement...",
    deliveryDate: "Chargement...",
    receptionDate: "Chargement...",
    userCreated: "Chargement...",
    userValidated: "Chargement...",
    deliveryAddress: "Chargement...",
    invoiceAddress: "Chargement...",
    htAmout: "Chargement...",
    tvaAmount: "Chargement...",
    ttcAmout: "Chargement...",
    comment: "Chargement...",
    anomaly: "Chargement...",
    status: "Chargement...",
    documents: {
      files: [
        {
          rowid: -99, 
          type: "Chargement...", 
          filepath: "Chargement...", 
          filename: "Chargement...", 
          original_file: "Chargement...", 
          date_c: "Chargement...", 
          date_m: "Chargement...", 
          data: {
            filename: "Chargement...",
            contentType: "Chargement...",
            filectime: "Chargement...",
            filemtime: "Chargement...",
            filesize: "Chargement...",
            content: "Chargement...",
            encoding: "Chargement..."
          }
          
        }
      ],
    },
    lines: [
      // {rowid: 0, barecode: "Chargement...", ref: "Chargement...", label: "Chargement...", volume: "Chargement...", weight: "Chargement...", qty: "Chargement...", unitPriceHT: "Chargement...", montantHT: "Chargement...", tva: "Chargement...", unitPriceTTC: "Chargement..."}
    ]
  };

  constructor(private commandeService: CommandeService, private downloadService: DownloadService, private router: Router) {
    router.events.subscribe((event: Event) => {
      if (event instanceof NavigationStart) {
          // Show loading indicator
          // console.log('NavigationStart: ', event);
      }

      if (event instanceof NavigationEnd) {
          // Hide loading indicator
          // console.log('NavigationEnd: ', event);
          if(this.endFirstLoad){
            this.getOrderById();
          }
      }

      if (event instanceof NavigationError) {
          // Hide loading indicator

          // Present error to user
          // console.log('NavigationError: ', event.error);
      }
    });
  }


  ngOnInit(): void {
    this.getOrderById();
  }

  back(){
    this.router.navigate(['../home/suivi-commandes']);
    // window.location.href="/#/home/suivi-commandes";
  }

  showLoadingUI(value){
    var loading = document.getElementById('main-content-loading');
    var content = document.getElementById('main-content-d-cmd');

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

  async getOrderById(){
    // this.showLoadingUI(true);

    if(this.router.url.split("/")[3] != null){
      const res: any = await new Promise(async (resolved) => {
        await this.commandeService.getOrderById(this.router.url.split("/")[3]).subscribe(async (data) => {
          await resolved(data);
        });
      });
  
      console.log("order by id", res);
      // this.order = res.success.cmds;
  
      if(res == null || res.status == "error"){
        alert("Aucune commande trouvé!");
        // return in suivi-cmd
        // this.showLoadingUI(false);
        // this.router.navigate(['../']);
        this.endFirstLoad = true;
        return;
      }
  
      const order_lines = [];
      for(let x=0; x < res.order.lines.length; x++){
        order_lines.push({rowid: res.order.lines[x].rowid, barecode: (res.order.lines[x].barcode == null || res.order.lines[x].barcode == "" ? "" : res.order.lines[x].barcode), ref: res.order.lines[x].ref, label: res.order.lines[x].libelle, description: res.order.lines[x].description, volume: (res.order.lines[x].volume == null ? "0 " + " m3" : res.order.lines[x].volume + " m3"), weight: (res.order.lines[x].weight == null ? "0 "+ " kg" : res.order.lines[x].weight+" " + " kg"), qty: res.order.lines[x].qty, unitPriceHT: res.order.lines[x].price, montantHT: res.order.lines[x].total_ht, tva: res.order.lines[x].total_tva, unitPriceTTC: res.order.lines[x].total_ttc, warehouse: res.order.lines[x].default_warehouse});
      }

      const order_documents = res.order.documents.files;

      /*
      for(let x=0; x < res.order.documents.files.length; x++){
        console.log("order_documents :", res.order.documents);
        console.log("res.order.documents.files[x].type :", res.order.documents.files[x].type);

        const obj = {
          rowid: res.order.documents.files[x].rowid, 
          type: res.documents.files[x].type, 
          // filepath: res.documents.files[x].filepath, 
          // filename: res.documents.files[x].filename, 
          // original_file: res.order.lines[x].original_file, 
          // date_c: res.documents.files[x].date_c, 
          // date_m: res.documents.files[x].date_m, 
          // data: {
          //   filename: res.documents.files[x].data.filename,
          //   contentType: res.documents.files[x].data.contentType,
          //   filectime: res.documents.files[x].data.filectime,
          //   filemtime: res.documents.files[x].data.filemtime,
          //   filesize: res.documents.files[x].data.filesize,
          //   content: res.documents.files[x].data.content,
          //   encoding: res.documents.files[x].data.encoding
          // }
          
        }
        console.log("order_documents__ :", obj);
        order_documents.push(obj);
      }
      */
  
      // set delivery adress
      // check in extrafields_data for custom fields
      let deliveryAddress;
      let receptionDate;
      if(res.order.extrafields_data != null){
        deliveryAddress = (res.order.extrafields_data.deliveryAddress_custom == null ? "Aucune adresse de livraison trouvée." : res.order.extrafields_data.deliveryAddress_custom);
        receptionDate = (res.order.extrafields_data.receptionDate_custom == null ? "" : res.order.extrafields_data.receptionDate_custom);
      }else{
        deliveryAddress = "Aucune adresse de livraison trouvée.";
        receptionDate = "";
      }

      console.log("start order :");
  
      this.order = {
        rowid: res.order.rowid,
        ref: res.order.ref,
        client1: res.order.client1,
        client2: res.order.client2,
        assign: res.order.assign,
        createDate: res.order.createDate,
        validDate: res.order.validDate,
        deliveryDate: res.order.deliveryDate,
        receptionDate: receptionDate,
        userCreated: res.order.userCreated,
        userValidated: res.order.userValidated,
        deliveryAddress: deliveryAddress,
        invoiceAddress: (res.order.invoiceAddress == null || res.order.invoiceAddress == "" ? "Aucune adresse de facturation trouvée." : res.order.invoiceAddress),
        htAmout: res.order.htAmout,
        tvaAmount: res.order.tvaAmount,
        ttcAmout: res.order.ttcAmout,
        comment: (res.order.comment == null || res.order.comment == "" ? "Aucun commentaire." : res.order.comment),
        anomaly: (res.order.anomaly == null || res.order.anomaly == "" ? "Aucune anomalie détectée." : res.order.anomaly),
        status: res.order.status,
        documents: {
          files: order_documents,
        },
        lines: order_lines
      };
  
      this.order_lines_length = this.order.lines.length;
  
      console.log("this.order ", this.order);
      this.endFirstLoad = true;
      // this.showLoadingUI(false);

    }
  }


  async download_bl(val){
    const downloadedFileData: DownloadFile = await new Promise(async (resolved) => {
      await this.downloadService.downloadOrderPdf({modulepart: val.type, original_file: val.original_file}).subscribe(async (data) => {
        await resolved(data);
      });
    });

    const fileName = downloadedFileData.filename;
    const fileType = downloadedFileData.contentType;
    const data = downloadedFileData.content;

    const e = document.createEvent('MouseEvents'),
    a = document.createElement('a');
    a.download = fileName;
    //a.href = window.URL.createObjectURL(blob);      //==> not working good
    a.href = `data:${fileType};base64,${data}`;       // ==> works good
    a.dataset.downloadurl = [fileType, a.download, a.href].join(':');
    console.log("downloadurl", a.dataset.downloadurl);
    e.initEvent('click', true, false);
    a.dispatchEvent(e);
  }

  download_invoice(){
    alert("Cette fonctionnalité sera disponible dans la prochaine version.\nNous nous excusons pour la gêne occasionnée.\n\nTeam BDC.");
  }

}
