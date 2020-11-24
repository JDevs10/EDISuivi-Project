import { Component, OnInit } from '@angular/core';
import { NavigationEnd, Router } from '@angular/router';
import { CommandeService } from '../services/commande/commande.service';

@Component({
  selector: 'app-suivi-commande-detail',
  templateUrl: './suivi-commande-detail.component.html',
  styleUrls: ['./suivi-commande-detail.component.css']
})
export class SuiviCommandeDetailComponent implements OnInit {

  loading = true; // var to show loading UI
  show = false;   // var to show content UI after loading

  order = {
    rowid: 0,
    ref: "",
    client1: "",
    client2: "",
    assign: "",
    deliveryAddress: "",
    invoiceAddress: "",
    benefitAmout: "",
    htAmout: "",
    tvaAmount: "",
    ttcAmout: "x",
    comment: "Aucun commentaire",
    anomaly: "Aucune anomalie détectée",
    status: "",
    last_main_doc: {
      modulePart: "commande",
      files: [
        // {rowid: 0, name: "CMD201029-000414.pdf", file: "CMD201029-000414/CMD201029-000414.pdf", size: "40 ko", dateTime: "05/11/2020 11:43", downloadLink: "#"}
      ],
    },
    lines: [
      // {rowid: 0, barecode: "5000112554359", ref: "Chargement", label: "Chargement", volume: "25 m3", weight: "45 kg", qty: 1234, unitPriceHT: 12.65, montantHT: 15610.1, tva: "5.5%", unitPriceTTC: 16468.65}
    ]
  };

  constructor(private commandeService: CommandeService, private router: Router) {
    router.changes.subscribe((val) => {
      console.log('router : ' , val instanceof NavigationEnd);
      // this.getOrderById();
    });
   }

  ngOnInit(): void {
    this.getOrderById();
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
      this.router.navigate(['../']);
      return;
    }

    const order_lines = [];
    for(let x=0; x < res.order.lines.length; x++){
      order_lines.push({rowid: res.order.lines[x].rowid, barecode: (res.order.lines[x].barcode == null || res.order.lines[x].barcode == "" ? "" : res.order.lines[x].barcode), ref: res.order.lines[x].ref, label: res.order.lines[x].libelle, volume: (res.order.lines[x].volume == null ? "0 " + res.order.lines[x].volume_units : res.order.lines[x].volume + " " + res.order.lines[x].volume_units), weight: (res.order.lines[x].weight == null ? "0 "+ res.order.lines[x].weight_units : res.order.lines[x].weight+" " + res.order.lines[x].weight_units), qty: res.order.lines[x].qty, unitPriceHT: res.order.lines[x].price, montantHT: res.order.lines[x].total_ht, tva: res.order.lines[x].total_tva, unitPriceTTC: res.order.lines[x].total_ttc, warehouse: "XXXXX"});
    }

    this.order = {
      rowid: res.order.id,
      ref: res.order.ref,
      client1: res.order.client1,
      client2: res.order.client2,
      assign: res.order.assign,
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
          {rowid: res.order.last_main_doc.files.rowid, name: res.order.last_main_doc.files.name, file: res.order.last_main_doc.files.file, size: res.order.last_main_doc.files.size, dateTime: res.order.last_main_doc.files.dateTime, downloadLink: res.order.last_main_doc.files.downloadLink}
        ],
      },
      lines: order_lines
    };

    console.log("this.order ", this.order);
    // this.showLoadingUI(false);
  }


  download_bl(){
    alert("Cette fonctionnalité sera disponible dans la prochaine version.\nNous nous excusons pour la gêne occasionnée.\n\nTeam BDC.");
  }

  download_invoice(){
    alert("Cette fonctionnalité sera disponible dans la prochaine version.\nNous nous excusons pour la gêne occasionnée.\n\nTeam BDC.");
  }

}
