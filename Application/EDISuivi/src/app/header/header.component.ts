import { Component, OnInit } from '@angular/core';
import { AuthenticationService } from '../services/authentication/authentication-service.service';


@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
  styleUrls: ['./header.component.css']
})
export class HeaderComponent implements OnInit {

  client_name: string = 'NomClient'

  constructor(public authenticationService: AuthenticationService) { }

  ngOnInit(): void {
  }

  test(){
    console.log('clicked');
  }

}
