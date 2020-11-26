import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from '../app/login/login.component';
import { DashbordComponent } from '../app/dashbord/dashbord.component';
import { AuthGuard } from './auth.guard';
import { SupportInterneComponent } from './support-interne/support-interne.component';
import { SupportExterneComponent } from './support-externe/support-externe.component';
import { SuiviCommandeComponent } from './suivi-commande/suivi-commande.component';
import { SuiviCommandeDetailComponent } from './suivi-commande-detail/suivi-commande-detail.component';
import { HomeComponent } from './home/home.component';
import { SuiviFacturesComponent } from './suivi-factures/suivi-factures.component';
import { SuiviStocksComponent } from './suivi-stocks/suivi-stocks.component';
import { SettingsComponent } from './settings/settings.component';

const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full'},
  { path: 'login', component: LoginComponent},
  { path: 'home', component: HomeComponent, pathMatch : 'prefix', canActivate: [AuthGuard],
    children: [
      { path: '', redirectTo: 'dashbord', pathMatch: 'full' },
      { path: 'dashbord', component: DashbordComponent, canActivate: [AuthGuard] },
      { path: 'suivi-commandes', component: SuiviCommandeComponent, canActivate: [AuthGuard]},
      { path: 'suivi-commande-detail/:id', pathMatch: 'full', component: SuiviCommandeDetailComponent, canActivate: [AuthGuard] },
      { path: 'suivi-factures', component: SuiviFacturesComponent, canActivate: [AuthGuard] },
      { path: 'suivi-stocks', component: SuiviStocksComponent, canActivate: [AuthGuard] },
      { path: 'support-interne', component: SupportInterneComponent, canActivate: [AuthGuard]},
      { path: 'settings', component: SettingsComponent, canActivate: [AuthGuard] },
    ]
  },
  { path: 'support-externe', component: SupportExterneComponent },
];

@NgModule({
  imports: [
    RouterModule.forRoot(routes ,{useHash: true})
  ],
  exports: [RouterModule]
})
export class AppRoutingModule { }
