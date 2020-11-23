const _status_ = [
    {id: 1, label: "Brouillon", bg_color: "#00AAFF", color: "#ffffff"},
    {id: 2, label: "Validé", bg_color: "7DFF7D", color: "#ffffff"},
    {id: 3, label: "En cours", bg_color: "FF7D00", color: "#ffffff"},
    {id: 4, label: "En livraison", bg_color: "00FF00", color: "#ffffff"},
    {id: 5, label: "Annulé", bg_color: "FF0000", color: "#ffffff"},
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
  
}