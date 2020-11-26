// This file can be replaced during build by using the `fileReplacements` array.
// `ng build --prod` replaces `environment.ts` with `environment.prod.ts`.
// The list of file replacements can be found in `angular.json`.

const cors_http = "https://cors-anywhere.herokuapp.com/";

export const environment = {
  production: false,
  api: {
    service: `${cors_http}http://82.253.71.109/prod/bdc_v11_04/api/index.php`,
    support: `${cors_http}https://bdc.bdcloud.fr/api/index.php`
  }
};

/*
 * For easier debugging in development mode, you can import the following file
 * to ignore zone related error stack frames such as `zone.run`, `zoneDelegate.invokeTask`.
 *
 * This import should be commented out in production mode because it will have a negative impact
 * on performance if an error is thrown.
 */
// import 'zone.js/dist/zone-error';  // Included with Angular CLI.
