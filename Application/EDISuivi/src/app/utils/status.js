const _status_ = [
    {id: -99, label: "", value: -99, bg_color: "", color: ""},
    {id: 1, label: "Brouillon", value: 0, bg_color: "#00AAFF", color: "#ffffff"},
    {id: 2, label: "Validé", value: 1, bg_color: "7DFF7D", color: "#ffffff"},
    {id: 3, label: "En cours", value: 2, bg_color: "FF7D00", color: "#ffffff"},
    // {id: 4, label: "En livraison", value: "", bg_color: "00FF00", color: "#ffffff"},
    {id: 5, label: "Annulé", value: -1, bg_color: "FF0000", color: "#ffffff"},
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