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
    const res: any = await new Promise(async (resolved) => {
      await this.supportService.sendTicket(value).subscribe(async (data)=>{
        await resolved(data);
      });
    });

    if(res == null){
      return;
    }
  }

  back(){
    this.router.navigate(['../']);
  }
}
