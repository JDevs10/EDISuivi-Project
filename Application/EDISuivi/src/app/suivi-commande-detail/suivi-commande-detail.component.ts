import { Component, OnInit } from '@angular/core';
import { Router, Event, NavigationStart, NavigationEnd, NavigationError } from '@angular/router';
import { CommandeService } from '../services/commande/commande.service';

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
    userCreated: "Chargement...",
    userValidated: "Chargement...",
    deliveryAddress: "Chargement...",
    invoiceAddress: "Chargement...",
    benefitAmout: "Chargement...",
    htAmout: "Chargement...",
    tvaAmount: "Chargement...",
    ttcAmout: "Chargement...",
    comment: "Chargement...",
    anomaly: "Chargement...",
    status: "Chargement...",
    last_main_doc: {
      modulePart: "Chargement...",
      files: [
        {rowid: 0, name: "Chargement...", file: "Chargement...", size: "Chargement...", dateTime: "Chargement...", downloadLink: "Chargement..."}
      ],
    },
    lines: [
      {rowid: 0, barecode: "Chargement...", ref: "Chargement...", label: "Chargement...", volume: "Chargement...", weight: "Chargement...", qty: "Chargement...", unitPriceHT: "Chargement...", montantHT: "Chargement...", tva: "Chargement...", unitPriceTTC: "Chargement..."}
    ]
  };

  constructor(private commandeService: CommandeService, private router: Router) {
    router.events.subscribe((event: Event) => {
      if (event instanceof NavigationStart) {
          // Show loading indicator
          console.log('NavigationStart: ', event);
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
          console.log('NavigationError: ', event.error);
      }
  });
   }

  ngOnInit(): void {
    this.getOrderById();
  }

  back(){
    // this.router.navigate(['suivi-commandes']);
    window.location.href="/#/home/suivi-commandes";
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
      order_lines.push({rowid: res.order.lines[x].rowid, barecode: (res.order.lines[x].barcode == null || res.order.lines[x].barcode == "" ? "" : res.order.lines[x].barcode), ref: res.order.lines[x].ref, label: res.order.lines[x].libelle, volume: (res.order.lines[x].volume == null ? "0 " + " m3" : res.order.lines[x].volume + " m3"), weight: (res.order.lines[x].weight == null ? "0 "+ " kg" : res.order.lines[x].weight+" " + " kg"), qty: res.order.lines[x].qty, unitPriceHT: res.order.lines[x].price, montantHT: res.order.lines[x].total_ht, tva: res.order.lines[x].total_tva, unitPriceTTC: res.order.lines[x].total_ttc, warehouse: res.order.lines[x].default_warehouse});
    }

    this.order = {
      rowid: res.order.rowid,
      ref: res.order.ref,
      client1: res.order.client1,
      client2: res.order.client2,
      assign: res.order.assign,
      createDate: res.order.createDate,
      userCreated: res.order.userCreated,
      userValidated: res.order.userValidated,
      deliveryAddress: (res.order.deliveryAddress == null || res.order.deliveryAddress == "" ? "Aucune adresse de livraison trouvée." : res.order.deliveryAddress),
      invoiceAddress: (res.order.invoiceAddress == null || res.order.invoiceAddress == "" ? "Aucune adresse de facturation trouvée." : res.order.invoiceAddress),
      benefitAmout: res.order.benefitAmout,
      htAmout: res.order.htAmout,
      tvaAmount: res.order.tvaAmount,
      ttcAmout: res.order.ttcAmout,
      comment: (res.order.note_public == null || res.order.note_public == "" ? "Aucun commentaire." : res.order.note_public),
      anomaly: (res.order.note_private == null || res.order.note_private == "" ? "Aucune anomalie détectée." : res.order.note_private),
      status: res.order.status,
      last_main_doc: {
        modulePart: res.order.last_main_doc.modulePart,
        files: [
          {rowid: res.order.last_main_doc.files.rowid, name: res.order.last_main_doc.files.name, file: res.order.last_main_doc.files.file, size: res.order.last_main_doc.files.size, dateTime: res.order.last_main_doc.files.dateTime, downloadLink: res.order.last_main_doc.files.dd}
        ],
      },
      lines: order_lines
    };

    this.order_lines_length = this.order.lines.length;

    console.log("this.order ", this.order);
    this.endFirstLoad = true;
    // this.showLoadingUI(false);
  }


  download_bl(){
    alert("Cette fonctionnalité sera disponible dans la prochaine version.\nNous nous excusons pour la gêne occasionnée.\n\nTeam BDC.");
  }

  download_invoice(){
    alert("Cette fonctionnalité sera disponible dans la prochaine version.\nNous nous excusons pour la gêne occasionnée.\n\nTeam BDC.");
  }

}
