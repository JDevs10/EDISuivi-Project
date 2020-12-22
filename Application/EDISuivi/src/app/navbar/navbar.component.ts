import { Component, OnInit, ElementRef } from '@angular/core';
import { ROUTES } from '../sidebar/sidebar.component';
import {Location, LocationStrategy, PathLocationStrategy} from '@angular/common';
import { Router } from '@angular/router';
import Chart from 'chart.js';
import { AuthenticationService } from '../services/authentication/authentication-service.service';

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.css']
})
export class NavbarComponent implements OnInit {
  private listTitles: any[];
  location: Location;
  mobile_menu_visible: any = 0;
  private toggleButton: any;
  private sidebarVisible: boolean;
  public userInfo = {
    client_name: 'NomClient',
    user_name: 'Nothing',
    last_connexion: '',
    userIsAdmin: 'Non'
  }

  public isCollapsed = false;

  constructor(location: Location,  private element: ElementRef, private router: Router, public authenticationService: AuthenticationService) {
    this.location = location;
        this.sidebarVisible = false;
  }

  ngOnInit(): void {
    this.getUserInfo();
    this.listTitles = ROUTES.filter(listTitle => listTitle);
      const navbar: HTMLElement = this.element.nativeElement;
      this.toggleButton = navbar.getElementsByClassName('navbar-toggler')[0];
      this.router.events.subscribe((event) => {
        this.sidebarClose();
         var $layer: any = document.getElementsByClassName('close-layer')[0];
         if ($layer) {
           $layer.remove();
           this.mobile_menu_visible = 0;
         }
    });
  }

  getUserInfo(){
    
    const data = this.authenticationService.getLoggedInUserInfo();
    if(data){
      this.userInfo.client_name = data.success.nom_entreprise;
      this.userInfo.user_name = data.success.identifiant_EDISuivi;
      const dateNoSeconds = data.success.last_connexion.split(':');
      this.userInfo.last_connexion = dateNoSeconds[0]+":"+dateNoSeconds[1]+":"+dateNoSeconds[2];

      if(data.success.identifiant_EDISuivi == "admin" || data.success.identifiant_EDISuivi == "JL"){
        this.userInfo.userIsAdmin = "Oui";
      }
    }
  }


  collapse(){
    console.log('before', this.isCollapsed);
      this.isCollapsed = !this.isCollapsed;
      console.log('after', this.isCollapsed);

      const panel = document.getElementById("panel-header-empty");
      const navbar_ = document.getElementById("collapseExample");
      if (!this.isCollapsed) {
        document.getElementById("li-space").classList.add("nav-item");
        document.getElementById("li-space").innerHTML = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        navbar_.classList.remove('show');
        panel.style.height = "65px";
        // navbar_.style.backgroundColor = "transparent";
      }else{
        document.getElementById("li-space").classList.remove("nav-item");
        document.getElementById("li-space").innerHTML = "";
        navbar_.classList.add('show');
        // navbar_.style.backgroundColor = "#000000";
        navbar_.style.color = "#000000";
        panel.style.height = "200px"
      }
    }

    sidebarOpen() {
        const toggleButton = this.toggleButton;
        const mainPanel =  <HTMLElement>document.getElementsByClassName('main-panel')[0];
        const html = document.getElementsByTagName('html')[0];
        if (window.innerWidth < 991) {
          mainPanel.style.position = 'fixed';
        }

        setTimeout(function(){
            toggleButton.classList.add('toggled');
        }, 500);

        html.classList.add('nav-open');

        this.sidebarVisible = true;
    };
    sidebarClose() {
        const html = document.getElementsByTagName('html')[0];
        this.toggleButton.classList.remove('toggled');
        const mainPanel =  <HTMLElement>document.getElementsByClassName('main-panel')[0];

        if (window.innerWidth < 991) {
          setTimeout(function(){
            mainPanel.style.position = '';
          }, 500);
        }
        this.sidebarVisible = false;
        html.classList.remove('nav-open');
    };
    sidebarToggle() {
        // const toggleButton = this.toggleButton;
        // const html = document.getElementsByTagName('html')[0];
        var $toggle = document.getElementsByClassName('navbar-toggler')[0];

        if (this.sidebarVisible === false) {
            this.sidebarOpen();
        } else {
            this.sidebarClose();
        }
        const html = document.getElementsByTagName('html')[0];

        if (this.mobile_menu_visible == 1) {
            // $('html').removeClass('nav-open');
            html.classList.remove('nav-open');
            if ($layer) {
                $layer.remove();
            }
            setTimeout(function() {
                $toggle.classList.remove('toggled');
            }, 400);

            this.mobile_menu_visible = 0;
        } else {
            setTimeout(function() {
                $toggle.classList.add('toggled');
            }, 430);

            var $layer = document.createElement('div');
            $layer.setAttribute('class', 'close-layer');


            if (html.querySelectorAll('.main-panel')) {
                document.getElementsByClassName('main-panel')[0].appendChild($layer);
            }else if (html.classList.contains('off-canvas-sidebar')) {
                document.getElementsByClassName('wrapper-full-page')[0].appendChild($layer);
            }

            setTimeout(function() {
                $layer.classList.add('visible');
            }, 100);

            $layer.onclick = function() { //asign a function
              html.classList.remove('nav-open');
              this.mobile_menu_visible = 0;
              $layer.classList.remove('visible');
              setTimeout(function() {
                  $layer.remove();
                  $toggle.classList.remove('toggled');
              }, 400);
            }.bind(this);

            html.classList.add('nav-open');
            this.mobile_menu_visible = 1;

        }
    };

    getTitle(){
      var titlee = this.location.prepareExternalUrl(this.location.path());
      
      if(titlee.charAt(0) === '#'){
          titlee = titlee.slice( 2 );
      }
      const titlee_ = titlee.split('/');
      titlee = titlee.split('/').pop();

      if(titlee_[titlee_.length-2] == "suivi-commande-detail"){
        return "Commande "+titlee;
      }

      for(var item = 0; item < this.listTitles.length; item++){
          if(this.listTitles[item].path === titlee){
              return this.listTitles[item].title;
          }
      }
      return '';
    }
}
