const _status_ = [
    {id: -99, label: "", value: -99, bg_color: "", color: ""},
    {id: 1, label: "Brouillon", value: 0, bg_color: "#00AAFF", color: "#ffffff"},
    {id: 2, label: "Validé", value: 1, bg_color: "7DFF7D", color: "#ffffff"},
    {id: 3, label: "En cours", value: 2, bg_color: "FF7D00", color: "#ffffff"},
    // {id: 4, label: "En livraison", value: "", bg_color: "00FF00", color: "#ffffff"},
    {id: 5, label: "Annulé", value: -1, bg_color: "FF0000", color: "#ffffff"},
];

const _status_v2 = [
    {id: -99, label: "", value: -99, bg_color: "", color: ""},
    {id: 0, label: "Réception Partielle", value: 0, bg_color: "#00AAFF", color: "#ffffff"},
    {id: 1, label: "Réception Complete", value: 1, bg_color: "7DFF7D", color: "#ffffff"},
    {id: 2, label: "Commande Non Planifiée", value: 2, bg_color: "FF7D00", color: "#ffffff"},
    {id: 3, label: "Commande Planifiée", value: 3, bg_color: "00FF00", color: "#ffffff"},
    {id: 4, label: "Commande Livrée (Sans Réserves)", value: 4, bg_color: "FF0000", color: "#ffffff"},
    {id: 5, label: "Commande Livrée (Avec Réserves)", value: 5, bg_color: "FF7D00", color: "#ffffff"},
    {id: 6, label: "Annulé", value: 6, bg_color: "00FF00", color: "#ffffff"},
];

export class Status {

    constructor() { }

    getLabelById(value){
        let res = "NaN";
        for(let i = 0; i < _status_.length; i++){
            if(_status_[i].id == value){
                res = _status_[i].label;
                break;
            }
        }
        return res;
    }

    getStatus(){
        return _status_;
    }

    getLabelById_v2(value){
        let res = "NaN";
        for(let i = 0; i < _status_v2.length; i++){
            if(_status_v2[i].id == value){
                res = _status_v2[i].label;
                break;
            }
        }
        return res;
    }

    getStatus_v2(){
        return _status_v2;
    }
  
}