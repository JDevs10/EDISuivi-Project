import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { SupportService } from '../services/support/support.service';

@Component({
  selector: 'app-support-interne',
  templateUrl: './support-interne.component.html',
  styleUrls: ['./support-interne.component.css']
})
export class SupportInterneComponent implements OnInit {

  constructor(private router: Router, public supportService: SupportService) { }

  ngOnInit(): void {
  }

  async sendTicket(value){
    const data = {
      fk_project: 29, //l'id du projet Support EDISuivi que j'ai créer pour que ca soit affecté et rassemblé de dans
      type_code: "ISSUE", 
      category_code: "DEV", 
      severity_code: "NORMAL", 
      type_label: "Probléme", 
      category_label: "Développeur", 
      severity_label: "Normal", 
      fk_user_assign: 32, // c'est mon id (JL) dans la base, pour qu'il assigne le tiké a mon nom
      subject: `${value.sujetTicket}`, 
      message: `Client : ${value.companyTicket} \nMessage :  ${value.messageTicket}`, 
      date_creation: new Date().getSeconds() 
    };

    const res: any = await new Promise(async (resolved) => {
      await this.supportService.sendTicket(data).subscribe({
        next: async (data) => {
          // console.log('Successful data: ', data);
          await resolved(data);
        },
        error: async (error) => {
            // this.errorMessage = error.message;
            // console.error('There was an error! ', error);
            await resolved(null);
        }
      });
    });

    // console.log("res : ", res);

    if(res == null){
      alert("Une erreur s'est produite lors de l'envoi du ticket.");
      return;
    }

    alert("Ticket envoyé!");
    this.router.navigate(['../']);
  }

  back(){
    this.router.navigate(['../']);
  }
}
