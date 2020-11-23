import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { AuthenticationService } from '../services/authentication/authentication-service.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {

  constructor(private authenticationService: AuthenticationService, route: ActivatedRoute) { }

  ngOnInit(): void {
    // if(this.authenticationService.loggedIn()){
    //   window.location.href="#/home/dashbord";
    // }
  }

}
