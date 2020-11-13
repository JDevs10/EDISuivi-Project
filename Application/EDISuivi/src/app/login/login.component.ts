import { Component, OnInit } from '@angular/core';
import { AuthenticationService } from '../services/authentication/authentication-service.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {

  constructor(public authenticationService: AuthenticationService) { }

  ngOnInit(): void {
    if(this.authenticationService.loggedIn()){
      window.location.href="home";
    }
  }

  tryLogin(value){
    this.authenticationService.doLogin(value);
  }

}
