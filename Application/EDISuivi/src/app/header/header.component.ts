import { Component, OnInit } from '@angular/core';
import { AuthenticationService } from '../services/authentication/authentication-service.service';


@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {

  userInfo = {
    client_name: 'NomClient',
    user_name: 'Nothing'
  }

  constructor(public authenticationService: AuthenticationService) { }

  ngOnInit(): void {
    this.getUserInfo(); 
  }

  info(){
    alert('Edisuivi information.....');
  }

  getUserInfo(){
    
    const data = this.authenticationService.getLoggedInUserInfo();
    if(data){
      this.userInfo.client_name = data.success.nom_entreprise;
      this.userInfo.user_name = data.success.identifiant_EDISuivi;
    }
  }

}
