const cors_http = "https://cors-anywhere.herokuapp.com/";

export const environment = {
  production: true,
  api: {
    service: `${cors_http}https://ctm.bdcloud.fr/api/index.php`,
    support: `${cors_http}https://bdc.bdcloud.fr/api/index.php`
  }
};
