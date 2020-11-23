import { Component, OnInit } from '@angular/core';
import { CommandeService } from '../services/commande/commande.service';

@Component({
  selector: 'app-suivi-commande-detail',
  templateUrl: './suivi-commande-detail.component.html',
  styleUrls: ['./suivi-commande-detail.component.css']
})
export class SuiviCommandeDetailComponent implements OnInit {

  order = {
    rowid: 14,
    ref: "CMD201029-000414",
    client1: "client1",
    client2: "client2",
    assign: "XXXXXXXX",
    deliveryAddress: "123 rue de la victoir, \nParis 75014, \nFrance",
    invoiceAddress: "123 rue de la victoir, \nParis 75014, \nFrance",
    benefitAmout: "xxxx €",
    htAmout: "421.2 €",
    tvaAmount: "xx.x €",
    ttcAmout: "xx.x €",
    comment: "Aucun commentaire",
    anomaly: "Aucun anomalie",
    status: "Order In process",
    last_main_doc: {
      modulePart: "commande",
      files: [
        {rowid: 0, name: "CMD201029-000414.pdf", file: "CMD201029-000414/CMD201029-000414.pdf", size: "40 ko", dateTime: "05/11/2020 11:43"}
      ],
    },
    lines: [
      {rowid: 0, barecode: "5000112554359", ref: "Chargement", label: "Chargement", volume: "25 m3", weight: "45 kg", qty: 1234, unitPriceHT: 12.65, montantHT: 15610.1, tva: "5.5%", unitPriceTTC: 16468.65},
      {rowid: 0, barecode: "5000112554359", ref: "Chargement", label: "Chargement", volume: "25 m3", weight: "45 kg", qty: 1234, unitPriceHT: 12.65, montantHT: 15610.1, tva: "5.5%", unitPriceTTC: 16468.65},
      {rowid: 0, barecode: "5000112554359", ref: "Chargement", label: "Chargement", volume: "25 m3", weight: "45 kg", qty: 1234, unitPriceHT: 12.65, montantHT: 15610.1, tva: "5.5%", unitPriceTTC: 16468.65},
      {rowid: 0, barecode: "5000112554359", ref: "Chargement", label: "Chargement", volume: "25 m3", weight: "45 kg", qty: 1234, unitPriceHT: 12.65, montantHT: 15610.1, tva: "5.5%", unitPriceTTC: 16468.65},
      {rowid: 0, barecode: "5000112554359", ref: "Chargement", label: "Chargement", volume: "25 m3", weight: "45 kg", qty: 1234, unitPriceHT: 12.65, montantHT: 15610.1, tva: "5.5%", unitPriceTTC: 16468.65},
      {rowid: 0, barecode: "5000112554359", ref: "Chargement", label: "Chargement", volume: "25 m3", weight: "45 kg", qty: 1234, unitPriceHT: 12.65, montantHT: 15610.1, tva: "5.5%", unitPriceTTC: 16468.65},
      {rowid: 0, barecode: "5000112554359", ref: "Chargement", label: "Chargement", volume: "25 m3", weight: "45 kg", qty: 1234, unitPriceHT: 12.65, montantHT: 15610.1, tva: "5.5%", unitPriceTTC: 16468.65},
    ]
  };

  constructor(private commandeService: CommandeService) { }

  ngOnInit(): void {
    this.getOrderById();
  }

  async getOrderById(){
    const res: any = await new Promise(async (resolved) => {
      await this.commandeService.getOrderById(672).subscribe(async (data) => {
        await resolved(data);
      });
    });

    console.log("order by id", res);
    // this.order = res.success.cmds;

    const order_lines = [];
    for(let x=0; x < res.lines.count; x++){
      order_lines.push({rowid: res.lines[x].id, barecode: "", ref: res.lines[x].ref, label: res.lines[x].libelle, volume: (res.lines[x].volume == null ? 0 : res.lines[x].volume), weight: (res.lines[x].weight == null ? 0 : res.lines[x].weight+" m3"), qty: res.lines[x].qty, unitPriceHT: res.lines[x].total_ht, montantHT: (res.lines[x].qty * res.lines[x].total_ht), tva: res.lines[x].total_tva+"%", unitPriceTTC: (res.lines[x].qty * res.lines[x].total_ttc)});
    }

    this.order = {
      rowid: res.id,
      ref: res.ref,
      client1: "client1",
      client2: "client2",
      assign: "XXXXXXXX",
      deliveryAddress: "123 rue de la victoir, \nParis 75014, \nFrance",
      invoiceAddress: "123 rue de la victoir, \nParis 75014, \nFrance",
      benefitAmout: "xxxx €",
      htAmout: "421.2 €",
      tvaAmount: "xx.x €",
      ttcAmout: "xx.x €",
      comment: res.note_public,
      anomaly: res.note_private,
      status: "Order In process",
      last_main_doc: {
        modulePart: "commande",
        files: [
          {rowid: 0, name: "CMD201029-000414.pdf", file: "CMD201029-000414/CMD201029-000414.pdf", size: "40 ko", dateTime: "05/11/2020 11:43"}
        ],
      },
      lines: order_lines
    };
  }


  download_(){
    
  }

}
