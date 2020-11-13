import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppComponent } from './app.component';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { AppRoutingModule } from './app-routing.module';
import { LoginComponent } from './login/login.component';
import { AuthGuard } from './auth.guard';
import { AuthenticationService } from './services/authentication/authentication-service.service';
import { DashbordComponent } from './dashbord/dashbord.component';
import { FooterComponent } from './footer/footer.component';
import { HeaderComponent } from './header/header.component';
import { SupportInterneComponent } from './support-interne/support-interne.component';
import { SuiviCommandeComponent } from './suivi-commande/suivi-commande.component';
import { SuiviCommandeDetailComponent } from './suivi-commande-detail/suivi-commande-detail.component';
import { HomeComponent } from './home/home.component';

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    DashbordComponent,
    FooterComponent,
    HeaderComponent,
    SupportInterneComponent,
    SuiviCommandeComponent,
    SuiviCommandeDetailComponent,
    HomeComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    ReactiveFormsModule,
    FormsModule
  ],
  providers: [
    AuthenticationService,
    AuthGuard
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
