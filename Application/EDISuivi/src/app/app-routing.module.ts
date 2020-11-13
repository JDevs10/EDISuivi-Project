import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from '../app/login/login.component';
import { DashbordComponent } from '../app/dashbord/dashbord.component';
import { AuthGuard } from './auth.guard';
import { SupportInterneComponent } from './support-interne/support-interne.component';
import { SuiviCommandeComponent } from './suivi-commande/suivi-commande.component';
import { SuiviCommandeDetailComponent } from './suivi-commande-detail/suivi-commande-detail.component';
import { HomeComponent } from './home/home.component';


const routes: Routes = [
  { path: '', redirectTo: '/login', pathMatch: 'full'},
  { path: 'login', component: LoginComponent},
  { path: 'home', component: HomeComponent, canActivate: [AuthGuard],
    children: [
      { path: '', redirectTo: 'dashbord', pathMatch: 'full' },
      { path: 'dashbord', component: DashbordComponent },
      { path: 'suivi-commandes', component: SuiviCommandeComponent },
      { path: 'suivi-dommande-detail/:id', component: SuiviCommandeDetailComponent },
      { path: 'support-interne', component: SupportInterneComponent }
    ]
  },
];

@NgModule({
  imports: [
    RouterModule.forRoot(routes)
  ],
  exports: [RouterModule]
})
export class AppRoutingModule { }
