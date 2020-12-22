import { Component, OnInit } from '@angular/core';
import { Chart } from 'node_modules/chart.js';
import { ChartsService } from '../services/charts/charts.service';
import { AuthenticationService } from '../services/authentication/authentication-service.service';
import * as Chartist from 'node_modules/chartist';
import { ChartData } from '../utils/Models/ChartData';

@Component({
  selector: 'app-my-chart',
  templateUrl: './my-chart.component.html',
  styleUrls: ['./my-chart.component.css']
})
export class MyChartComponent implements OnInit {
  
  _data_ = {
    type: "line", 
    labels: ['Janvier', 'Février', 'Mars', 'Avril', 'May', 'Jun', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
    title: ["xxxx","xxxx"],
    data: [
      [7,9,2,6,4,4,1,5,0,6,0,0],
      [4,1,2,6,9,5,1,0,0,0,9,0]
    ],
    backgroundColor: [
      ['rgba(174, 0, 255, 0.7)',],
      ['rgba(0, 182, 255, 0.7)',],
    ],
    borderColor: [
      ['#ae00ff',],
      ['#00b6ff',]
    ]
  }

  // events
  public chartClicked(e:any):void {
    console.log(e);
  }

  public chartHovered(e:any):void {
    console.log(e);
  }

  constructor(private authenticationService: AuthenticationService, 
    private chartsService: ChartsService) { }

  ngOnInit(): void {
    this.getLoadData();

  }

  async getLoadData(){
    const htmlLabel = document.getElementById('my-global-chart-label');
    htmlLabel.innerText = "Nombre de commande par mois";
    htmlLabel.style.color = "white";
    htmlLabel.style.paddingLeft = "30px";

    const user =  await this.authenticationService.getLoggedInUserInfo();
    const res: ChartData = await new Promise(async (resolved) => {
      await this.chartsService.getNbOrdersByMonthOfTwoYears(user.success.socid).subscribe(async (data) => {
        await resolved(data);
      });
    });
    
    if(res == null){
      console.log('loading nb cmd chart failed');
      return;
    }

    this._data_.title[0] = res.success.title[0];
    this._data_.title[1] = res.success.title[1];
    this._data_.data[0] = res.success.data[0];
    this._data_.data[1] = res.success.data[1];

    const myChart = new Chart("myChart", {
      type: this._data_.type,
      data: {
          labels: this._data_.labels,
          datasets: [{
              label: this._data_.title[0],
              data: this._data_.data[0],
              backgroundColor: this._data_.backgroundColor[0],
              borderColor: this._data_.borderColor[0],
              borderWidth: 1
          },
          {
            label: this._data_.title[1],
            data: this._data_.data[1],
            backgroundColor: this._data_.backgroundColor[1],
            borderColor: this._data_.borderColor[1],
            borderWidth: 1
        }]
      },
      options: {
        legend: {
          labels: {
              fontColor: "white",
              fontSize: 14
          }
        },
        scales: {
            yAxes: [{
                ticks: {
                    fontColor: "white",
                    fontSize: 14,
                    stepSize: 1,
                    beginAtZero: true
                }
            }],
            xAxes: [{
                ticks: {
                    fontColor: "white",
                    fontSize: 14,
                    stepSize: 1,
                    beginAtZero: true
                }
            }]
        }
      }
  });
  }

}
