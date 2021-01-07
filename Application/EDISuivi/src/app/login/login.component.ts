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
      window.location.href="#/home/dashbord";
    }
    
  }

  
  async tryLogin(value){
    const connectBtn = document.getElementById("btn-one");
    connectBtn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Loading...';
    connectBtn.classList.add('disabled');

    if(value.user == "" || value.password == ""){
      connectBtn.innerHTML = 'Se Connecter';
      connectBtn.classList.remove('disabled');
      return;
    }

    const res: any = await new Promise(async (resolved) => {
      await this.authenticationService.doLogin(value).subscribe(async (data)=>{
        await resolved(data);
      });
    });
    // console.log("tryLogin() => res : ", res);

    // const res: any = {
    //   "success": {
    //     "ref_user_EDISuivi": "USER-0000003",
    //     "identifiant_EDISuivi": "JL",
    //     "last_connexion": "2020-12-22 11:30:02",
    //     "user_type_EDISuivi": "1",
    //     "nom_entreprise_EDISuivi": "@JL",
    //     "nb_commandes": null,
    //     "socid": "2254",
    //     "nom_entreprise": "Développeur @JL",
    //     "code_client": "CU1909-0018",
    //     "code_fournisseur": null,
    //     "token_EDISuivi": "3-8-13-12-7-8-24-8"
    //   }
    // }

    if(res.error != null){
      alert("Error !!");
      connectBtn.innerHTML = 'Se Connecter';
      connectBtn.classList.remove('disabled');
      return;
    }

    this.authenticationService.createUserLocalToken(res);

    window.location.href="#/home/dashbord";
  }

}
