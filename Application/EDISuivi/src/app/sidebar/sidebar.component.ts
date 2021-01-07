import { Component, OnInit } from '@angular/core';

declare interface RouteInfo {
  path: string;
  title: string;
  icon: string;
  class: string;
}
export const ROUTES: RouteInfo[] = [
  { path: 'dashbord', title: 'Dashboard',  icon: 'far fa-analytics', class: '' },
  { path: 'suivi-commandes', title: 'Suivi Commandes',  icon:'fas fa-box-open', class: '' },
  { path: '/suivi-bon-livraison', title: 'Suivi Bon de Livraison',  icon:'fas fa-truck-loading', class: '' },
  { path: 'suivi-factures', title: 'Suivi Factures',  icon:'fas fa-receipt', class: '' },
  { path: 'suivi-stocks', title: 'Suivi Stocks',  icon:'fas fa-boxes', class: '' },
  { path: 'support-interne', title: 'Support',  icon:'fas fa-headset', class: '' },
  { path: 'settings', title: 'Paramètres',  icon:'fas fa-cogs', class: '' },
];
// { path: '/upgrade', title: 'Version PRO',  icon:'fas fa-spaceship', class: 'active active-pro' }

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css']
})
export class SidebarComponent implements OnInit {
  menuItems: any[];

  constructor() { }

  ngOnInit(): void {
    this.menuItems = ROUTES.filter(menuItem => menuItem);
  }

  isMobileMenu() {
    if ( window.innerWidth > 991) {
        return false;
    }
    return true;
};
}
